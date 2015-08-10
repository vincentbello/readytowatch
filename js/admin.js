$('#admin-links').on('shown.bs.modal', function (e) {
	var id = $('.links-container').attr('id').substring(2);

	$.ajax({
    	type: "POST",
    	url: "ajax/admin/links_modal.php",
    	data: { id: id }
    }).done( function (links) {
    	for (var p in links) {
    		var html = "";
    		for (var link in links[p]) {
    			var caption = link.charAt(0).toUpperCase() + link.slice(1);
    			html += "<b>" + caption + "</b>";
    			html += "<input type='text' value='" + links[p][link] + "' name='" + link + "'><br>";
    		}
    		$( '#admin-' + p ).prepend(html);
    	}
    }).fail( function () {
    	console.log('failed');
    });
});

$('#admin-links button').on('click', function (e) {
	var that = $( this );
	var linkType = that.data('link-type');
	var id = $('input[name="id"]').val();
	var inputs = that.parent().find('input[type="text"]');
	var values = {};
	inputs.each( function () {
		var input = $( this );
		if (input.attr('name') != 'id')
			values[input.attr('name')] = input.val();
	});

	$.ajax({
    	type: "POST",
    	url: "ajax/admin/save_link_changes.php",
    	data: { id: id, linkType: linkType, values: values }
    }).done( function (data) {
		that.parent().find('.message-success').html(data).delay(2000).fadeOut();
    }).fail( function () {
    	console.log('failed');
    });
});