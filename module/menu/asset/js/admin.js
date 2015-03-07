$(function(){
	$('table#menu-item-list tbody').sortable({
		helper: function(e, ui){
			ui.children().each(function(){
				$(this).width($(this).width());
			});
			return ui;
		},
		containment: 'window',
		cursor: 'move',
		update: function(e, ui){
			var mo = {}; // mo = menu order

			$(this).find('tr').each(function(i, e){
				var mi = $(e).attr('id').replace('menu-item-', ''); // mi = menu_id
				mo[mi] = i;
			});

			var update_menu_flash = Linko.notify.show('Updating', {type: 'flash', duration: 0});

			$.ajax({
				dataType: 'json',
				type: 'POST',
				data: {
					action: 'menu/updateOrder',
					order: mo
				},
				complete: function(data){
					Linko.notify.update(update_menu_flash, 'Menu Order Updated', {duration: 2000});
				}
			});
		}
	}).disableSelection();

});