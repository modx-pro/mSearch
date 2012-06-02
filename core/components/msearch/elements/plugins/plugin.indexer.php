<?php
if ($modx->event->name == 'OnDocFormSave') {
	if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
		$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
		if (!($modx->mSearch instanceof mSearch)) return '';
	}

	$resource = $modx->event->params['resource'];

	if ($resource->get('searchable') == 0) {
		if ($res = $modx->getObject('ModResIndex', array('rid' => $resource->get('id')))) {
			$res->remove();
		}
		return;
	}

	if (!$res = $modx->getObject('ModResIndex', array('rid' => $resource->get('id')))) {
		$res = $modx->newObject('ModResIndex');
		$res->set('rid', $resource->get('id'));
	}

	if (isset($indexFields) && !empty($indexFields)) {
		$indexFields = explode(',', $indexFields);
		$text = '';
		foreach ($indexFields as $v2) {
			if ($tmp = $resource->get($v2)) {
				$text .= $modx->mSearch->stripTags($tmp).' ';
			}
		}
	}
	else {
		$text = $modx->mSearch->stripTags($resource->get('pagetitle'));
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

	$index = $text.' '.$tvs;
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

if ($modx->event->name == 'OnSiteRefresh') {
    if ($modx->cacheManager->clearCache(array('default/msearch'))) {
        $modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('refresh_default').': mSearch');
    }
}