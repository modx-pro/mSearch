<?php

if (isset($modx->mSearch->config)) {
	$modx->mSearch->config = $config = array_merge($modx->mSearch->config, $scriptProperties, array('returnIds' => 1, 'limit' => 0));
}
else {
	$config = array_merge($scriptProperties, array('returnIds' => 1, 'limit' => 0));
}

$ids = $modx->runSnippet('mSearch', $config);
if (empty($ids)) {return;}

$params = $modx->mSearch->getFilterParams($ids);

$result = ''; $idx = 0;
foreach ($params as $v) {
	$rows = '';
	if ($v['type'] == 'number') {
		$tmp = array_keys($v['values']);
		if (count($tmp) < 2) {continue;}
		$rows .= $modx->getChunk($tplParamNumber, array('min' => min($tmp), 'max' => max($tmp), 'idx' => $idx));
		$idx++;
	}
	else {
		if (count($v['values']) < 2) {continue;}
		ksort($v['values']);
		foreach ($v['values'] as $k2 => $v2) {
			$rows .= $modx->getChunk($tplParamCheckbox, array('value' => $k2, 'num' => $v2, 'idx' => $idx));
			$idx++;
		}
	}
	$v['rows'] = $rows;
	$result .= $modx->getChunk($tplParamOuter, $v); 
}

return $result;
