<?php
if ($modx->event->name == 'OnDocFormSave') {
	if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
		$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
		if (!($modx->mSearch instanceof mSearch)) return '';
	}

	if ($_POST['searchable'] == 0) {
		if ($res = $modx->getObject('ModResIndex', array('rid' => $_POST['id']))) {
			$res->remove();
		}
		return;
	}

	if (!$res = $modx->getObject('ModResIndex', array('rid' => $_POST['id']))) {
		$res = $modx->newObject('ModResIndex');
		$res->set('rid', $_POST['id']);
	}

	$tvs = '';
	if (!empty($includeTVList) && $includeTVs) {
		$includeTVList = explode(',',$includeTVList);
		foreach ($includeTVList as $v) {
			if ($tv = $modx->event->params['resource']->getTVValue($v)) {
				$tv = $modx->mSearch->stripTags($tv);
				$tvs .= $tv.' ';
			}
		}
	}
	
	$content = $modx->mSearch->stripTags($_POST['content']);
	$resource = implode(' ', array(
			$_POST['pagetitle']
			,$_POST['longtitle']
			,$_POST['description']
			,$_POST['introtext']
			,$content
			,$tvs
		)
	);
	$index = $modx->mSearch->getBaseForms($resource);

	$res->set('resource', $resource);
	$res->set('index', $index);
	$res->save();
}

if ($modx->event->name == 'OnDocFormDelete') {
	if (!isset($modx->mSearch) || !is_object($modx->mSearch)) {
		$modx->mSearch = $modx->getService('msearch','mSearch',$modx->getOption('msearch.core_path',null,$modx->getOption('core_path').'components/msearch/').'model/msearch/',$scriptProperties);
		if (!($modx->mSearch instanceof mSearch)) return '';
	}

	if ($res = $modx->getObject('ModResIndex', array('rid' => $_POST['id']))) {
		$res->remove();
	}
}