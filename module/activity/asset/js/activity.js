var $Activity = {};

var $LoadMorePage = 2;

$Activity.loadMore = function() {
	$('.activity-view-more').click(function(e) {
		$LoadMorePage++;

		var $this = $(this);

		var text = $this.text();

		$this.html('');

		Linko.process.show('activity_load_more', $(this), {
			method:'appendTo',
			center:true
		});

		var param = {
			page:$this.attr('id').replace('activity-view-more-', ''),
			user_id:$('#js-user-id').text()
		};

		Linko.ajax.get('activity/loadMore', param, function(response) {
			Linko.process.remove('activity_load_more');

			if($.trim(response) != '') {
				$this.attr('id', 'activity-view-more-' + $LoadMorePage).text(text);

				$('#activity-load-more-js-wrapper').append(response);
			}
			else {
				$this.text(Linko.translate.get('activity.no_more_activity'));
			}
		});

		return false;
	});
};

$Activity.initComment = function() {
	$(document).on('click', '.activity-extra-comment > a', function(e) {
		var $form = $(this.hash);

		$form.find('textarea').focus();

		$form.slideDown();

		return false;
	});

	$(document).on('keypress', '.activity-comments textarea', function(e) {
		var $textarea = $(this),
			$activity = $textarea.parents('.activity-comments')
		id = $activity.attr('id').replace('activity-comment-', '');

		if(e.keyCode == 13 && !e.shiftKey) {
			// console.log(id);
			var comment = $textarea.val();

			$textarea.attr('disabled', 'disabled');

			Linko.process.show('post_comment_' + id, $textarea, {method:'insertAfter'});

			var param = {
				comment:comment,
				activity_id:id
			};

			Linko.ajax.post('activity/addComment', param, function(response) {
				$textarea.removeAttr('disabled');

				Linko.process.remove('post_comment_' + id);

				if(response.error) {
					return Linko.notify.show(response.message, {});
				}

				if(response) {
					var comment = $(response).appendTo($activity.find('.activity-js-comment-new-block')).get(0);

					$(comment).hide().fadeIn();

					$Activity.updateTimer();

					$textarea.val('');

					Linko.notify.show(Linko.translate.get('activity.comment_added'), {duration:2000});
				}
			});
		}
	});
};

$Activity.updateTimer = function(elem, time) {
	var _int = {};

	if(activity_live_timeupdate) {
		$('.activity-time-created, .activity-comment-time').each(function(i, e) {
			var time = $(this).attr('data-time'), $self = $(this), timeago;

			_int[i] = window.setInterval(function() {
				if(timeago = Linko.util.timeAgo(time)) {
					$self.html(timeago);
				}
				else {
					window.clearInterval(_int[i]);
				}
			}, 1000);
		});
	}
};

$Activity.initStatus = function() {
	var default_value;

	// First, lets hide other toolbox items
	$('.activity-feed-item').not('#activity-feed-item-status').removeClass('selected').hide();

	$('#activity-feed-item-status').find('textarea').on('focus', function() {
		var $this = $(this);

		$this.css({
			height:'60px',
			border:'1px solid #FFBD59'
		});
	});

	$('#activity-feed-toolbox').find('li > a').on('click', function(e) {
		$('.activity-feed-item').hide();

		$('#activity-feed-toolbox li').removeClass('active');

		$(this).parents('li').addClass('active');

		$($(this).attr('href')).show();

		$('#activity-ajax-action').val($.trim($(this).attr('rel')));

		return false;
	});
};

$Activity.processStatusAdd = function() {
	$('#activity-feeds-form').find('form').on('submit', function(e) {
		e.preventDefault();

		var ajaxAction = $('#activity-feed-toolbox').find('li.active > a').attr('rel'),
			$form_btn = $('#activity-feeds-button-post'),
			$textarea = $(this).find('#activity-feed-item-status textarea');

		Linko.process.show('activity_add_status', $form_btn, {
			method:'insertBefore'
		});

		var param = {
			val:$(this).serialize()
		};

		$form_btn.attr('disabled', true);
		$textarea.attr('disabled', true);

		Linko.ajax.post(ajaxAction, param, function(response) {
			Linko.process.remove('activity_add_status');

			$form_btn.removeAttr('disabled');
			$textarea.attr('disabled', false);

			$textarea.val('');

			if(response.error) {
				return Linko.notify.show(response.message, {});
			}

			if(response) {
				var activity = $(response).prependTo(document.getElementById('activity-js-new-block')).get(0);

				$(activity).hide().fadeIn();

				// remove the "No activity" message if found
				$('#activity-wrapper').find('.no-item').remove();

				$Activity.updateTimer();
			}
		});

		return false;
	});
};


$App.activity = (function() {

	$Activity.initStatus();

	$Activity.processStatusAdd();

	$Activity.updateTimer();

	$Activity.initComment();

	$Activity.loadMore();
});
