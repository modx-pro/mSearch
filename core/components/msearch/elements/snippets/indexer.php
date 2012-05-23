<?php

if (isset($indexFields) && !empty($indexFields)) {
	$indexFields = explode(',', $indexFields);
}
else {$indexFields[] = 'pagetitle';}

if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
	$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
	if (!($modx->mSearch instanceof mSearch)) return '';
}

ini_set('memory_limit', '512M');
set_time_limit(180);

$modx->mSearch->get_execution_time();

if (!empty($includeTVList) && !empty($includeTVs)) {
	$includeTVList = explode(',',$includeTVList);
}

$offset = !empty($offset) ? $offset : 0;
$limit = !empty($limit) ? $limit : 0;

$q = $modx->newQuery('modResource');
$q->where(array('deleted' => 0, 'searchable' => 1));
$q->limit($limit, $offset);

$count = $modx->getCount('modResource', $q);
$resources = $modx->getCollection('modResource', $q);
$i = 0;
foreach ($resources as $v) {
	if (!$res = $modx->getObject('ModResIndex', array('rid' => $v->get('id')))) {
		$res = $modx->newObject('ModResIndex');
		$res->set('rid', $v->get('id'));
	}

	$text = '';
	foreach ($indexFields as $v2) {
		if ($tmp = $v->get($v2)) {
			$text .= $modx->mSearch->stripTags($tmp).' ';
		}
	}

	$tvs = '';
	if (!empty($includeTVs)) {
		foreach ($includeTVList as $v2) {
			if ($tv = $v->getTVValue($v2)) {
				$tv = $modx->mSearch->stripTags($tv);
				$tvs .= $tv.' ';
			}
		}
	}
	
	$resource = $text.' '.$tvs;
	$index = $modx->mSearch->getBaseForms($resource);

	$res->set('resource', $resource);
	$res->set('index', $index);
	if ($res->save()) {
		$i++;
	}
}

$t = $modx->mSearch->get_execution_time();
return "Indexed: $i resources from $count, time: $t";
?>
