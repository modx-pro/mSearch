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
			,'plPrefix' => 'mse.'
			,'cut_before' => 50
			,'cut_after' => 250
			,'morphy_lang' => $this->modx->getOption('msearch.lang')
			,'morphy_storage' => 'mem'
			,'disablePhpMorphy' => false
		),$config);

		if (isset($this->config['sortFilters'])) {$this->config['sortFilters'] = explode(',', $this->config['sortFilters']);}

		$this->modx->addPackage('msearch',$this->config['modelPath'], $this->modx->config['table_prefix'].'mse_');
		$this->modx->lexicon->load('msearch:default');

		if (!file_exists($this->config['morphyPath'].'dicts/common_aut.'.strtolower($this->config['morphy_lang']).'.bin')) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, $this->modx->lexicon('mse.err_no_morphy_dicts', array('morphy_path' => $corePath.'phpmorphy/dicts/')));
			$this->config['disablePhpMorphy'] = true;
		}

		if ((bool) $this->config['disablePhpMorphy'] != true) {
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
	}


	/**
	 * Initializes mSearch into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
	 */
	public function initialize($ctx = 'web') {
		/*
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
			break;
		}
		*/
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
		if ((bool) $this->config['disablePhpMorphy'] != true) {
			$base_form = $this->phpMorphy->getBaseForm($bulk_words);
		}
		else {
			$base_form = array();
		}
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
		if ((bool) $this->config['disablePhpMorphy'] != true) {
			$tmp = $this->phpMorphy->getAllForms($bulk_words);
		}
		else {
			$tmp = $bulk_words;
		}
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
		$query = $this->stripTags($query);

		$where = '';
		if (!empty($this->config['where']) && $tmp = $this->modx->fromJSON($this->config['where'])) {
			if (is_array($tmp)) {
				$tmp2 = $this->modx->newQuery('modResource', $tmp);
				$tmp2->select('id');
				$tmp2->prepare();
				$tmp = $tmp2->toSQL();
				$where .= 'AND' . substr($tmp, strpos($tmp, 'WHERE') + 5);
			}
		}
		$context = !empty($this->config['context']) ? $this->config['context'] : $this->modx->resource->get('context_key');

		if (!empty($_REQUEST[$this->config['parentsVar']])) {
			$parents = $_REQUEST[$this->config['parentsVar']];
			$this->modx->setPlaceholder($this->config['plPrefix'].'parents', $parents);
		}

		$add_query = '';
		if (empty($this->config['showHidden'])) {$add_query .= ' AND `hidemenu` != 1';}
		if (empty($this->config['showUnpublished'])) {$add_query .= ' AND `published` != 0';}
		if (!empty($this->config['templates'])) {$add_query .= " AND `template` IN ({$this->config['templates']})";}
		if (!empty($this->config['resources'])) {$add_query .= " AND `rid` IN ({$this->config['resources']})";}
		if (!empty($parents)) {
			$tmp = explode(',',$parents);
			$arr = $tmp;
			foreach ($tmp as $v) {
				$arr = array_merge($arr, $this->modx->getChildIds($v, 10, array('context' => $context)));
			}
			$ids = implode(',', $arr);
			$add_query .= " AND `rid` IN ($ids)";
		}

		$query_string = $this->getAllForms($query);
		$db_index = $this->modx->getTableName('ModResIndex');
		$db_res = $this->modx->getTableName('modResource');

		$sql = "SELECT COUNT(`rid`) as `id` FROM $db_index
			LEFT JOIN $db_res `modResource` ON $db_index.`rid` = `modResource`.`id`
			WHERE (MATCH (`resource`,`index`) AGAINST ('$query_string') OR `resource` LIKE '%$query%')
			AND (`modResource`.`searchable` = 1 $add_query) {$where}";

		$total = 0;
		$q = new xPDOCriteria($this->modx, $sql);
		if ($q->prepare() && $q->stmt->execute()){
			if (!$total = $q->stmt->fetchColumn()) {
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

		$sql = "SELECT `rid`,`resource`, MATCH(`resource`,`index`) AGAINST ('>\"$query\" <($query_string)' IN BOOLEAN MODE) as `rel`
			FROM $db_index
			LEFT JOIN $db_res `modResource` ON $db_index.`rid` = `modResource`.`id`
			WHERE (MATCH (`resource`,`index`) AGAINST ('>\"$query\" <($query_string)' IN BOOLEAN MODE) OR `resource` LIKE '%$query%')
			AND (`modResource`.`searchable` = 1 $add_query) $where
			ORDER BY `rel` DESC";
		if (!empty($this->config['limit'])) {$sql .= " LIMIT {$this->config['offset']},{$this->config['limit']}";}
		$q = new xPDOCriteria($this->modx, $sql);
		if ($q->prepare() && $q->stmt->execute()) {
			$result = array(
				'total' => $total
				,'sql' => $sql
				,'time' => $this->get_execution_time()
				,'result' => $q->stmt->fetchAll(PDO::FETCH_ASSOC)
			);
			return $result;
		}
		else {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'Error on execution search query: ' . $sql);
		}
	}


	/*
	 * Gets filter parameters for specified resources
	 * */
	function getFilterParams($resources) {
		if ($params = $this->modx->cacheManager->get('msearch/fltr_' . md5($resources))) {
			return $params;
		}

		$ids = array_map('trim', explode(',', $resources));
		$extra = array('tv' => array(),'ms' => array());

		$tv_params = array();
		if (!empty($this->config['includeTVList'])) {
			$q = $this->modx->newQuery('modTemplateVar');

			if (!empty($this->config['includeTVList'])) {
				$inTVs = explode(',', $this->config['includeTVList']);
				if (count($inTVs)) {
					foreach ($inTVs as $k => $v) {
						if (strpos($v, ':') !== false) {
							$tmp = explode(':', $v);
							$extra['tv'][trim($tmp[0])] = trim($tmp[1]);
							unset($inTVs[$k]);
						}
					}
					$q->andCondition(array('name:IN' => $inTVs));
				}
			}
			if (!empty($this->config['excludeTVList'])) {
				$exTVs = explode(',', $this->config['excludeTVList']);
				if (count($exTVs)) {
					$q->andCondition(array('name:NOT IN' => $exTVs));
				}
			}

			$q->select('id,name,caption,rank,type,description');
			$q->sortby('rank','ASC');

			if ($q->prepare() && $q->stmt->execute()) {
				$tvs = array();
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$tvs[$row['id']] = $row;
				}
			}

			$q = $this->modx->newQuery('modTemplateVarResource', array('contentid:IN' => $ids));
			$q->select('tmplvarid,contentid,value');

			$tvIds = array_keys($tvs);
			if (is_array($tvIds) && !empty($tvIds)) {
				$q->andCondition(array('tmplvarid:IN' => $tvIds));
			}

			if ($q->prepare() && $q->stmt->execute()) {
				$tv_tmp = array();
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					if (!array_key_exists($row['tmplvarid'], $tv_tmp)) {
						$tv_tmp[$row['tmplvarid']][$row['value']][] = $row['contentid'];
					}
					else {
						$tv_tmp[$row['tmplvarid']][$row['value']][] = $row['contentid'];
					}
				}
				foreach ($tvs as $k => $v) {
					$tv_params['tv_' . $v['name']] = array(
						'name' => trim($tvs[$k]['caption'])
						,'type' => $tvs[$k]['type']
						,'values' => $tv_tmp[$k]
					);
				}
			}
		}
		$ms_params = array();
		if (!empty($this->config['includeMS'])) {
			$this->modx->lexicon->load('minishop2:product');

			$q = $this->modx->newQuery('msProductData', array('id:IN' => $ids));
			if (!empty($this->config['includeMSList'])) {
				$inMS = array_map('trim', explode(',', $this->config['includeMSList']));
				foreach ($inMS as $k => $v) {
					if (strpos($v, ':') !== false) {
						$tmp = explode(':', $v);
						$extra['ms'][trim($tmp[0])] = trim($tmp[1]);
						unset($inMS[$k]);
					}
				}
				$q->select('id,' . implode(',', $inMS));
			}
			else {
				$q->select('id,price');
			}

			$field_meta = $this->modx->getFieldMeta('msProductData');
			$meta = array();
			foreach ($field_meta as $k => $v) {
				$meta[$k] = $v['phptype'];
			}


			if ($q->prepare() && $q->stmt->execute()) {
				while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					foreach ($row as $k => $v) {
						if ($v == '' || $k == 'id') {continue;}
						else if ($k == 'price' || $k == 'old_price') {$type = 'number'; $v = round($v,2);}
						else if ($k == 'weight') {$type = 'number'; $v = round($v,3);}
						else {
							switch ($meta[$k]) {
								case 'boolean':
									$v = !empty($v) ? $this->modx->lexicon('yes') : $this->modx->lexicon('no');
									$type = 'text';
								break;
								case 'json':
									$v = json_decode($v, true);
									$type = 'array';
								break;
								default: $type = 'text';
							}
						}

						if (!isset($ms_params['ms_' . $k])) {
							$ms_params['ms_' . $k] = array(
								'name' => $this->modx->lexicon('ms2_product_' . $k)
								,'type' => $type
							);

							if ($type == 'array') {
								$ms_params['ms_' . $k]['values'] = array();
								foreach ($v as $v2) {
									if (!empty($v2)) {
										$ms_params['ms_' . $k]['values'][$v2][] = $row['id'];
									}
								}
							}
							else {
								$ms_params['ms_' . $k]['values'] = array(
									$v => array($row['id'])
								);
							}
						}
						else {
							if ($type == 'array') {
								foreach ($v as $v2) {
									if (!empty($v2)) {
										$ms_params['ms_' . $k]['values'][$v2][] = $row['id'];
									}
								}
							}
							else {
								$ms_params['ms_' . $k]['values'][$v][] = $row['id'];
							}
						}
					}
				}
			}
		}
		$params = array_merge($ms_params, $tv_params);
		// Достаем экстра параметры, то есть те, для которых был указан дополнительный сниппет через двоеточие
		foreach ($extra as $k => $v) {
			foreach ($v as $k2 => $v2) {
				$tmp = $this->modx->runSnippet($v2, array('param' => $k2, 'ids' => $ids));
				/*
					$tmp должен вернуть закодированный в JSON массив типа:
					[name] => Размер
					[type] => custom
					[snipet] => size_search_extra
					[values] => Array(
						[42] => Array (
								[0] => 1198
								,[1] => 2015
								...
				*/
				$tmp = json_decode($tmp, true);
				if (is_array($tmp) && !empty($tmp)) {
					$params[$k.'_'.$k2] = $tmp;
				}
			}
		}

		// Sorting the values
		foreach ($params as $k => $v) {
			if (isset($v['values']) && is_array($v['values'])) {
				ksort($params[$k]['values']);
			}
		}
		// Sorting the filters
		if (!empty($this->config['sortFilters'])) {
			$sortFilters = array_map('trim', explode(',', $this->config['sortFilters']));
			$tmp = array();
			foreach ($sortFilters as $v) {
				if (array_key_exists($v, $params)) {
					$tmp[$v] = $params[$v];
					unset($params[$v]);
				}
			}
			if (!empty($tmp)) {
				$params = array_merge($tmp, $params);
			}
		}

		$this->modx->cacheManager->set('msearch/fltr_' . md5($resources), $params, 1800);
		return $params;
	}


	/*
	 * Return array of resources with parameters for filter
	 * */
	function getResParams($resources) {
		if ($res = $this->modx->cacheManager->get('msearch/res_' . md5($resources))) {
			return $res;
		}

		$params = $this->getFilterParams($resources);

		$res = array();
		foreach ($params as $k => $v) {
			if (empty($v['values'])) {continue;}
			foreach ($v['values'] as $k2 => $v2) {
				foreach ($v2 as $v3) {
					if ($v['type'] == 'number') {
						if (!array_key_exists($v3, $res)) {
							$res[$v3] = array();
							$res[$v3][$k] = $k2;
						}
						else {
							$res[$v3][$k] = $k2;
						}
					}
					else {
						if (!array_key_exists($v3, $res)) {
							$res[$v3] = array();
							$res[$v3][$k] = array($k2);
						}
						else {
							$res[$v3][$k][] = $k2;
						}
					}
				}
			}
		}

		$this->modx->cacheManager->set('msearch/res_' . md5($resources), $res, 1800);
		return $res;
	}


	/*
	 * Suggestions of search results for each parameter
	 * */
	function getActiveParams(array $filter, $resources) {
		if (!empty($this->config['fastMode'])) {
			return array();
		}

		if ($res = $this->modx->cacheManager->get('msearch/act_' . md5(json_encode($filter).$resources))) {
			return $res;
		}

		$default_params= $this->getFilterParams($resources);
		$params = array();
		foreach ($default_params as $k => $v) {
			if (is_array($v['values']) && !empty($v['values'])) {
				$params[$k] = array_keys($v['values']);
			}
		}

		if (empty($params)) {return array();}

		$res = array();
		foreach ($params as $k => $v) {
			if ($default_params[$k]['type'] == 'number') {continue;}
			foreach ($v as $v2) {
				$tmp = $filter;

				if (!array_key_exists($k, $filter)) {
					$tmp[$k] = array($v2);
					$res[$k][$v2] = count($this->getResIds($tmp, $resources));
				}
				else {
					if (!in_array($v2, $filter[$k])) {
						$tmp2[$k] = array($v2);
						$alone = count($this->getResIds($tmp2, $resources));

						if ($alone == 0) {
							$res[$k][$v2] = 0;
						}
						else {
							$tmp[$k][] = $v2;
							$total = count($this->getResIds($tmp, $resources));
							$current = count($this->getResIds($filter, $resources));

							if ($total > $current) {
								$res[$k][$v2] = $total;
							}
							else {
								$res[$k][$v2] = 0;
							}
						}
					}
					else {
						$res[$k][$v2] = count($this->getResIds($filter, $resources));
					}
				}

			}
		}
		$this->modx->cacheManager->set('msearch/act_' . md5(json_encode($filter).$resources), $res, 1800);
		return $res;
	}


	/*
	 * Filter goods by received parameters
	 * */
	function getResIds(array $params, $resources) {
		$default_params = $this->getFilterParams($resources);
		$ids = $this->getResParams($resources);

		$in = $out = array();
		foreach ($params as $key => $value) {

			if (strpos($key, 'ms_') === false && strpos($key, 'tv_') === false) {continue;}

			$type = $default_params[$key]['type'];
			foreach ($ids as $id => $params) {
				if (!array_key_exists($key, $params)) {$out[] = $id;continue;}
				if ($type == 'number' && count($value) == 2) {
					if ($params[$key] >= $value[0] && $params[$key] <= $value[1]) {$in[] = $id;}
					else {$out[] = $id;}
				}
				else {
					/*
					foreach ($params[$key] as $value2) {
						if (in_array($value2, $value)) {$in[] = $id;}
						else {$out[] = $id;}
					}*/

					$exists = 0;
					foreach ($params[$key] as $value2) {
						if (in_array($value2, $value)) {$exists += 1;}
					}
					if ($exists == 0) {$out[] = $id;}
					else {$in[] = $id;}

				}
			}
		}
		//$in = array_unique($in);
		//$out = array_unique($out);

		if (!empty($in) && empty($out)) {return array_unique($in);}
		else if (!empty($out) && empty($in)) {return array();}
		else if (!empty($out) && !empty($in)) {
			$out = array_flip($out);
			foreach ($in as $key => $value) {
				if (isset($out[$value])) {
					unset($in[$key]);
				}
			}
			return array_unique($in);
		}
		else {
			return explode(',',$resources);
		}
	}


}
