<div class="row">
	<div class="span3">
		<div class="filter">
			<form action="[[~[[*id]]]]" method="post" id="mFilter">
				[[!mFilter?
					&resources=``
					&includeTVs=`0`
					&includeMS=`1`
					&includeMSList=`price,new,favorite,popular,size,color`
					&sortFilters=`ms_new,ms_favorite,ms_popular,ms_size,ms_color,ms_price`
					&tpl=`tpl.msProducts.row`
				]]
				<input type="hidden" name="query" value="[[+mse.query]]">
				<input type="hidden" name="page" value="1">
				<input type="hidden" name="sort" value="ms_price,asc">
				<input type="hidden" name="limit" value="10">
				<input type="hidden" name="parents" value="[[+parents]]">
				<input type="hidden" name="action" value="filter" />
			</form>
		</div><!-- end_filter -->
	</div>
	<div class="span9" id="mItems"></div>
</div>

<link href="http://yandex.st/jquery-ui/1.10.3/themes/smoothness/jquery-ui.min.css" rel="stylesheet" />
<script src="http://yandex.st/jquery-ui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>
<script src="/assets/components/msearch/js/mfilter.js" type="text/javascript"></script>