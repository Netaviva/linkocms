$(function(){
    var $cbox = $('#js-contentbox');
    var $cboxc = $cbox.html();

	$('.js-homepage-drag').draggable({
		cursor: 'move',
		axis: 'y',
		snap: '.js-homepage-drop',
		snapMode: 'both',
	});

	$('.js-homepage-drop').droppable({
		accept: '.js-homepage-drag',
		activate: function(){
			$(this).removeClass('js-ui-homepage-drop').addClass('js-ui-homepage-drop-active');
		},
		deactivate: function(){
			$(this).removeClass('js-ui-homepage-drop-active').addClass('js-ui-homepage-drop');
		},
		drop: function(e, ui)
		{
			var page_id = $(this).parents('tr').attr('id').replace('page-', '');
			var notify_hp_id = Linko.notify.show('Setting Homepage...', {
				type: 'flash',
				duration: 0
			})

			Linko.ajax.post('page/setHomepage', {id: page_id}, function(data){
				Linko.notify.update(notify_hp_id, 'Homepage Updated.', {
					duration: 2000
				});
			});
		}
	});
});