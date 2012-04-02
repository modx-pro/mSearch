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
 * Adds modActions and modMenus into package
 *
 * @package msearch
 * @subpackage build
 */
 /*
$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'msearch',
    'parent' => 0,
    'controller' => 'index',
    'haslayout' => 1,
    'lang_topics' => 'msearch:default',
    'assets' => '',
),'',true,true);


$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'msearch',
    'parent' => 'components',
    'description' => 'msearch.menu_desc',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);
unset($action);
*/
$menu = array();

return $menu;