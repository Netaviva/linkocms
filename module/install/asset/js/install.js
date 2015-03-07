$App.installer = function(){
	$('.ajax-chmod-trigger').on('click', function(){
		var file = $(this).parent().find('span.ajax-chmod-file').html();

		var notify_id = Linko.notify.show('Trying to chmod ' + file + ' to 777', {type: 'flash'});

		Linko.ajax.post('install/chmod', {file: file}, function(response){
			if(response.return == true){
				Linko.notify.update(notify_id, 'Chmod successfull');
			}
			else{
				Linko.notify.update(notify_id, 'Chmod failed');
			}
		});

		return false;
	});
};