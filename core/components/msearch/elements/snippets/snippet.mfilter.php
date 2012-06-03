<?php

if (isset($modx->mSearch->config)) {
	$config = $modx->mSearch->config = array_merge($modx->mSearch->config, $scriptProperties, array('returnIds' => 1, 'limit' => 0));
}
else {
	$config = array_merge($scriptProperties, array('returnIds' => 1, 'limit' => 0));
}

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'filter') {
	$ids = $modx->runSnippet('mSearch', $config);
	if (empty($ids)) {exit($modx->lexicon('mse.err_no_results'));}
	else {$ids = explode(',', $ids);}
	
	$tv_arr = $ms_arr = array();
	foreach ($_POST as $k => $v) {
		// TVs filter
		if (preg_match('/^tv_/', $k)) {
			$k = preg_replace('/^tv_/', '', $k);
			
			if ($tmp = $modx->getObject('modTemplateVar', array('name' => $k))) {
				$tvs[] = $tmp->get('id');
				$type = $tmp->get('type');
				if ($type == 'number' && count($v) == 2) {
					$tv_arr[] = array('tmplvarid' => $tmp->get('id'), 'value:>=' => $v[0], 'value:<=' => $v[1]);
				}
				else {
					$tv_arr[] = array('tmplvarid' => $tmp->get('id'), 'value:IN' => $v);
				}
			}
		}
		// miniShop filter
		else if (preg_match('/^ms_/', $k)) {
			$k = preg_replace('/^ms_/', '', $k);
			
			if (($k == 'price' || $k == 'weight' || $k == 'remains') && count($v) == 2) {
				$ms_arr[] = array("$k:>=" => $v[0], "$k:<=" => $v[1]);
			}
			else {
				$ms_arr[] = array("$k:IN" => $v);
			}
		}
	}

	$tv_ids = array();
	if (!empty($tv_arr)) {
		foreach ($tv_arr as $v) {
			$q = $modx->newQuery('modTemplateVarResource');
			if (empty($tv_ids)) {
				$q->where(array('contentid:IN' => $ids));
			}
			else {
				$q->where(array('contentid:IN' => $tv_ids));
			}
			$q->andCondition($v);
			$q->select('contentid');
			
			if ($q->prepare() && $q->stmt->execute()) {
				$tv_ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
			}
		}
	}

	$ms_ids = array();
	if (!empty($ms_arr)) {
		if (!isset($modx->miniShop) || !is_object($modx->miniShop)) {
			$modx->miniShop = $modx->getService('minishop','miniShop',$modx->getOption('core_path').'components/minishop/model/minishop/',$scriptProperties);
			if (!($modx->miniShop instanceof miniShop)) return '';
		}
		$q = $modx->newQuery('ModGoods', array('gid:IN' => $ids, 'wid' => $_SESSION['minishop']['warehouse']));
		$q->andCondition($ms_arr);
		$q->select('gid');
		if ($q->prepare() && $q->stmt->execute()) {
			$ms_ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN, 0);
		}
	}

	$ids = array_intersect($tv_ids, $ms_ids);
	$limit = !empty($_POST['limit']) ? (int) $_POST['limit'] : $scriptProperties['limit'];
	if ($limit > 200) {$limit = 200;}
	$params = array(
		'parents' => '-1'
		,'element' => isset($includeMS) ? 'msGetResources' : 'getResources'
		,'resources' => implode(',',$ids)
		,'tpl' => !empty($tpl) ? $tpl : 'tpl.msGoods.row'
		,'limit' => $limit
		,'offset' => !empty($_POST['offset']) ? (int) $_POST['offset'] : 0
		,'page' => !empty($_POST['page']) ? $_POST['page'] : 1

		,'sortby' => !empty($_POST['sortby']) ? $_POST['sortby'] : 'pagetitle'
		//,'sortbyMS' => !empty($_POST['sortbyMS']) ? $_POST['sortbyMS'] : 'price'
		,'sortdir' => !empty($_POST['sortdir']) ? $_POST['sortdir'] : 'ASC'

		//,'debug' => 1
	);

	if (isset($_POST['sort']) && !empty($_POST['sort'])) {
		$tmp = explode(',', $_POST['sort']);
		if (preg_match('/^ms\./', $tmp[0])) {
			$params['sortbyMS'] = preg_replace('/^ms\./', '', $tmp[0]);
			if (!empty($tmp[1])) {
				$params['sortdir'] = $tmp[1];
			}
		}
		else if (preg_match('/^tv\./', $tmp[0])) {
			$params['sortbyTV'] = preg_replace('/^tv\./', '', $tmp[0]);
			if (!empty($tmp[1])) {
				$params['sortdirTV'] = $tmp[1];
			}
		}
	}

	
	
	$rows = $modx->runSnippet('getPage', $params);
	if (empty($rows)) {$rows = $modx->lexicon('mse.err_no_results');}
	
	if (isset($tplOuter) && !empty($tplOuter)) {
		$arr = array(
			'page.nav' => $modx->getPlaceholder('page.nav')
			,'rows' => $rows
			,'sort' => $_POST['sort']
		);
		
		echo $modx->getChunk($tplOuter, array_merge($params, $arr));
	}
	else {
		echo $rows;
	}
	
	//exit;
}
else {
	$ids = $modx->runSnippet('mSearch', $config);
	if (empty($ids)) {return;}
	
	$params = $modx->mSearch->getFilterParams($ids);
	$result = ''; $idx = 0;
	foreach ($params as $k => $v) {
		$rows = '';
		if ($v['type'] == 'number') {
			$tmp = array_keys($v['values']);
			if (count($tmp) < 2) {continue;}
			$rows .= $modx->getChunk($tplParamNumber, array('name' => $k, 'min' => min($tmp), 'max' => max($tmp), 'idx' => $idx));
			$idx++;
		}
		else {
			if (count($v['values']) < 2) {continue;}
			ksort($v['values']);
			foreach ($v['values'] as $k2 => $v2) {
				$rows .= $modx->getChunk($tplParamCheckbox, array('name' => $k, 'value' => $k2, 'num' => $v2, 'idx' => $idx));
				$idx++;
			}
		}
		$v['rows'] = $rows;
		$result .= $modx->getChunk($tplParamOuter, $v); 
	}

	return $result;
}
