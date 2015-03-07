;(function($){
	$(function(){
		var $form = $('#add-comment-form');

		$('a.reply-link').on('click', function(e){
			e.preventDefault();

			$form.find('#comment-form-params').append($('<input />', {
				id: 'js-comment-parent-id',
				name: 'val[parent_id]',
				type: 'hidden',
				value: $(this).parents('.comment-body').attr('id').replace('comment-', '')
			}));

			$form.find('#cancel-comment-reply').show();
			$('#comments-form').find('h4 > small').show();
			$.scrollTo($('#comments-form'), 500);

			return false;
		});

		$('#cancel-comment-reply').on('click', function(){
			var $parent = $('#js-comment-parent-id');
			var $comment = $('#comment-' + $parent.val());
			$parent.remove();
			$('#comments-form').find('h4 > small').hide();
			$.scrollTo($comment, 500);
			$(this).hide();
		});

		$form.submit(function(){
			$('#add-comment-button').val(Linko.translate.get('comment.posting_your_comment')).attr('disabled', true);
		});
	});
})(jQuery);