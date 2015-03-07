(function($){
	$(function(){
		$('.js-color-picker').ColorPicker({
			livePreview: false,
			onSubmit: function(hsb, hex, rgb, el) {
				$("input[name='sett[" + el.id  + "]']").val('#' + hex.toUpperCase());
				$(el).find('> div').css('background-color', '#' + hex);
				$(el).ColorPickerHide();
			},
			onChange: function(hsb, hex, rgb, el){

			},
			onBeforeShow: function () {
			}
		});

		$('.js-color-form').bind('keyup', function(){
			$('#' + this.name.replace('sett[', '').replace(']', '')).find('> div').css('backgroundColor', this.value);
		});
	});
})(jQuery);