<?php
if (!empty($indexer)) {
	return require $modx->getOption('core_path').'components/msearch/elements/snippets/indexer.php';
}

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['autocomplete'] != false) {$ajax = true;} else {$ajax = false;}

// Подключаем класс mSearch
$mSearch = $modx->getService('msearch','mSearch',$modx->getOption('core_path').'components/msearch/model/msearch/',$scriptProperties);
if (!($mSearch instanceof mSearch)) return '';

//reconfigurate mSearch
if($reconfig==1)
    $mSearch->reconfigurate($scriptProperties);
    
// Обрабатываем поисковый запрос
if (isset($_REQUEST[$queryVar])) {
	$query = trim(strip_tags($_REQUEST[$queryVar]));
	$query = preg_replace('/[^_-а-яёa-z0-9\s\.]+/iu','',$query);
}
else {$query = 0;}

if (empty($query) && isset($_REQUEST[$queryVar])) {
	$modx->setPlaceholder($plPrefix.'error', $modx->lexicon('mse.err_no_query'));
	if ($ajax) {die('[]');} else {return;}
}
else if (strlen($query) < $minQuery && !empty($query)) {
	$modx->setPlaceholder($plPrefix.'error', $modx->lexicon('mse.err_min_query'));
	if ($ajax) {die('[]');} else {return;}
}
else if (empty($query)) {
	$modx->setPlaceholder($plPrefix.'error', ' ');
	if ($ajax) {die('[]');} else {return;}
}
else {
	$modx->setPlaceholder($plPrefix.'query', $query);
}

// Поиск
$results = $mSearch->Search($query);
// Выставляем служебные плейсхолдеры
$modx->setPlaceholder($plPrefix.'query_time',$results['time']);
$modx->setPlaceholder($plPrefix.'query_string',$results['sql']);

if ($results['total'] == 0) {
	$modx->setPlaceholder($totalVar, 0);
	$modx->setPlaceholder($plPrefix.'error', $modx->lexicon('mse.err_no_results'));
	if ($ajax) {die('[]');} else {return;}
}

$modx->setPlaceholder($totalVar, $results['total']);
$res = $results['result'];

if ($returnIds == 1) {
	$ids = array();
	foreach ($res as $v) {
		$ids[] = $v['rid'];
	}
	
	return implode(',', $ids);
}
else if (isset($_REQUEST['autocomplete']) && $ajax) {
	$arr = $exists = array();
	foreach ($res as $v) {
		if ($tmp = $modx->getObject('modResource', $v['rid'])) {
			$tmp2 = $tmp->toArray();
			if (!in_array($tmp2['pagetitle'], $exists)) {
				$arr[] = array(
					'id' => $tmp2['id']
					,'url' => $modx->makeUrl($tmp2['id'], '', '', 'full')
					,'value' => $tmp2['pagetitle']
					,'label' => $modx->getChunk($tpl, $tmp2)
				);
				$exists[] = $tmp2['pagetitle'];
			}
		}
	}
	array_multisort($arr);
	echo json_encode($arr);
	die;
}
else {
	$class = !empty($includeMS) ? 'msProduct' : 'modResource';
	$i = $offset;
	$result = array();
	foreach ($res as $v) {
		if ($tmp = $modx->getObject($class, $v['rid'])) {
			$arr = $tmp->toArray();
			$arr['num'] = $i++;
			$arr['intro'] = $mSearch->Highlight($v['resource'], $query);
			if (!empty($includeTVs) && !empty($includeTVList)) {
				$includeTVList = explode(',',$includeTVList);
				foreach ($includeTVList as $v2) {
					$arr[$tvPrefix.$v2] = $tmp->getTVValue($v2);
				}
			}
			$result[] = $modx->getChunk($tpl, $arr);
		}
	}
	$modx->setPlaceholder($plPrefix.'render_time',$mSearch->get_execution_time() - $modx->getPlaceholder($plPrefix.'query_time'));

	if ($i == 0) {
		$modx->setPlaceholder($plPrefix.'error', $modx->lexicon('mse.err_no_results'));
		return;
	}
	return implode($outputSeparator, $result);
}
