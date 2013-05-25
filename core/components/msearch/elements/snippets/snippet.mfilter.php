<?php
/* @var mSearch $mSearch */
$mSearch = $modx->getService('msearch','mSearch',$modx->getOption('core_path').'components/msearch/model/msearch/', $scriptProperties);
$mSearch->config = array_merge($mSearch->config, $scriptProperties, array('returnIds' => 1, 'limit' => 0));

$ids = !isset($resources) || empty($resources) ? $modx->runSnippet('mSearch', $mSearch->config) : trim($resources);

// Filtering resources via Ajax
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['action'] == 'filter') {
	$filter = $mSearch->getActiveParams($_POST, $ids);
	$ids = $mSearch->getResIds($_POST, $ids);

	if (empty($ids) || (isset($ids[0]) && empty($ids[0]))) {
		$tmp = $modx->getPlaceholder($mSearch->config['plPrefix'].'error');
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
	if (empty($snippet)) {
		$snippet = !empty($includeMS) &&  $includeMS != 'false' ? 'msProducts' : 'getResources';
	}

	$params = array(
		'parents' => 0
		,'element' => $snippet
		,'resources' => implode(',',$ids)
		,'limit' => !empty($_POST['limit']) ? (int) $_POST['limit'] : $scriptProperties['limit']
		,'offset' => !empty($_POST['offset']) ? (int) $_POST['offset'] : 0
		,'page' => !empty($_POST['page']) ? $_POST['page'] : 1
		,'sortby' => !empty($_POST['sortby']) ? $_POST['sortby'] : 'pagetitle'
		,'sortdir' => !empty($_POST['sortdir']) ? $_POST['sortdir'] : 'ASC'
		,'showLog' => 0
	);
	// Merging received properties with required
	$params = array_merge($scriptProperties, $params);
	// Sort by and dir
	if (!empty($_POST['sort'])) {
		$tmp = array_map('trim', explode(',', $_POST['sort']));
		if (strpos($tmp[0], 'ms_') === 0) {
			$params['sortby'] = 'Data.'.substr($tmp[0], 3);
			if (!empty($tmp[1])) {
				$params['sortdir'] = $tmp[1];
			}
		}
		else if (strpos($tmp[0], 'tv_') === 0) {
			$params['sortby'] = 'TV'.substr($tmp[0], 3).'.value';
			if (!empty($tmp[1])) {
				$params['sortdir'] = $tmp[1];
			}
		}
		else {
			$params['sortby'] = $tmp[0];
			if (!empty($tmp[1])) {
				$params['sortdir'] = $tmp[1];
			}
		}
	}

	// Running getPage
	if (empty($paginator)) {$paginator = 'getPage';}
	$rows = $modx->runSnippet($paginator, $params);
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

	$params = $mSearch->getFilterParams($ids);
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
				$num = !empty($mSearch->config['fastMode']) ? '' : count($v2);
				$rows .= $modx->getChunk($tplParamCheckbox, array('paramname' => $k, 'value' => $k2, 'num' => $num, 'idx' => $idx));
				$idx++;
			}
		}
		$v['paramname'] = $k;
		$v['rows'] = $rows;
		$result .= $modx->getChunk($tplParamOuter, $v);
	}

	return $result;
}