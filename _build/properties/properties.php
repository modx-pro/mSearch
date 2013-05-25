<?php
/**
 * Properties for the mSearch snippet.
 *
 * @package msearch
 * @subpackage build
 */
$properties = array(
	// Snippet mSearch
	array(
		array(
			'name' => 'tpl',
			'desc' => 'mse.tpl',
			'type' => 'textfield',
			'value' => 'tpl.mSearch.row',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'limit',
			'desc' => 'mse.limit',
			'type' => 'numberfield',
			'value' => 10,
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'offset',
			'desc' => 'mse.offset',
			'type' => 'numberfield',
			'value' => '0',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'outputSeparator',
			'desc' => 'mse.outputSeparator',
			'type' => 'textfield',
			'value' => "\n",
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'totalVar',
			'desc' => 'mse.totalVar',
			'type' => 'textfield',
			'value' => 'total',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'queryVar',
			'desc' => 'mse.queryVar',
			'type' => 'textfield',
			'value' => 'query',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'parentsVar',
			'desc' => 'mse.parentsVar',
			'type' => 'textfield',
			'value' => 'parents',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'minQuery',
			'desc' => 'mse.minQuery',
			'type' => 'numberfield',
			'value' => 3,
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'returnIds',
			'desc' => 'mse.returnIds',
			'type' => 'combo-boolean',
			'value' => false,
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'plPrefix',
			'desc' => 'mse.plPrefix',
			'type' => 'textfield',
			'value' => 'mse.',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'tvPrefix',
			'desc' => 'mse.tvPrefix',
			'type' => 'textfield',
			'value' => 'tv.',
			'lexicon' => 'msearch:properties'
		),
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
			'name' => 'includeMS',
			'desc' => 'mse.includeMS',
			'type' => 'combo-boolean',
			'value' => false,
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'context',
			'desc' => 'mse.context',
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
			'name' => 'showHidden',
			'desc' => 'mse.showHidden',
			'type' => 'combo-boolean',
			'value' => false,
		'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'showUnpublished',
			'desc' => 'mse.showUnpublished',
			'type' => 'combo-boolean',
			'value' => false,
		'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'templates',
			'desc' => 'mse.templates',
			'type' => 'textfield',
			'value' => '',
		'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'resources',
			'desc' => 'mse.resources',
			'type' => 'textfield',
			'value' => '',
		'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'parents',
			'desc' => 'mse.parents',
			'type' => 'textfield',
			'value' => '',
		'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'where',
			'desc' => 'mse.where',
			'type' => 'textfield',
			'value' => '',
		'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'disablePhpMorphy',
			'desc' => 'mse.disablePhpMorphy',
			'type' => 'combo-boolean',
			'value' => false,
		'lexicon' => 'msearch:properties'
		),
	)
	,array(
		array(
			'name' => 'includeTVs',
			'desc' => 'mse.includeTVs',
			'type' => 'combo-boolean',
			'value' => true,
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
			'name' => 'excludeTVList',
			'desc' => 'mse.excludeTVList',
			'type' => 'textfield',
			'value' => '',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'includeMS',
			'desc' => 'mse.includeMS',
			'type' => 'combo-boolean',
			'value' => false,
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'includeMSList',
			'desc' => 'mse.includeMSList',
			'type' => 'textfield',
			'value' => '',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'tplParamOuter',
			'desc' => 'mse.tplParamOuter',
			'type' => 'textfield',
			'value' => 'tpl.mFilter.param.outer',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'tplParamCheckbox',
			'desc' => 'mse.tplParamCheckbox',
			'type' => 'textfield',
			'value' => 'tpl.mFilter.param.checkbox',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'tplParamNumber',
			'desc' => 'mse.tplParamNumber',
			'type' => 'textfield',
			'value' => 'tpl.mFilter.param.number',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'tplOuter',
			'desc' => 'mse.tplOuter',
			'type' => 'textfield',
			'value' => 'tpl.mFilter.outer',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'resources',
			'desc' => 'mse.ids',
			'type' => 'textfield',
			'value' => '',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'tpl',
			'desc' => 'mse.tpl',
			'type' => 'textfield',
			'value' => 'tpl.mSearch.row',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'sortFilters',
			'desc' => 'mse.sortFilters',
			'type' => 'textfield',
			'value' => '',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'snippet',
			'desc' => 'mse.snippet',
			'type' => 'textfield',
			'value' => '',
			'lexicon' => 'msearch:properties'
		),
		array(
			'name' => 'paginator',
			'desc' => 'mse.paginator',
			'type' => 'textfield',
			'value' => 'getPage',
			'lexicon' => 'msearch:properties'
		),
	)

);

return $properties;
