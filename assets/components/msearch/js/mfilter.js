$(document).ready(function() {

	// Слайдеры фильтра
	$('.mFilter_slider').each(function() {
		var form = $(this).parents('form');
		var idx = $(this).data('idx');
		var vmin = Number($('#min_'+idx).val());
		var vmax = Number($('#max_'+idx).val());
		var elem = this;

		$(this).slider({
			min: vmin
			,max: vmax
			,values: [vmin, vmax]
			,range: true
			,stop: function(event, ui) {
				$('input#min_'+idx).val($(this).slider('values',0));
				$('input#max_'+idx).val($(this).slider('values',1));
				$(form).find('input[name=page]').val(1);
				if ($(this).slider('values',0) != $(this).slider('option','min') || $(this).slider('values',1) != $(this).slider('option','max')) {
					$(this).parents('fieldset').find('.reset').show();
				}
				else {
					$(this).parents('fieldset').find('.reset').hide();
				}
				$(form).submit();
			},
			slide: function(event, ui){
				$('input#min_'+idx).val($(this).slider('values',0));
				$('input#max_'+idx).val($(this).slider('values',1));
			}
		})
	})

	// Фильтр товаров
	$(document).on('click', '#mFilter .reset', function(e) {
		e.preventDefault();
		$(this).hide();
		if ($(this).data('type') == 'number') {
			var sl = $(this).parents('fieldset').find('.mFilter_slider');
			$(this).parents('fieldset').find('.minCost').val(sl.slider('option','min'));
			$(this).parents('fieldset').find('.maxCost').val(sl.slider('option','max')).trigger('change');
			return;
		}
		else {
			var tmp = $(this).parents('fieldset').find('input[type=checkbox]:checked');
			if (tmp.length == 0) {
				return;
			}
			tmp.removeAttr('checked');
			$(this).parents('fieldset').find('sup').show();
			$(this).parents('form').submit();
		}

	})
	$(document).on('change', '#mFilter input', function() {
		if ($(this).attr('type') == 'checkbox') {
			$('#mFilter input[name=page]').val(1);
			$(this).parent().find('sup').toggle();
		}
		if ($(this).parents('fieldset').find('input:checked').length) {
			$(this).parents('fieldset').find('.reset').show();
		}
		else {
			$(this).parents('fieldset').find('.reset').hide();
		}
		$(this).parents('form').submit()
	})
	
	$(document).on('click', '#mItems .reset', function(e) {
		$('#mFilter input[name=page]').val(1);
		$('#mFilter input[name=limit]').val(limit).trigger('change');
		e.preventDefault();
	})
	$(document).on('click', '#mItems .mLimit', function(e) {
		var limit = $(this).data('limit');
		$('#mFilter input[name=page]').val(1);
		$('#mFilter input[name=limit]').val(limit).trigger('change');
		e.preventDefault();
	})
	$(document).on('click', '#mItems .mSort', function(e) {
		var sortby = $(this).data('sort');
		$('#mFilter input[name=sort]').val(sortby).trigger('change');
		e.preventDefault();
	})
	$(document).on('click', '#mItems .pages a', function(e) {
		var href = $(this).attr('href').split('=');
		
		$('#mFilter input[name=page]').val(href[href.length - 1]).trigger('change');
		e.preventDefault();
	})
	$(document).on('submit', '#mFilter', function(e) {
		$(this).ajaxSubmit({
			beforeSubmit: function() {
				$('#mItems').css('opacity',.5)
			}
			,success: function(res,status,form) {
				var data = $.parseJSON(res)

				if (data.total) {
					$('.content h1 span').text(' ('+data.total+')');
				}
				$('#mFilter input[type=checkbox]').each(function() {
					var name = $(this).attr('name').replace(/\[\]/, '');
					var val = $(this).val();
					
					tmp = data.filter[name][val];
					
					if (tmp != 0) {
						$(this).removeAttr('disabled').parent().find('sup').text(tmp);
					}
					else {
						$(this).attr('disabled','disabled').parent().find('sup').text(0);
					}
				})
				$('#mItems').replaceWith(data.rows).css('opacity',1)
			}
			,beforeSubmit: function showRequest(formData, jqForm, options) {
				var tmp = new Object();
				for (var i in formData) {
					key = formData[i].name
					if (key == 'query' || key == 'action' || key == 'cat_id') {continue;}
					if (tmp[key] == undefined) {
						tmp[key] = new Array();
					}
					tmp[key].push(formData[i].value)
				}
				var tmp2 = new Object();
				for (var i in tmp) {
					tmp2[i] = tmp[i].join('--')
				}
				document.location.hash = $.param(tmp2)
				return true; 
			} 
		})
		e.preventDefault();
	})
	$(document).on('change', '#mFilter .minCost, #mFilter .maxCost', function() {
		var fieldset = $(this).parents('fieldset');
		var vmin = fieldset.find('.minCost').val();
		var vmax = fieldset.find('.maxCost').val();

		if (Number(vmin) > Number(vmax)) {
			vmin = vmax;
			fieldset.find('.minCost').val(vmin);
			fieldset.find('.maxCost').val(vmax);
		}
		fieldset.find('.mFilter_slider').slider("values",0,vmin);
		fieldset.find('.mFilter_slider').slider("values",1,vmax);
	})
	$(document).on('click', 'ul.selected a', function(e) {
		e.preventDefault();
		if ($(this).hasClass('remove')) {
			var val = $(this).prev().text();
		}
		else {
			var val = $(this).text();
		}
		$(this).parent('li').remove();
		$('#mFilter input[value='+val+']').trigger('click');
	})

	// Если есть хэш в url - пробуем отправить форму
	if (vars = getUrlVars()) {
		if ($('#mFilter').length == 0) {return;}
		else {
			$('#mFilter fieldset').each(function() {
				if ($(this).find('.reset').data('type') == 'number') {return;}
				if ($(this).find('input[type=checkbox]').length > 9) {
					var tmp = $(this).find('ul');
					$(this).find('ul').replaceWith('<div class="scroll"><div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div><div class="viewport"><div class="overview"></div></div></div>');
					$(this).find('.overview').append(tmp);
				}
			})
		}
		
		for (i in vars) {
			if (/\[\]/.test(i)) {
				for (i2 in vars[i]) {
					el = $('input[name='+i+'][value='+vars[i][i2]+']');
					if (el.length > 0) {
						el.attr('checked','checked').parent().find('sup').hide();
						el.parents('fieldset').find('.reset[data-type!=number]').show();
					}
					else {
						if (i2 == 0) {
							$('input[name='+i+'].minCost').val(vars[i][0]).parents('fieldset').find('.mFilter_slider').slider("values",0,vars[i][0]);
							$('input[name='+i+'].maxCost').val(vars[i][1]).parents('fieldset').find('.reset[data-type=number]').show();
						}
						else {
							$('input[name='+i+'].maxCost').val(vars[i][1]).parents('fieldset').find('.mFilter_slider').slider("values",1,vars[i][1])
							$('input[name='+i+'].maxCost').val(vars[i][1]).parents('fieldset').find('.reset[data-type=number]').show();
							
						}
					}
				}
			}
			else {
				$('input[name='+i+']').val(vars[i]);
			}
		}
		$('#mFilter').submit()
	}
	else if ($('#mFilter').length) {
		$('#mFilter').submit();
	}
})


// Разбор хэша url на значения
function getUrlVars() {
    var vars = new Object(), hash;
    var hashes = decodeURIComponent(window.location.hash.substr(1));
    if (hashes.length == 0) {return false;}
    else {hashes = hashes.split('&');}

	for (var i in hashes) {
        hash = hashes[i].split('=');

		if (hash[1] != undefined && /\[\]/.test(hash[0])) {
			var tmp = hash[1].split('--');
			hash[1] = new Array();
			for (var i2 in tmp) {
				hash[1].push(tmp[i2]);
			}
		}
		vars[hash[0]] = hash[1];
	}
	return vars;
}
