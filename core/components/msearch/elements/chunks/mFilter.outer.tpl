<div class="sort">
	<p>Сортировка:
		[[+sort:is=`ms_popular,desc`:then=`<b>Популярные</b>`:else=`<a href="#" class="mSort" data-sort="ms_popular,desc">Популярные</a>`]]  /
		[[+sort:is=`ms_price,desc`:then=`<b>Цена, по возрастанию</b>`:else=`<a href="#" class="mSort" data-sort="ms_price,desc">Цена, по возрастанию</a>`]]  /
		[[+sort:is=`ms_price,asc`:then=`<b>Цена, по убыванию</b>`:else=`<a href="#" class="mSort" data-sort="ms_price,asc">Цена, по убыванию</a>`]]
	</p>
</div>

<p>
	[[+rows]]
</p>

<div class="pagination">
	<ul>
		[[+limit:isnot=`10`:then=`<li><a href="#" class="mLimit" data-limit="10">Показывать по 10 товаров</a></li>`:else=`<li><a href="#" class="mLimit" data-limit="3">Показывать по 3 товара</a></li>`]]
		[[+page.nav]]
	</ul>
</div>
