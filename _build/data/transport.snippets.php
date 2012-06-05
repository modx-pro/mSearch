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
    'description' => 'Search snippet with russian morphology support',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.msearch.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.php';
$snippets[0]->setProperties($properties[0]);

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 0,
    'name' => 'mFilter',
    'description' => 'Snippet for filtering results by TVs and/or miniShop goods properties',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.mfilter.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.php';
$snippets[1]->setProperties($properties[1]);


unset($properties);
return $snippets;
