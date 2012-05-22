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
	),
	// Snippet mSearch.indexer
	array()
);

return $properties;
