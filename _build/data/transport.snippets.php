<?php
/**
 * Add snippets to build
 * 
 * @package msearch
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'mSearch',
    'description' => 'Simple search snippet with russian morphology support',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.msearch.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.msearch.php';
$snippets[0]->setProperties($properties[0]);


unset($properties);
return $snippets;
