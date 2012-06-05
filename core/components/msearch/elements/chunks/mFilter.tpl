<form action="[[~5]]" method="post" id="mFilter">
	[[!mFilter?
		&includeTVs=`1`
		&includeTVList=`size,color`
		&includeMS=`1`
		&includeMSList=`price,add2`
	]]
	<input type="hidden" name="query" value="[[+mse.query]]">
	<input type="hidden" name="page" value="1">
	<input type="hidden" name="sort" value="ms_price,asc">
	<input type="hidden" name="limit" value="10">
	<input type="hidden" name="cat_id" value="[[*id]]">
	<input type="hidden" name="action" value="filter" />
</form>
