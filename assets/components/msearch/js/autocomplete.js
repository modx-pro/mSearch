$(document).ready(function() {

	$("#searchInput").autocomplete({
		source: function(request, response) {
				$.post('/service/ac.html',{autocomplete: true, query: request.term}, function(data) {
					response($.parseJSON(data))
				});
		}
		,minLength: 2
		,select: function(event,ui) {
			$("#searchInput").val(ui.item.value).parents('form').submit();
			
		}
	})
	.data("autocomplete")._renderItem = function( ul, item ) {
		return $("<li></li>")
		   .data("item.autocomplete", item)
		   .append("<a>"+ item.label + "</a>")
		   .appendTo( ul );
	};

})
