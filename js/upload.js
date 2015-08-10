function usrImgUpload {
	// hide image, replace it with loading gif
	var fd = new FormData($('#usr-img-form'));
	fd.append('label', 'WEBUPLOAD');
	$.ajax({
		url: 'usr_img_upload.php';
		type: 'POST',
		data: fd,
		enctype: 'multipart/form-data',
		processData: false,
		contentType: false
	}).done( function (data) {
		var message = $('#response');
		message.removeClass();
		if (data.status == 1) {
			message.addClass('message-success');
			// HIDE LOADING GIF
			// UPDATE THE IMAGE
			// DELETE OLD IMAGE
			$('.usr-img-box img').attr('src', );
			message.show().delay(2000).fadeOut();
		}
		else {
			message.addClass('message-error');
			message.show();
			// HIDE LOADING GIF
			// SHOW THE IMAGE
		}
		message.html(data.response);
		console.log(data);
	});
}





$('#usr-img-upload').on('change', usrImgUpload());