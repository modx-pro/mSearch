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

if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for mSearchIndexer.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for mSearchIndexer!');
}
unset($events);

return $plugins;