$(document).ready(function() {

	// Слайдеры фильтра
	$('.mFilter_slider').each(function() {
		var form = $(this).parents('form');
		var idx = $(this).parents('.mFilter_wrapper').data('idx');
		var vmin = Number($('#min_'+idx).val());
		var vmax = Number($('#max_'+idx).val());

		$(this).slider({
			min: vmin
			,max: vmax
			,values: [vmin, vmax]
			,range: true
			,stop: function(event, ui) {
				$('input#min_'+idx).val($(this).slider('values',0));
				$('input#max_'+idx).val($(this).slider('values',1));
				$(form).find('input[name=page]').val(1);
				$(form).submit();
			},
			slide: function(event, ui){
				$('input#min_'+idx).val($(this).slider('values',0));
				$('input#max_'+idx).val($(this).slider('values',1));
			}
		})
	})

	$(document).on('change', '#mFilter input', function() {
		if ($(this).attr('type') == 'checkbox') {
			$('#mFilter input[name=page]').val(1);
			$(this).parent().find('sup').toggle();
		}
		$(this).parents('form').submit()
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

	$(document).on('click', '#mItems .pagination a', function(e) {
		var href = $(this).attr('href').split('=');
		
		$('#mFilter input[name=page]').val(href[href.length - 1]).trigger('change');
		e.preventDefault();
	})

	$(document).on('submit', '#mFilter', function(e) {
		$(this).ajaxSubmit({
			success: function(res,status,form) {
				var data = $.parseJSON(res)

				if (data.total) {
					if ($('#mFilter_total'))
						$('#mFilter_total').text(' ('+data.total+')');
					else
						$('h1 span').text(' ('+data.total+')');
				}
				$('#mFilter input[type=checkbox]').each(function() {
					var name = $(this).attr('name').replace(/\[\]/, '');
					var val = $(this).val();

					if (data.filter[name] == undefined) {tmp = 0;}
					else {tmp = data.filter[name][val];}
					
					if (tmp != 0) {
						$(this).removeAttr('disabled').parent().find('sup').text(tmp);
					}
					else {
						$(this).attr('disabled','disabled').parent().find('sup').text(0);
					}
				})
				$('#mItems').html(data.rows).css('opacity',1)
			}
			,beforeSubmit: function showRequest(formData, jqForm, options) {
                $('#mItems').css('opacity',.5)
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


	// Если есть хэш в url - пробуем отправить форму
	if (vars = getUrlVars()) {
		if ($('#mFilter').length == 0) {return;}
		
		for (i in vars) {
			if (/\[\]/.test(i)) {
				for (i2 in vars[i]) {
					el = $('input[name="'+i+'"][value="'+vars[i][i2]+'"]');
					if (el.length > 0) {
						el.attr('checked','checked').parent().find('sup').hide();
					}
					else {
						var name = i.replace(/\[\]/, '');
						if (i2 == 0) {
							$('#mfilter_'+name+' .vmin').val(vars[i][0]).parent().find('.mFilter_slider').slider("values",0,vars[i][0]);
						}
						else {
							$('#mfilter_'+name+' .vmax').val(vars[i][1]).parent().find('.mFilter_slider').slider("values",1,vars[i][1])
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