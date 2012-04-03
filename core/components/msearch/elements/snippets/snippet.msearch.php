<?php
if (!empty($indexer)) {
	return require $modx->getOption('core_path').'components/msearch/elements/snippets/snippet.indexer.php';
}

// Определяем переменные для работы
$tplRow = !empty($tplRow) ? $tplRow : 'tpl.mSearch.row';
$limit = !empty($limit) ? $limit : 0;
$offset = !empty($offset) ? $offset : 0;
$outputSeparator = isset($outputSeparator) ? $outputSeparator : "\n";
$totalVar = !empty($totalVar) ? $totalVar : 'total';
$minQuery = !empty($minQuery) ? $minQuery : 3;
$returnIds = !empty($returnIds) ? 1 : 0;

$add_query = '';
if (empty($showHidden)) {$add_query .= " AND `hidemenu` != 1";}
if (empty($showUnpublished)) {$add_query .= " AND `published` != 0";}
if (!empty($templates)) {$add_query .= " AND `template` IN ($templates)";}
if (!empty($resources)) {$add_query .= " AND `rid` IN ($resources)";}
if (!empty($parents)) {
	$tmp = explode(',',$parents);
	$arr = $tmp;
	foreach ($tmp as $v) {
		$arr = array_merge($arr, $modx->getChildIds($v));
	}
	$ids = implode(',', $arr);
	$add_query .= " AND `rid` IN ($ids)";
}

// Подключаем класс mSearch
if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
	$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
	if (!($modx->mSearch instanceof mSearch)) return '';
}
$modx->mSearch->get_execution_time();

// Обрабатываем поисковый запрос
$query = trim(strip_tags($_GET['query']));
if (empty($query) && !empty($_GET['query'])) {
	$modx->setPlaceholder('mse.error', $modx->lexicon('mse.err_no_query'));
	return;
}
else if (strlen($query) < $minQuery && !empty($_GET['query'])) {
	$modx->setPlaceholder('mse.error', $modx->lexicon('mse.err_min_query'));
	return;
}
else if (empty($query)) {
	$modx->setPlaceholder('mse.error', ' ');
	return;
}
else {
	$modx->setPlaceholder('mse.query', $_GET['query']);
}


// Получаем все возможные формы слов запроса
$query_string = $modx->mSearch->getAllForms($query);

// Составляем запросы в БД
$db_index = $modx->getTableName('ModResIndex');
$db_res = $modx->getTableName('modResource');
// Определяем количество результатов
$sql = "SELECT COUNT(`rid`) FROM $db_index 
	LEFT JOIN $db_res ON $db_index.`rid` = $db_res.`id`
	WHERE (MATCH (`resource`,`index`) AGAINST ('$query_string') OR `resource` LIKE '%$query%')
	AND `searchable` = 1 $add_query";

$q = new xPDOCriteria($modx, $sql);
if ($q->prepare() && $q->stmt->execute()){
	$total = $q->stmt->fetchColumn();
	$modx->setPlaceholder($totalVar, $total);
	if ($total == 0) {
		$modx->setPlaceholder('mse.error', $modx->lexicon('mse.err_no_results'));
		return;
	}
}
// Если их больше 0 - запускаем основной поиск
$sql = "SELECT `rid`,`resource`,
	MATCH (`resource`,`index`) AGAINST ('>\"$query\" <($query_string)' IN BOOLEAN MODE) as `rel`
	FROM $db_index 
	LEFT JOIN $db_res ON $db_index.`rid` = $db_res.`id`
	WHERE (MATCH (`resource`,`index`) AGAINST ('>\"$query\" <($query_string)' IN BOOLEAN MODE) OR `resource` LIKE '%$query%')
	AND `searchable` = 1 $add_query
	ORDER BY `rel` DESC
	LIMIT $offset,$limit";
$modx->setPlaceholder('mse.query_string',$sql);
$q = new xPDOCriteria($modx, $sql);
$q->prepare();
$q->stmt->execute();

$res = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
$modx->setPlaceholder('mse.query_time',$modx->mSearch->get_execution_time());
$result = array();
$i = $offset;

// Возвращаем либо список подходящих ID, либо готовый результат
if ($returnIds == 1) {
	$ids = array();
	foreach ($res as $v) {
		$ids[] = $v['rid'];
	}
	return implode(',', $ids);
}
else {
	foreach ($res as $v) {
		if ($tmp = $modx->getObject('modResource', $v['rid'])) {
			$arr = $tmp->toArray();
			$i++;
			$arr['num'] = $i;
			$arr['intro'] = $modx->mSearch->Highlight($v['resource'], $query);
			$result[] = $modx->getChunk($tplRow, $arr);
			
		}
	}
	$modx->setPlaceholder('mse.render_time',$modx->mSearch->get_execution_time());

	if ($i == 0) {
		$modx->setPlaceholder('mse.error', $modx->lexicon('mse.err_no_results'));
		return;
	}
	return implode($outputSeparator, $result);
}