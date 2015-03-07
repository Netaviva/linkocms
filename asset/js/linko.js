/**
 * Core Js File
 *
 * @author LinkoDEV Team
 * @package linkocms
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */

;
(function(win, $) {
	var $App = {}, $win = $(win), $doc = $(document),
	Linko = {
		cache:{

		},

		init: function()
		{
			$.ajaxSetup({
				url:Linko.config.get('ajax_url'),
				data:
				{
					csrf:''
				}
			});

			$.each($App, function(index, value)
			{
				this(this);
			});

			$(".linko-ajax-form").each(function(i)
			{
				$this = $(this);

				$this.on('submit', function(e)
				{
					$button = $this.find('input[type=submit], button[type=submit]');

					Linko.process.show('add_dashboard_item', $button);

					$button.attr('disabled', true);

					var param = $.param(
						$this.formToArray()
					);

					var callback = $this.data('callback');

					Linko.ajax.call($this.data('action'), param, function(response)
					{
						Linko.callback.invoke(callback, response);

						$button.attr('disabled', false);

						Linko.process.remove('add_dashboard_item');

					}, $this.data('method'), $this.data('type'));

					return false;
				});
			});

			$("[data-modal]").each(function(i) {
				var $content = $('#' + $(this).attr('data-modal') + '-modal'),
					$self = $(this), options = {};
				$content.hide();
				options.modalType = $content.data('modaltype') || 'content';
				options.title = $content.attr('title');

				if(options.modalType == 'form') {
					options.formAction = $content.data('action') || location.hash;
					options.formMethod = $content.data('method') || 'post';
					options.dataType = $content.data('type') || 'json';
					options.submit = $self.attr('id');
					options.callback = $content.data('callback');
					options.formAttributes = $content.get(0).attributes;
				}

				$(this).on('click', function(e) {
					e.preventDefault();
					Linko.modal.show($content.clone().html(), options);
				});
			});
		}
	};

	Linko.callback = {
		store: {},
		register: function(id, callback)
		{
			this.store[id] = callback;
		},
		has: function(id)
		{
			return !!(this.store.hasOwnProperty(id));
		},
		get: function(id)
		{
			return this.has(id) ? this.store[id] : function()
			{
				throw new Error("Trying to get an unregistered callback: " + id);
			};
		},
		invoke: function(id)
		{
			var args = _.toArray(arguments); args.shift();
			return this.get(id).apply(this, args);
		}
	};

	Linko.modal = {
		content:{
			template:'<div id="modal-content"><%= content %></div>'
		},
		form:{
			template:'<form action="<%= action %>" method="<%= method %>" <%= attributes %>>' +
				'<div id="modal-content-wrap">' +
					'<h3 id="modal-title"><%= title %></h3> ' +
					'<div id="modal-content"><%= content %></div>' +
				'</div>' +
				'<div id="modal-footer">' +
					'<div id="modal-footer-inner">' +
					'<input id="modal-close" class="modal-close button button-secondary" type="button" value="Close" />  ' +
					'<input id="modal-form-submit" class="button" type="submit" name="<%= submit %>" value="Submit" />' +
					'</div>' +
				'</div>' +
				'</form>'
		},

		show:function(data, options)
		{
			this.close();

			// set default options
			options = $.extend({
				modalType:'content', // content | form
				formAction: '',
				formMethod: 'post',
				formAttributes: '',
				title:'', // title of modal
				dataType: 'json',
				submit:'',
				autoCloseInterval:3000,
				callback: ''
			}, options);

			var html = _.template(this[options.modalType].template, {
				content    : data,
				title      : options.title,
				action     : options.formAction,
				method     : options.formMethod,
				attributes : options.formAttributes,
				submit     : options.submit
			});

			var callback = options.callback;

			if((typeof callback == 'string'))
			{
				if(Linko.callback.has(callback))
				{
					// if the callback has been registered with the
					// Linko callback manager, we use it
					callback = Linko.callback.get(callback);
				}
				else
				{
					if(typeof callback == 'string')
					{
						callback = window[options.callback];
					}

					if(typeof callback != 'function')
					{
						callback = function(response)
						{
							this.container.find('#modal-content').html(response);
						};
					}
				}
			}

			options = $.extend({
				overlayId:'modal-overlay',
				dataId:'modal-data',
				closeClass:'modal-close',
				containerId: 'modal-container',
				minHeight:260,
				minWidth:520,
				overlayClose:true,
				autoResize:true,
				onShow:function(modal)
				{
					Linko.modal.setHeight(modal);

					if(options.modalType == 'form') {

						if(options.formAction.substr(0, 7) != 'http://')
						{
							modal.container.find('form').on('submit', function(e)
							{
								Linko.process.show('form', modal.container, {
									mask: true
								});

								var param = $.param(
									$(this).formToArray()
								);

								Linko.ajax.call(options.formAction, param, function(response)
								{
									callback.call(modal, response);

									window.setTimeout(function(){
										Linko.modal.close();
									}, options.autoCloseInterval);

								}, options.formMethod, options.datatype);

								return false;
							});
						}
					}
				}
			}, options);

			$.modal(html, options);
		},
		close:function() {
			$.modal.close();
		},
		setHeight:function(modal)
		{
			var wH = $win.height(), cH = (
				modal.data.height() + $('#modal-footer').height() + 30
			);

			if(cH >= $doc.height())
			{
				cH = ($doc.height() - 60);
			}

			modal.container.css('min-height', cH + 'px');

			$.modal.setPosition();
		}
	};

	Linko.config = {
		get:function(ref) {
			return Config.hasOwnProperty(ref) ? Config[ref] : null;
		},
		set:function(ref, value) {
			Config[ref] = value;
		}
	};

	Linko.translate = {
		get:function(ref, param) {
			if(Translation.hasOwnProperty(ref)) {
				var translated = Translation[ref];
				if(param && (typeof param == 'object')) {
					for(key in param) {
						translated = translated.replace('{' + key + '}', param[key]);
					}
				}

				return translated;
			}

			return null;
		},
	};

	Linko.util = {
		slugify:function(value, separator) {
			separator = separator || '-';
			value = $.trim(value);
			var slug;

			if(!(/[a-z]|[A-Z]|[0-9]||[áàâąбćčцдđďéèêëęěфгѓíîïийкłлмñńňóôóпúùûůřšśťтвýыžżźзäæœчöøüшщßåяюжαβγδεέζηήθιίϊκλμνξοόπρστυύϋφχψωώ]/.test())) {
				slug = value;
			}
			else {
				slug = value.replace(/[^a-zA-Z0-9\/_|+ -]/g, '')
					.toLowerCase()
					.replace(/[\/_|+ -]+/g, separator);
			}

			return slug;
		},
		timeAgo:function(time) {
			var Diff = (Math.round(new Date().getTime() / 1000) - time);
			var Seconds = Math.round(Math.abs(Diff));
			if(Seconds < 60) {
				if(Seconds < 10) {
					return Linko.translate.get('date.just_now');
				}
				return Linko.translate.get('date.x_seconds_ago', {x:Seconds});
			}
			var Minutes = Math.floor(Seconds / 60);
			if(Minutes < 60) {
				if(Minutes == 1) {
					return Linko.translate.get('date.a_minute_ago');
				}
				return Linko.translate.get('date.x_minutes_ago', {x:Minutes});
			}
			var Hours = Math.floor(Minutes / 60);
			if(Hours < 24) {
				if(Hours == 1) {
					return Linko.translate.get('date.an_hour_ago');
				}
				return Linko.translate.get('date.x_hours_ago', {x:Hours});
			}
			return false;
		},
		resizeTextarea: function($selector, options){

			var $this = $(this);
			options = $.extend({}, {
				intervalHeight: 15,
				maxHeight: 0
			}, options);

			var $checkHeight = $('#cache-textarea-info')

			if($checkHeight.length == 0){
				$checkHeight = $('<div />', {
					id: 'cache-textarea-info',
					style: 'display: none;'
				})
				.css({
					position: 'absolute',
					top: -10000,
					left: -10000,
					width: $selector.width(),
					fontSize: $selector.css('fontSize'),
					fontFamily: $selector.css('fontFamily'),
					lineHeight: $selector.css('lineHeight'),
				})
				.appendTo('body');
			}

			var val = $selector.val().replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/&/g, '&amp;')
				.replace(/\n/g, '<br/>');

			$checkHeight.html(val);
			$selector.css('height', Math.max($checkHeight.height() + options.intervalHeight, $selector.height()) + 'px');
		}
	};

	Linko.notify = {
		count:0,
		timer:{},
		factory:{},
		types:{
			sticky:{
				effect:{
					open:function(wrapper, callback) {
						wrapper.fadeIn(callback);
					},
					close:function(wrapper, callback) {
						wrapper.animate({opacity:0}, 4000, callback);
					}
				},
				callback:{},
				template:'<span class="notify-message"></span><span class="notify-close"></span>'
			},
			toast:{
				effect:{
					open:function(wrapper, fn) {
						wrapper.fadeIn(300);
					},
					close:function(wrapper, callback) {
						wrapper.animate({opacity:0}, 500, callback);
					}
				},
				callback:{},
				template:'<span class="notify-message"></span><span class="notify-close"></span>'
			},
			flash:{
				effect:{
					open:function(wrapper, callback) {
						wrapper.slideDown(300, callback);
					},
					close:function(wrapper, callback) {
						wrapper.slideUp(callback);
					}
				},
				callback:{},
				template:'<span class="notify-message"></span><span class="notify-close"></span>'
			}
		},
		show:function(msg, options) {
			this.count++;
			var id = this.count,
				wrapper;

			this.factory[id] = this._create(id, msg, options);

			$(document).bind(['linkoNotifyOpen' + id, 'linkoNotifyUpdate' + id].join(' '), $.proxy(function(event) {
				var wrapper = this.factory[id].wrapper,
					options = this.factory[id].options,
					parent = this.factory[id].parent, eventType;

				$.each(this.position, function(i, fn) {
					wrapper.removeClass('notify-position-' + i.replace('_', '-'));
				});

				if(!parent) {
					wrapper.addClass('notify-position-' + options.gravity.replace('_', '-'))
						.css('position', 'fixed');
				}

				wrapper.hide().appendTo(options.ref);

				this.types[options.type].effect.open.call(this, wrapper, function() {
					options[event.type.replace('linkoNotify', '').replace(id, '').toLowerCase()].call(this, wrapper);
				});

				if(options.duration > 0) {
					this._startTimer(id);

					// only sticky notifications will be restored on mouse over when fading
					if(options.type == 'sticky') {
						wrapper.bind("mouseenter mouseleave", $.proxy(function(event) {
							if(event.type == 'mouseenter') {
								this._restore(id);
							}
							else {
								this._startTimer(id);
							}
						}, this));
					}
				}

				if(options.type == 'toast' && (fn = this.position[options.gravity])) {
					fn.call(this, wrapper);
				}

				// execute callback open()

			}, this)).trigger('linkoNotifyOpen' + id);

			return id;
		},

		/**
		 * Call this method if you want to reposition
		 * notification elements on window resize
		 */
		reposition:function() {
			$win.resize($.proxy(function() {
				$.each(this.factory, $.proxy(function(i, item) {
					if(fn = this.position[item.options.gravity]) {
						fn.call(this.position[item.options.gravity], item.wrapper);
					}
				}, this));
			}, this));
		},

		/**
		 * Gets a notification object ()
		 *
		 * @param id
		 * @return {*}
		 */
		get:function(id) {
			return this._exists(id) ? this.factory[id] : null;
		},

		update:function(id, msg, options) {
			var notify;
			if(this._exists(id)) {
				this.get(id).options = $.extend(this.get(id).options, options);
				if(msg) {
					this.get(id).wrapper.find('> .notify-message').html(msg).end();
				}
				this._restore(id);
				$(document).trigger('linkoNotifyUpdate' + id);
				return true;
			}
			return false;
		},

		close:function(id) {
			if(id) {
				this._close(id);
			}
			else {
				// no id, lets close all notifications
				$.each(this.factory, $.proxy(function(i, $item) {
					this._close(i);
				}, this))
			}
		},

		_create:function(id, msg, options) {
			var options = $.extend({
				id:'notify-item-' + id,
				type:'toast', // toast, sticky, flash
				duration:2000,
				gravity:'center',
				ref:'body',
				open:function() {
				},
				close:function() {
				},
				update:function() {
				}
			}, options);

			var item = $('<div />', {
				class:'notify-' + options.type + '-container',
				id:options.id
			})
				//.css({position: 'fixed'})
				.append(this.types[options.type].template)
				.find('> .notify-message')
				.html(msg).end();

			var $wrapper;

			// sticky
			if(options.type == 'sticky') {
				if(options.gravity != 'left' && options.gravity != 'right') {
					options.gravity = 'right';
				}

				var wrapper_id = '#notify-sticky-wrapper-' + options.gravity.replace('_', '-');

				// create a wrapper to contain all sticky notifications
				if($(wrapper_id).length == 0) {
					$('<div />', {
						id:'notify-sticky-wrapper-' + options.gravity,
						class:['notify-sticky-wrapper', 'notify-position-' + options.gravity].join(' ')
					}).appendTo('body').css({position:'fixed'}).end();
				}

				$wrapper = $(wrapper_id);

				// update the container reference to the sticky wrapper
				options.ref = $wrapper;

				this.position[options.gravity].call(this, $wrapper);
			}

			// flash
			if(options.type == 'flash') {
				if(options.gravity != 'top' && options.gravity != 'bottom') {
					options.gravity = 'top';
				}

				var wrapper_id = '#notify-flash-wrapper-' + options.gravity.replace('_', '-');

				if($(wrapper_id).length == 0) {
					$('<div />', {
						id:'notify-flash-wrapper-' + options.gravity,
						class:['notify-flash-wrapper', 'notify-position-' + options.gravity].join(' ')
					}).appendTo('body').css({position:'fixed'}).end();
				}

				$wrapper = $(wrapper_id);

				// update the container reference to the sticky wrapper
				options.ref = $wrapper;

				this.position[options.gravity].call(this, $wrapper);
			}

			return {
				options:options,
				wrapper:item,
				parent:$wrapper
			};
		},

		_startTimer:function(id) {
			var time = this.factory[id].options.duration;
			if(this.timer[id]) {
				clearTimeout(this.timer[id]);
			}
			this.timer[id] = window.setTimeout($.proxy(function() {
				this.close(id);
			}, this), time);
		},

		_restore:function(id) {
			window.clearTimeout(this.timer[id]);
			this.get(id).wrapper.stop(true).css({opacity:1});
		},

		_exists:function(id) {
			return !!(this.factory.hasOwnProperty(id));
		},

		_close:function(id) {
			if(!this._exists(id)) {
				return;
			}

			var options = this.get(id).options,
				wrapper = this.get(id).wrapper;

			this.types[options.type].effect.close.call(this, wrapper.stop(true), function(i) {
				wrapper.remove();
				options.close.call(this, wrapper)
				delete Linko.notify.factory[id];
			});
		},

		position:{
			top:function(e) { // flash
				e.css('top', 0);
				e.css('left', 0);
			},
			bottom:function(e) { // flash
				e.css('bottom', 0);
				e.css('left', 0);
			},
			right:function(e) { // sticky
				e.css('top', 0);
				e.css('left', ($win.width() - e.outerWidth() - parseInt(e.css('margin-right'))) + 'px');
			},
			left:function(e) { // sticky
				e.css('top', 0);
				e.css('left', parseInt(e.css('margin-left')) + 'px');
			},
			center:function(e) {
				e.css('top', ($win.height() / 2 - e.outerHeight() / 2) + 'px');
				e.css('left', ($win.width() / 2 - e.outerWidth() / 2) + 'px');
			},
			center_left:function(e) {
				e.css('top', ($win.height() / 2 - e.outerHeight() / 2) + 'px');
				e.css('left', 0);
			},
			center_right:function(e) {
				e.css('top', ($win.height() / 2 - e.outerHeight() / 2) + 'px');
				e.css('left', ($win.width() - e.outerWidth() - parseInt(e.css('margin-right'))) + 'px');
			},
			top_center:function(e) {
				e.css('top', 0);
				e.css('left', ($win.width() / 2 - e.outerWidth() / 2) + 'px');
			},
			top_left:function(e) {
				e.css('top', 0);
				e.css('left', parseInt(e.css('margin-left')) + 'px');
			},
			top_right:function(e) {
				e.css('top', 0);
				e.css('left', ($win.width() - e.outerWidth() - parseInt(e.css('margin-right'))) + 'px');
			},
			bottom_center:function(e) {
				e.css('top', ($win.height() - e.outerHeight() - parseInt(e.css('margin-bottom'))) + 'px');
				e.css('left', ($win.width() / 2 - e.outerWidth() / 2) + 'px');
			},
			bottom_right:function(e) {
				console.log(parseInt(e.css('margin-bottom')));
				e.css('top', ($win.height() - e.outerHeight() - parseInt(e.css('margin-bottom'))) + 'px');
				e.css('left', ($win.width() - e.outerWidth() - parseInt(e.css('margin-right'))) + 'px');
			},
			bottom_left:function(e) {
				e.css('top', ($win.height() - e.outerHeight() - parseInt(e.css('margin-bottom'))) + 'px');
				e.css('left', 0);
			}
		}
	};

	Linko.process = {
		store:{},
		show:function(id, elem, options)
		{
			options = $.extend({
				image:Linko.config.get('asset_image') + 'loader/small.gif',
				center:false,
				mask: false,
				method:'appendTo'
			}, options);

			var $loading = $('' +
				'<span class="linko-process-loader ' + id + '">' +
				'<img src="' + options.image + '" /></span>'
			);

			if(options.mask)
			{
				var dimensions = {
					x: elem.width(),
					y: elem.height()
				};

				var overlay = $('<div></div>')
					.attr('id', 'linko-process-overlay-' + id)
					.addClass('linko-process-overlay')
					.css({
						display: 'block',
						height: dimensions.y,
						width: dimensions.x,
						position: 'absolute',
						left: 0,
						top: 0,
						zIndex: 9999999,
					});

				$('<div />').css({
						position: 'relative',
					}).prependTo(elem).append(overlay);

				$loading.css({
					position: 'absolute',
					top: dimensions.y / 2,
					left: dimensions.x / 2
				});

				elem = overlay;
			}

			this.store[id] = $loading[options.method](elem);

			if(options.center)
			{
				$loading.css({
					display:'block',
					marginTop:(elem.height() / 2) - ($loading.height() / 2),
					textAlign:'center'
				});
			}
		},

		remove:function(id) {
			this.store[id].remove();
			delete this.store[id];
		}
	};

	Linko.cookie = {
		get:function(name) {
			name = Linko.config.get('cookie_prefix') + name;
			var cookies = document.cookie.split('; '), length = cookies.length;

			for(var i = 0; i < length; i++) {
				var parts = cookies[i].split('=');
				if(parts[0].replace(/^\s+|\s+$/g, '') == name) {
					return parts[1].replace(/^\s+|\s+$/g, '')
				}
			}

			return null;
		},
		set:function(name, value, expire) {
			var today = new Date();
			today.setTime(today.getTime());

			if(value === null) {
				expire = -1;
			}

			expire = new Date(today.getDate() + (expire));

			document.cookie = (Linko.config.get('cookie_prefix') + name) + '=' + encodeURIComponent(value) +
				((expire) ? '; expires=' + expire.toUTCString() : '') +
				((Linko.config.get('cookie_path')) ? '; path=' + Linko.config.get('cookie_path') : '') +
				((Linko.config.get('cookie_domain')) ? '; domain=' + Linko.config.get('cookie_domain') : '');
		},
		delete:function(name) {
			if(this.get(name)) {
				this.set(name, null);
				return true;
			}

			return false;
		}
	};

	Linko.ajax = {
		post:function(action, param, callback, type) {
			this.call(action, param, callback, 'POST', type);
		},

		get:function(action, param, callback, type) {
			this.call(action, param, callback, 'GET', type);
		},

		call:function(action, param, callback, method, type) {
			$.ajax({
				dataType:type,
				type:method,
				success:callback,
				data:{
					action:action,
					param:param
				}
			});
		}
	};

	Linko.tabilize = function($buttons, $data) {
		$data = $data || $('> div');
		$buttons.click(function(e) {
			$buttons.parent().removeClass('active');
			$(this).parent().addClass('active');
			$data.hide().filter(this.hash).show();
			return false;
		}).filter(':first').click();
	};

	$(function() {
		var date = (new Date).getTime();
		Linko.init();
	});

	win.Linko = Linko;
	win.$App = $App;
})(window, jQuery);