/**
 * @author LinkoDEV team
 */
jQuery(function(){
	var page_id = $.trim($('#page-id').text());

	$('select#page-select').change(function(){
		window.location = $(this).find('option:selected').val();
	});

	$('.component-item').draggable({
		revert : 'invalid',
		helper: 'clone',

		start : function(e, ui){
			$(this).addClass('component-drag')
			$(ui.helper).css({
				'width': $('.block-position-blocks').width(),
				'z-index': 99999
			});
		},

		stop: function() {
			$(this).removeClass('component-drag');
		}
	});

	$('.block-position-blocks ol').sortable({
		cancel: '.skip-sortable',
		helper: 'clone',
		cursor: 'move',
		start: function(){

		},
		update: function(e, ui){
			var block_order = {};
			$(this).find('li.block-item').each(function(index, elem){
				var block_id = $(elem).attr('id').replace('block-', '');
				block_order[block_id] = index;
			});

			var notify_order_block = Linko.notify.show('Updating Block Order.', {
				type: 'flash',
				duration: 0
			});

			Linko.ajax.post('block/updateBlockOrder', {order: block_order}, function(){
				Linko.notify.update(notify_order_block, 'Block Order Updated', {duration: 2000});
			}, 'json');
		}
	});

	$('.block-position').droppable({
		accept : '.component-item',
		tolerance: 'pointer',

		drop: function(e, ui){
			Linko.process.show('drop-block', $(this), {
				center: true,
				method: 'appendTo'
			});

			var $self = $(this);
			var component_id = ui.draggable.attr('id').replace('component-', '');

			Linko.ajax.get('block/getBlockForm', {component_id: component_id}, function(form){
				Linko.process.remove('drop-block');
				handle_block_assign_form($self, $self.find('.block-position-blocks ol').append('<li class="block-form skip-sortable">' + form + '</li>').hide().slideDown().find('form'), component_id);
			}, 'html');
		}
	});

	$(document).on('click', 'a.block-edit', function(e){
		e.preventDefault();
		var $block = $(this).parents('li');
		var id = $block.attr('id').replace('block-', '');
		Linko.process.show('edit-link-click', $block.find('> div'));

		Linko.ajax.get('block/getBlockForm', {block_id: id}, function(form){
			Linko.process.remove('edit-link-click');
			var orig = $block.html();
			handle_block_edit_form($block, $block.addClass('block-edit-form skip-sortable').html(form).hide().slideDown().find('form'), id, orig);
		}, 'html');
	});

	$(document).on('click', 'a.block-remove', function(e){
		e.preventDefault();
		var $block = $(this).parents('li');
		var id = $block.attr('id').replace('block-', '');
		Linko.process.show('remove-block-link-click', $block.find('> div'));

		Linko.ajax.post('block/deleteBlock', {block_id: id}, function(response){
			Linko.process.remove('remove-block-link-click');
			$block.remove();
			Linko.notify.show(response.message);
		}, 'json');
	});

	function handle_block_assign_form($position, $form, component_id){

		var $container = $form.parents('li');
		var $cancel = $form.find('a.cancel');
		var $submit = $form.find('input[type="submit"]');

		$cancel.on('click', function(e){
			e.preventDefault();
			$container.slideUp(function(){
				handle_block_remove($container, 'assign');
			});
		});

		$submit.on('click', function(e){
			e.preventDefault();
			Linko.process.show('assign-form-save', $form.find('.control-bottom'));
			$(this).attr('disabled', 'disabled');

			var data = $form.serialize('param');
			var position = $position.attr('id').replace('position-', '');

			Linko.ajax.post('block/assignBlock', {
				position: position,
				page_id: page_id,
				component_id: component_id,
				form: data
			}, function(response){
				Linko.process.remove('assign-form-save');
				if(response.success){
					var $block_item = $('<li id="block-' + response.block.block_id + '" class="block-item"></li>');
					$block_item.html($('.drop-block-helper').get(0).innerHTML);
					$block_item.find('span.block-label').text(response.block.component_label);
					$block_item.find('span.block-title').text(response.block.block_title);
					$container.fadeOut(50, function(){
						handle_block_remove($(this), 'assign');
					});

					$block_item.appendTo($position.find('.block-position-blocks ol'));

					Linko.notify.show(response.message);
				}
				else{
					$submit.removeAttr('disabled');
				}
			}, 'json');
		});
	};

	function handle_block_edit_form($block, $form, block_id, orig){
		var $cancel = $form.find('a.cancel');
		var $submit = $form.find('input[type="submit"]');

		$cancel.on('click', function(e){
			e.preventDefault();
			$form.slideUp(function(){
				$(this).remove();
				$block.removeClass('block-edit-form skip-sortable');
				$block.html(orig);
			});
		});

		$submit.on('click', function(e){
			e.preventDefault();
			Linko.process.show('save-block-form', $form.find('.control-bottom'));
			$(this).attr('disabled', 'disabled');
			var data = $form.serialize('param');

			Linko.ajax.post('block/updateBlock', {
				block_id:block_id,
				form:data
			}, function(response) {
				Linko.process.remove('save-block-form');
				if(response.success) {
					$form.slideUp(function() {
						$block.removeClass('block-edit-form skip-sortable');
						$block.html(orig);
						$block.find('span.block-label').text(response.block.component_label);
						$block.find('span.block-title').text(response.block.block_title);
						Linko.notify.show(response.message);
					});
				}
				else {
					$submit.removeAttr('disabled');
				}
			}, 'json');
		});
	}

	function handle_block_remove($container, action){
		action == 'assign'
			? $container.remove()
			: $container.hide();

	}
});