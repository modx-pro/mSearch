<?php
/**
 * The base class for mSearch.
 *
 * @package msearch
 */
class mSearch {
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('msearch.core_path',$config,$this->modx->getOption('core_path').'components/msearch/');
		$assetsUrl = $this->modx->getOption('msearch.assets_url',$config,$this->modx->getOption('assets_url').'components/msearch/');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'
			,'connectorUrl' => $connectorUrl
			,'corePath' => $corePath
			,'morphyPath' => $corePath.'phpmorphy/'
			,'modelPath' => $corePath.'model/'
			,'chunksPath' => $corePath.'elements/chunks/'
			,'chunkSuffix' => '.chunk.tpl'
			,'snippetsPath' => $corePath.'elements/snippets/'
			,'processorsPath' => $corePath.'processors/'
			
			,'cut_before' => 50
			,'cut_after' => 250
			,'morphy_lang' => $this->modx->getOption('msearch.lang')
			,'morphy_storage' => 'mem'
		),$config);

		$this->modx->addPackage('msearch',$this->config['modelPath'], $this->modx->config['table_prefix'].'mse_');
		$this->modx->lexicon->load('msearch:default');

		if (!file_exists($this->config['morphyPath'].'dicts/common_aut.'.strtolower($this->config['morphy_lang']).'.bin')) {
			die($this->modx->lexicon('mse.err_no_morphy_dicts', array('morphy_path' => $corePath.'phpmorphy/dicts/')));
		}
		
		require_once($this->config['morphyPath'].'src/common.php');
		$dict_bundle = new phpMorphy_FilesBundle($this->config['morphyPath'].'dicts/', $this->config['morphy_lang']);
		
		$this->phpMorphy = new phpMorphy($dict_bundle, array(
			'storage' => $this->config['morphy_storage']
			,'with_gramtab' => false
			,'predict_by_suffix' => true
			,'predict_by_db' => true
		));
		mb_internal_encoding('UTF-8');
	}

    /**
     * Initializes mSearch into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx = 'web') {
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('msearch.request.mSearchControllerRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new mSearchControllerRequest($this);
                return $this->request->handleRequest();
            break;
            case 'connector':
                if (!$this->modx->loadClass('msearch.request.mSearchConnectorRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load connector request handler.';
                }
                $this->request = new mSearchConnectorRequest($this);
                return $this->request->handle();
            break;
            default:
                /* if you wanted to do any generic frontend stuff here.
                 * For example, if you have a lot of snippets but common code
                 * in them all at the beginning, you could put it here and just
                 * call $msearch->initialize($modx->context->get('key'));
                 * which would run this.
                 */
            break;
        }
    }


	/*
	 * Measurement of time
	 * */
	function get_execution_time() {
		static $microtime_start = null;
		if ($microtime_start === null) {
			$microtime_start = microtime(true);
			return 0.0;
		}
		return microtime(true) - $microtime_start;
	}


	/*
	 * Gets base form of the words
	 * */
	function getBaseForms($text) {
		$text = strip_tags($text);
		
		$words = preg_replace('#\[.*\]#isU', '', $text);
		$words = preg_split('#\s|[,.:;!?"\'()]#', $words, -1, PREG_SPLIT_NO_EMPTY);

		$bulk_words = array();
		foreach ($words as $v) {
			if (mb_strlen($v,'UTF-8') > 3)
				$bulk_words[] = mb_strtoupper($v, 'UTF-8');
		}
		
		$base_form = $this->phpMorphy->getBaseForm($bulk_words);
		
		$fullList = array();
		if (is_array($base_form) && count($base_form)) {
			foreach ($base_form as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $v1) {
						if (mb_strlen($v1,'UTF-8') > 3) {
							$fullList[$v1] = 1;
						}
					}
				}
			}
		}
		return implode(' ', array_keys($fullList));
	}


	/*
	 * Gets all morphological forms of the words
	 * */
	function getAllForms($text, $implode = 1) {
		$words = preg_split('#\s|[,.:;!?"\'()]#', $text, -1, PREG_SPLIT_NO_EMPTY);
		$bulk_words = array();
		foreach ($words as $v) {
			if (mb_strlen($v,'UTF-8') > 3) {
				$bulk_words[] = mb_strtoupper($v, 'UTF-8');
			}
		}
		$tmp = $this->phpMorphy->getAllForms($bulk_words);
		
		if ($implode && is_array($tmp)) {
			$str = '';
			foreach ($tmp as $v) {
				if (is_array($v)) {
					$str .= implode(' ', $v).' ';
				}
				else {
					$str .= $v.' ';
				}
			}
			return $str;
		}
		else {
			return $tmp;
		}
	}


	/*
	 * Highlight of the string
	 * */
	function Highlight($text, $query) {
		$arr = array($query);
		$tmp = explode(' ', $this->getAllForms($query));
		if (!empty($tmp)) {$arr = array_merge($arr, $tmp);}
		$text_cut = '';
		
		foreach ($arr as $v) {
			if (empty($v)) {continue;}
			// При первом совпадении - обрезка куска текста
			if (empty($text_cut) && preg_match("/$v/imu", $text, $matches)) {
				$pos = mb_strpos($text, $matches[0], 0, 'UTF-8');
				if ($pos >= 50) {
					$text_cut = '... ';
					$pos -= 50;
				}
				else {
					$pos = 0;
				}
				$text_cut .= mb_substr($text, $pos, 250, 'UTF-8');
				if (mb_strlen($text,'UTF-8') > 250) {$text_cut .= ' ...';}
			}
			// Если текст обрезан - выделяем совпадения
			if (!empty($text_cut)) {
				$text_cut = preg_replace("/$v/imu", "<span class='highlight'>$0</span>", $text_cut);
			}
		}
		return $text_cut;
	}


	/*
	 * Sanitization of the string
	 * */
	function stripTags($html) {
		$search = array(
			'@<script[^>]*?>.*?</script>@si'	// Strip out javascript 
			,'@<style[^>]*?>.*?</style>@siU'	// Strip style tags properly 
			,'@<iframe[^>]*?>.*?</iframe>@siU'	// Strip style tags properly 
			,'@<[\/\!]*?[^<>]*?>@si'			// Strip out HTML tags 
			,'@<![\s\S]*?–[ \t\n\r]*>@'			// Strip multi-line comments including CDATA 
			,'/\s{2,}/'							
		); 

		$text = preg_replace($search, '', html_entity_decode($html)); 

		$pat[0] = "/^\s+/"; 
		$pat[2] = "/\s+\$/"; 
		$rep[0] = ""; 
		$rep[2] = " "; 

		$text = preg_replace($pat, $rep, trim($text)); 
		$text = $this->modx->stripTags($text);

		return $text; 
	}


	/*
	 * Main search method
	 * */
	function Search($query) {
		$this->get_execution_time();
		
		if (!empty($this->config['includeTVList'])) {$includeTVList = explode(',', $includeTVList);} else {$includeTVList = array();}
		if (!empty($this->config['where']) && $tmp = $this->modx->fromJSON($this->config['where'])) {
			if (is_array($tmp)) {
				$tmp2 = $this->modx->newQuery('modResource', $tmp);
				$tmp2->select('id');
				$tmp2->prepare();
				$tmp = $tmp2->toSQL();
				$where = 'AND' . substr($tmp, strpos($tmp, 'WHERE') + 5);
			}
		}
		$context = !empty($this->config['context']) ? $this->config['context'] : $this->modx->resource->context_key;

		if (!empty($_REQUEST[$this->config['parentsVar']])) {
			$parents = $_REQUEST[$this->config['parentsVar']];
			$modx->setPlaceholder($this->config['plPrefix'].'parents', $parents);
		}

		$add_query = '';
		if (empty($this->config['showHidden'])) {$add_query .= ' AND `hidemenu` != 1';}
		if (empty($this->config['showUnpublished'])) {$add_query .= ' AND `published` != 0';}
		if (!empty($this->config['templates'])) {$add_query .= " AND `template` IN ({$this->config['templates']})";}
		if (!empty($this->config['resources'])) {$add_query .= " AND `rid` IN ({$this->config['resources']})";}
		if (!empty($this->config['parents'])) {
			$tmp = explode(',',$this->config['parents']);
			$arr = $tmp;
			foreach ($tmp as $v) {
				$arr = array_merge($arr, $this->modx->getChildIds($v, 10, array('context' => $context)));
			}
			$ids = implode(',', $arr);
			$add_query .= " AND `rid` IN ($ids)";
		}

		// Получаем все возможные формы слов запроса
		$query_string = $this->modx->mSearch->getAllForms($query);

		// Составляем запросы в БД
		$db_index = $this->modx->getTableName('ModResIndex');
		$db_res = $this->modx->getTableName('modResource');
		// Определяем количество результатов
		$sql = "SELECT COUNT(`rid`) as `id` FROM $db_index 
			LEFT JOIN $db_res `modResource` ON $db_index.`rid` = `modResource`.`id`
			WHERE (MATCH (`resource`,`index`) AGAINST ('$query_string') OR `resource` LIKE '%$query%')
			AND (`modResource`.`searchable` = 1 $add_query) $where";

		$q = new xPDOCriteria($this->modx, $sql);
		if ($q->prepare() && $q->stmt->execute()){
			$total = $q->stmt->fetchColumn();
			if ($total == 0) {
				return array(
					'total' => 0
					,'sql' => $sql
					,'time' => $this->get_execution_time()
					,'result' => ''
				);
			}
		}
		else {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'Error on execution search query: ' . $sql);
		}

		// Если их больше 0 - запускаем основной поиск
		$sql = "SELECT `rid`,`resource`, MATCH(`resource`,`index`) AGAINST ('>\"$query\" <($query_string)' IN BOOLEAN MODE) as `rel`
			FROM $db_index 
			LEFT JOIN $db_res `modResource` ON $db_index.`rid` = `modResource`.`id`
			WHERE (MATCH (`resource`,`index`) AGAINST ('>\"$query\" <($query_string)' IN BOOLEAN MODE) OR `resource` LIKE '%$query%')
			AND (`modResource`.`searchable` = 1 $add_query) $where
			ORDER BY `rel` DESC";
		if (!empty($this->config['limit'])) {$sql .= " LIMIT {$this->config['offset']},{$this->config['limit']}";}
		$q = new xPDOCriteria($this->modx, $sql);
		if ($q->prepare() && $q->stmt->execute()) {
			$result = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
			return array(
				'total' => $total
				,'sql' => $sql
				,'time' => $this->get_execution_time()
				,'result' => $result
			);
		}
		else {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'Error on execution search query: ' . $sql);
		}





	}
}
