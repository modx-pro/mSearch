<form action="[[~[[*id]]]]" method="post" id="mFilter" class="form-horizontal">
	[[!mFilter?
		&includeMS=`1`
		&includeMSList=`price,add2,weight`
		&includeTVs=`0`
	]]
	<input type="hidden" name="query" value="[[+mse.query]]">
	<input type="hidden" name="page" value="1">
	<input type="hidden" name="sort" value="ms_price,asc">
	<input type="hidden" name="limit" value="3">
	<input type="hidden" name="cat_id" value="[[*id]]">
	<input type="hidden" name="action" value="filter" />
</form>
