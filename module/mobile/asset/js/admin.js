/**
 * @package Mobile Module
 * @author Morrison Laju
 */

var MobileDashboard =
{
	init : function()
	{
		this.reorderDashboard();

		this.noItemMonitor();

		$(document).on('click', '.mobile-dashboard-action > a', function(e)
		{
			e.preventDefault();

			var $this = $(this),
				$item = $this.parents('li');

			var id = $item.attr('id').replace('dashboard-item-', '');

			if($this.attr('id') == 'mobile-dashboard-edit')
			{
				Linko.modal.show('Loading...', {
					title: 'Loading Data',
					onOpen: function(modal)
					{
						modal.overlay.show();
						modal.container.show();
						modal.data.show();

						Linko.process.show('mobile_edit_item_loading', modal.container, {mask: true});
					}
				});

				Linko.ajax.get('mobile/getDashboardEditForm', {item_id: id}, function(form)
				{
					Linko.process.remove('mobile_edit_item_loading');

					Linko.modal.show(form, {
						modalType : 'form',
						title: 'Edit Dashboard Item',
						formMethod: 'POST',
						formAction: 'mobile/updateDashboardItem',
						formAttributes: 'class="form-horizontal"',
						callback: function(response)
						{
							if(response.error)
							{
								Linko.notify.show(response.data);

								return true;
							}

							var $template = $(MobileDashboard._buildTemplate(response.data));

							$item.replaceWith($template);

							$template.children().effect('highlight', {}, 3000);

							Linko.modal.close();
						}
					});

				}, 'html');
			}
			else
			{
				if(confirm('Are you sure you want to delete this dashboard item?'))
				{
					Linko.process.show('mobile_delete_dashboard_item', $item, {mask: true});

					Linko.ajax.post('mobile/deleteDashboardItem', {item_id: id}, function(response)
					{
						Linko.notify.show("Dashboard Item Deleted!");

						$item.animate({opacity: 0}, 1000, function()
						{
							$item.remove();

							MobileDashboard.noItemMonitor();
						});

					}, 'json');
				}
			}
		});
	},

	reorderDashboard : function()
	{
		$('#js-dashboard-items-wrapper').sortable(
		{
			helper: 'clone',
			containment: 'parent',
			cursor: 'move',
			update: function(e, ui)
			{
				var dashboardOrder = {},
					$items = $(this).find('> li'), length = ($items.length), cnt = 0;

				// Build the Dashboard Order
				while(cnt < length)
				{
					var itemID = $items[cnt].id.replace('dashboard-item-', '');

					dashboardOrder[itemID] = (length - cnt);

					cnt++;
				}

				var updateOrderNotify = Linko.notify.show('Updating Order...', {
					type: 'flash',
					duration: 0
				});

				Linko.ajax.post('mobile/updateDashboardOrder', {order: dashboardOrder}, function()
				{
					Linko.notify.update(updateOrderNotify, 'Dashboard Order Updated', {
						duration: 2000
					});
				});
			}
		}).disableSelection();
	},

	addDashboardItem: function(response)
	{
		if(response.error)
		{
			Linko.notify.show(response.data);

			return true;
		}

		var $template = $(MobileDashboard._buildTemplate(response.data));

		$('#js-dashboard-items-wrapper').prepend($template);

		MobileDashboard.noItemMonitor();

		$template.find('*').effect('highlight', {}, 3000);

		Linko.notify.show('Dashboard Item Added', {
			type: 'toast'
		});
	},

	noItemMonitor: function()
	{
		$noItem = $('.dashboard-list-box').find('.no-item');

		if($('.mobile-dashboard-list').find('> li').length == 0)
		{
			$noItem.show();
		}
		else
		{
			$noItem.hide();
		}
	},

	_buildTemplate: function(data)
	{
		var copy = $('#js-dashboard-item-data-copy').html(), $noItem;

		copy = _.template(copy,
			{
				id    : data.item_id,
				image : data.item_image,
				title : data.item_title,
				page  : data.module_id + " : " + data.page_title,
				link  : data.page_url
			});

		return copy;
	}
};

/**
 * This callback is called when a dashboard item is added.
 */
Linko.callback.register('mobile_add_dashboard_item', MobileDashboard.addDashboardItem);

/**
 * Register method to be executed when dom is loaded.
 */
$App.mobile = function()
{
	MobileDashboard.init();
};