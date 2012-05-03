<?php
if ($modx->event->name == 'OnDocFormSave') {
	if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
		$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
		if (!($modx->mSearch instanceof mSearch)) return '';
	}

	$resource = $modx->event->params['resource'];

	if ($resource->get('searchable') == 0) {
		if ($res = $modx->getObject('ModResIndex', array('rid' => $resource->get('id'))) {
			$res->remove();
		}
		return;
	}

	if (!$res = $modx->getObject('ModResIndex', array('rid' => $resource->get('id')))) {
		$res = $modx->newObject('ModResIndex');
		$res->set('rid', $resource->get('id'));
	}

	$tvs = '';
	if (!empty($includeTVList) && $includeTVs) {
		$includeTVList = explode(',',$includeTVList);
		foreach ($includeTVList as $v) {
			if ($tv = $resource->getTVValue($v)) {
				$tv = $modx->mSearch->stripTags($tv);
				$tvs .= $tv.' ';
			}
		}
	}
	
	$content = $modx->mSearch->stripTags($resource->get('content');
	$index = implode(' ', array(
			$resource->get('pagetitle')
			,$resource->get('longtitle')
			,$resource->get('description')
			,$resource->get('introtext')
			,$content
			,$tvs
		)
	);
	$words = $modx->mSearch->getBaseForms($index);

	$res->set('resource', $index);
	$res->set('index', $words);
	$res->save();
}

if ($modx->event->name == 'OnDocFormDelete') {
	if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
		$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
		if (!($modx->mSearch instanceof mSearch)) return '';
	}

	$resource = $modx->event->params['resource'];

	if ($res = $modx->getObject('ModResIndex', array('rid' => $resource->get('id')))) {
		$res->remove();
	}
}