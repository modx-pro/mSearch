<?php
if (isset($modx->mSearch->config)) {
	$config = $modx->mSearch->config = array_merge($modx->mSearch->config, $scriptProperties, array('returnIds' => 1, 'limit' => 0));
}
else {
	$config = array_merge($scriptProperties, array('returnIds' => 1, 'limit' => 0));
}

if (!isset($resources) || empty($resources)) {
	$ids = $modx->runSnippet('mSearch', $config);
}
else {
	$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('core_path').'components/msearch/model/msearch/',$config);
	if (!($modx->mSearch instanceof mSearch)) return '';
	$ids = $resources;
}
$ids = trim($ids);

// Filtering resources via Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'filter') {

	if (!empty($ids)) {
		$filter = $modx->mSearch->getActiveParams($_POST, $ids);
		$ids = $modx->mSearch->getResIds($_POST, $ids);
	}

	if (empty($ids)) {
		$tmp = $modx->getPlaceholder($modx->mSearch->config['plPrefix'].'error');
		if (empty($tmp)) {$tmp = $modx->lexicon('mse.err_no_results');}
		$rows = $modx->newObject('modChunk', array('snippet' => $tmp))->process();
		echo json_encode(array(
			'rows' => $rows
			,'filter' => '[]'
			,'total' => 0
		));
		exit;
	}

	// Set parameters for getPage
	$params = array(
		'parents' => '-1'
		,'element' => $includeMS ? 'msGetResources' : 'getResources'
		,'resources' => implode(',',$ids)
		,'limit' => !empty($_POST['limit']) ? (int) $_POST['limit'] : $scriptProperties['limit']
		,'offset' => !empty($_POST['offset']) ? (int) $_POST['offset'] : 0
		,'page' => !empty($_POST['page']) ? $_POST['page'] : 1
		,'sortby' => !empty($_POST['sortby']) ? $_POST['sortby'] : 'pagetitle'
		,'sortdir' => !empty($_POST['sortdir']) ? $_POST['sortdir'] : 'ASC'
		//,'debug' => 1
	);
	// Merging received properties with required
	$params = array_merge($scriptProperties, $params);

	// Sort by and dir
	if (isset($_POST['sort']) && !empty($_POST['sort'])) {
		$tmp = explode(',', $_POST['sort']);
		if (preg_match('/^ms_/', $tmp[0])) {
			$params['sortbyMS'] = preg_replace('/^ms_/', '', $tmp[0]);
			if (!empty($tmp[1])) {
				$params['sortdir'] = $tmp[1];
			}
		}
		else if (preg_match('/^tv_/', $tmp[0])) {
			$params['sortbyTV'] = preg_replace('/^tv_/', '', $tmp[0]);
			if (!empty($tmp[1])) {
				$params['sortdirTV'] = $tmp[1];
			}
		}
	}

	// Running getPage
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

	// Parse all MODX tags in results
	$maxIterations= (integer) $modx->getOption('parser_max_iterations', null, 10);
	$modx->getParser()->processElementTags('', $rows, false, false, '[[', ']]', array(), $maxIterations);
	$modx->getParser()->processElementTags('', $rows, true, true, '[[', ']]', array(), $maxIterations);

	echo json_encode(array(
		'rows' => $rows
		,'filter' => $filter
		,'total' => count($ids)
	));
	exit;
}
// Generating filters
else {
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