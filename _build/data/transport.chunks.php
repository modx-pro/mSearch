<?php
/**
 * mSearch
 *
 * Copyright 2010 by Shaun McCormick <shaun+msearch@modx.com>
 *
 * mSearch is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * mSearch is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * mSearch; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package msearch
 */
/**
 * Add chunks to build
 * 
 * @package msearch
 * @subpackage build
 */
$chunks = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'tpl.mSearch.row',
    'description' => 'Single row with search result',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/mSearch.row.tpl'),
),'',true,true);
//$properties = include $sources['build'].'properties/properties.msearch.php';
//$chunks[0]->setProperties($properties);
//unset($properties);

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 0,
    'name' => 'tpl.mFilter.outer',
    'description' => 'Chunk for templating filter outer.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/mFilter.outer.tpl'),
),'',true,true);

$chunks[2]= $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => 0,
    'name' => 'tpl.mFilter.param.outer',
    'description' => 'Chunk for templating one parameter in filter',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/mFilter.param.outer.tpl'),
),'',true,true);

$chunks[3]= $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => 0,
    'name' => 'tpl.mFilter.param.checkbox',
    'description' => 'Chunk for templating one checkbox item in filter parameter',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/mFilter.param.checkbox.tpl'),
),'',true,true);

$chunks[4]= $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => 0,
    'name' => 'tpl.mFilter.param.number',
    'description' => 'Chunk for templating number item in filter parameter. For example, price ("From, to")',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/mFilter.param.number.tpl'),
),'',true,true);

$chunks[5]= $modx->newObject('modChunk');
$chunks[5]->fromArray(array(
    'id' => 0,
    'name' => 'mFilter',
    'description' => 'Example chunk with mFilter call',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/chunks/mFilter.tpl'),
),'',true,true);


return $chunks;
