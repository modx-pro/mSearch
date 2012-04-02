<?php

if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
	$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
	if (!($modx->mSearch instanceof mSearch)) return '';
}
$modx->mSearch->get_execution_time();

$offset = !empty($offset) ? $offset : 0;
$limit = !empty($limit) ? $limit : 0;

$q = $modx->newQuery('modResource');
$q->where(array('deleted', 0));
$q->limit($limit, $offset);

$resources = $modx->getIterator('modResource', $q);
$i = 0;
foreach ($resources as $v) {
	if (!$res = $modx->getObject('ModResIndex', array('rid' => $v->get('id')))) {
		$res = $modx->newObject('ModResIndex');
		$res->set('rid', $v->get('id'));
	}
	$content = $modx->mSearch->stripTags($v->get('content'));
	$content = $modx->stripTags($content);

	$resource = implode(' ', array(
			$v->get('pagetitle')
			,$v->get('longtitle')
			,$v->get('description')
			,$v->get('introtext')
			,$content
		)
	);
	$resource = strip_tags($resource);
	$index = $modx->mSearch->getBaseForms($resource);

	$res->set('resource', $resource);
	$res->set('index', $index);
	if ($res->save()) {
		$i++;
	}
}

$t = $modx->mSearch->get_execution_time();
return "Total: $i resources, time: $t";
?>