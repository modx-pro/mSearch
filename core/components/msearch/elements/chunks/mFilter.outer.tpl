<div id="mItems">
    <div class="sort">
        <p>Сортировка:
            [[+sort:is=`tv.popular,desc`:then=`<b>Популярные</b>`:else=`<a href="#" class="mSort" data-sort="tv.popular,desc">По популярности</a>`]]  /
            [[+sort:is=`ms.price,desc`:then=`<b>Дорогие</b>`:else=`<a href="#" class="mSort" data-sort="ms.price,desc">По цене, по возрастанию</a>`]]  /
            [[+sort:is=`ms.price,asc`:then=`<b>Дешевые</b>`:else=`<a href="#" class="mSort" data-sort="ms.price,asc">По цене, по убыванию</a>`]]
        </p>
        <div class="pagination">
        	[[+page.nav]]
        </div>
    </div><!-- end_sort -->
    	<ul class="list">
            [[+rows]]
        </ul><!-- end_list -->
        <div class="sort">
    	[[+limit:isnot=`20`:then=`<p><a href="#" class="mLimit" data-limit="20">Показывать по 20 товаров</a></p>`:else=`<p><a href="#" class="mLimit" data-limit="5">Показывать по 5 товаров</a></p>`]]
        <div class="pagination">
        	[[+page.nav]]
        </div>
    </div>
</div>
