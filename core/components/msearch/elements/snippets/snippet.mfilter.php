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

	$filter = $modx->mSearch->getActiveParams($_POST, $ids);
	$ids = $modx->mSearch->getResIds($_POST, $ids);
	
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
		$rows = $modx->getChunk($tplOuter, array_merge($params, $arr));
	}

	echo json_encode(array(
		'rows' => $rows
		,'filter' => $filter
	));
	
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
			$rows .= $modx->getChunk($tplParamNumber, array('paramname' => $k, 'min' => min($tmp), 'max' => max($tmp), 'idx' => $idx));
			$idx++;
		}
		else {
			if (count($v['values']) < 2) {continue;}
			ksort($v['values']);
			foreach ($v['values'] as $k2 => $v2) {
				$rows .= $modx->getChunk($tplParamCheckbox, array('paramname' => $k, 'value' => $k2, 'num' => count($v2), 'idx' => $idx));
				$idx++;
			}
		}
		$v['paramname'] = $k;
		$v['rows'] = $rows;
		$result .= $modx->getChunk($tplParamOuter, $v); 
	}

	return $result;
}
