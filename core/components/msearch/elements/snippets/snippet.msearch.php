<?php
if (!empty($indexer)) {
	return require $modx->getOption('core_path').'components/msearch/elements/snippets/indexer.php';
}

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_REQUEST['autocomplete'] != false) {$ajax = true;} else {$ajax = false;}

// Подключаем класс mSearch
if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
	$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('core_path').'components/msearch/model/msearch/',$scriptProperties);
	if (!($modx->mSearch instanceof mSearch)) return '';
}

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
$results = $modx->mSearch->Search($query);
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
	if ($includeMS != 0) {
		if (!isset($modx->miniShop) || !is_object($modx->miniShop)) {
			$modx->miniShop = $modx->getService('minishop','miniShop',$modx->getOption('core_path').'components/minishop/model/minishop/',$scriptProperties);
			if (!($modx->miniShop instanceof miniShop)) return '';
		}
	}
	$i = $offset;
	$result = array();
	foreach ($res as $v) {
		if ($tmp = $modx->getObject('modResource', $v['rid'])) {
			$arr = $tmp->toArray();
			$i++;
			$arr['num'] = $i;
			$arr['intro'] = $modx->mSearch->Highlight($v['resource'], $query);
			if ($includeTVs && !empty($includeTVList)) {
				$includeTVList = explode(',',$includeTVList);
				foreach ($includeTVList as $k => $v) {
					$arr[$tvPrefix.$v] = $tmp->getTVValue($v);
				}
			}
			if ($includeMS != 0 && $tmp2 = $modx->getObject('ModGoods', array('gid' => $v['rid'], 'wid' => $_SESSION['minishop']['warehouse']))) {
				$tmp2 = $tmp2->toArray();
				unset($tmp2['id']);
				foreach ($tmp2 as $k => $v) {
					$arr[$k] = $v;
				}
			}
			$result[] = $modx->getChunk($tpl, $arr);
		}
	}
	$modx->setPlaceholder($plPrefix.'render_time',$modx->mSearch->get_execution_time() - $modx->getPlaceholder($plPrefix.'query_time'));

	if ($i == 0) {
		$modx->setPlaceholder($plPrefix.'error', $modx->lexicon('mse.err_no_results'));
		return;
	}
	return implode($outputSeparator, $result);
}