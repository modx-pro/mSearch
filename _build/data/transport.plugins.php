<?php
/**
 * Package in plugins
 *
 * @package mSearch
 * @subpackage build
 */
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id',1);
$plugins[0]->set('name','mSearchIndexer');
$plugins[0]->set('description','Indexing of your resources on save');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'plugin.indexer.php'));
$plugins[0]->set('category',0);


$events = array();
$events['OnDocFormSave']= $modx->newObject('modPluginEvent');
$events['OnDocFormSave']->fromArray(array(
	'event' => 'OnDocFormSave',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

$events['OnDocFormDelete']= $modx->newObject('modPluginEvent');
$events['OnDocFormDelete']->fromArray(array(
	'event' => 'OnDocFormDelete',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

$events['OnSiteRefresh']= $modx->newObject('modPluginEvent');
$events['OnSiteRefresh']->fromArray(array(
	'event' => 'OnSiteRefresh',
	'priority' => 0,
	'propertyset' => 0,
),'',true,true);

if (is_array($events) && !empty($events)) {
	$plugins[0]->addMany($events);
	$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for mSearchIndexer.'); flush();
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for mSearchIndexer!');
}
unset($events);

$properties = array(
	array(
		'name' => 'includeTVs',
		'desc' => 'mse.includeTVs',
		'type' => 'combo-boolean',
		'value' => false,
		'lexicon' => 'msearch:properties'
	),
	array(
		'name' => 'includeTVList',
		'desc' => 'mse.includeTVList',
		'type' => 'textfield',
		'value' => '',
		'lexicon' => 'msearch:properties'
	),
	array(
		'name' => 'indexFields',
		'desc' => 'mse.indexFields',
		'type' => 'textfield',
		'value' => 'pagetitle,longtitle,description,introtext,content',
		'lexicon' => 'msearch:properties'
	),
	array(
		'name' => 'disablePhpMorphy',
		'desc' => 'mse.disablePhpMorphy',
		'type' => 'combo-boolean',
		'value' => false,
		'lexicon' => 'msearch:properties'
	),
);
if (is_array($properties)) {
	$modx->log(xPDO::LOG_LEVEL_INFO,'Set '.count($properties).' plugin properties.'); flush();
	$plugins[0]->setProperties($properties);
} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Could not set plugin properties.');
}
unset($properties);

return $plugins;
