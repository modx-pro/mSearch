<fieldset id="filter_[[+paramname]]">
    <h3>[[+name]]</h3>
    <div>
        [[+type:isnot=`number`:then=`<ul>`:else=``]]
            [[+rows]]
        [[+type:isnot=`number`:then=`</ul>`:else=``]]
    </div>
</fieldset>
