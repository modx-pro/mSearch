<?php

$_lang['mse.tpl'] = 'Chunk for templating results.';
$_lang['mse.limit'] = 'Limit for returned results. Used for pagination and indexing.';
$_lang['mse.offset'] = 'Offset of returned results. Used for pagination and indexing.';
$_lang['mse.outputSeparator'] = 'Output separator. Used for pagination.';
$_lang['mse.totalVar'] = 'Name of placeholder with count of results. Used for pagination.';
$_lang['mse.queryVar'] = 'Name of variable in request with search query. By default - query, e.g., snippet will expect $_REQUEST[\'query\']';
$_lang['mse.parentsVar'] = 'Name of variable in request for filtering results by parents. By default - parents, e.g., snippet will expect $_REQUEST[\'parents\']';
$_lang['mse.minQuery'] = 'Minimal number of characters for start searching.';
$_lang['mse.returnIds'] = 'If enabled - instead of the templated results snippet returns a list of comma separated id of found resources. You can use it for display search results by other snippets, e.g. getResources.';
$_lang['mse.plPrefix'] = 'Placeholders prefix.';
$_lang['mse.tvPrefix'] = 'TV prefix.';
$_lang['mse.includeTVs'] = 'Need to include TVs?';
$_lang['mse.includeTVList'] = 'List of comma-separated TVs for including.';
$_lang['mse.excludeTVList'] = 'List of comma-separated TVs for excluding.';
$_lang['mse.includeMS'] = 'Need to include parameters of resources-goods from miniShop tables?';
$_lang['mse.includeMSList'] = 'List of comma-separated parameters of miniShop goods for including.';
$_lang['mse.context'] = 'Working context. By default - current.';
$_lang['mse.indexFields'] = 'Fields of resources for indexing.';
$_lang['mse.showHidden'] = 'Show hidden resources.';
$_lang['mse.showUnpublished'] = 'Show unpublished resources';
$_lang['mse.templates'] = 'Comma separated list of templates for filtering resources.';
$_lang['mse.resources'] = 'Comma separated list of resources for search';
$_lang['mse.parents'] = 'Comma separated list of parents for search in it and its childrens.';
$_lang['mse.where'] = 'JSON expression for additional filtering of resources';
$_lang['mse.disablePhpMorphy'] = 'Do you want disable morphological search with phpMorphy?';

$_lang['mse.tplParamOuter'] = 'Chunk for templating one parameter in filter.';
$_lang['mse.tplParamCheckbox'] = 'Chunk for templating one checkbox item in filter parameter.';
$_lang['mse.tplParamNumber'] = 'Chunk for templating number item in filter parameter. For example, price ("From, to").';
$_lang['mse.tplOuter'] = 'Chunk for templating filter outer.';
$_lang['mse.ids'] = 'Comma-separated list of resources for generating filters and searching. If not specified - resources will be found by snippet mSearch.';
$_lang['mse.sortFilters'] = 'Comma-separated list how to sort filters, with prefix. For example: &filtersOrder=`tv_color,tv_vendor,ms_price,ms_weight,tv_year`. Prefix can be an "tv_" or "ms_" - from miniShop table.';
$_lang['mse.snippet'] = 'Custom snippet for receiving results of filter from database';
$_lang['mse.paginator'] = 'Snippet fot for paginating results';
