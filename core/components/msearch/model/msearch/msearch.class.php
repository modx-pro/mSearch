<?php
/**
 * mSearch
 *
 * Copyright 2010 by Shaun McCormick <shaun+msearch@modx.com>
 *
 * mSearch is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * mSearch is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * mSearch; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package msearch
 */
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
			//$this->modx->log(modX::LOG_LEVEL_ERROR, 'mSearch: '.$this->modx->lexicon('mse.err_no_morphy_dicts', array('morphy_path' => $corePath.'phpmorphy/dicts/')));
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
	
	
	function get_execution_time() {
		static $microtime_start = null;
		if($microtime_start === null)
		{
			$microtime_start = microtime(true);
			return 0.0;
		}
		return microtime(true) - $microtime_start;
	}
	

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
}
