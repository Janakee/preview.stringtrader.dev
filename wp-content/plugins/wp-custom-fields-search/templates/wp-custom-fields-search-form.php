<div class="shadowblock_out" id="rb_advcdsrch">
<div class="shadowblock">


<?php if($title && !(@$params['noTitle'])){

	echo $params['before_title'].$title.$params['after_title'];

}?>
	<form method='get' class='<?php echo $formCssClass?>' action='<?php echo $formAction ?>' id='custom_search_form'>
<?php echo $hidden ?>
		<div class='searchform-params' id='searchform-params'>
<?php		foreach($inputs as $input){?>
<div class='<?php echo $input->getCSSClass()?>'><?php echo $input->getInput()?></div>
<?php	}?>
</div>
<div class='searchform-controls'>
<input type='submit' id='submit_button' name='search' value='<?php _e('Search','wp-custom-fields-search')?>'/>
</div>
<script>
jQuery(document).ready(function(){
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	
	var oldValues = '';

	function values_to_string(v){
		var r = [];
		for (var i = 0; i<v.length; i++){
			if (r.length != 0) r.push('&');
			r.push(v[i].name, '=', v[i].value);
		}
		return r.join('');
	}
	
	jQuery('.searchform-params').bind('change', function()
	{
			var controls = jQuery.parseJSON(<?php print json_encode(json_encode($inputs)); ?>);
			var values = jQuery('#searchform-params *').serializeArray();
			
			if (oldValues == values_to_string(values)) return;
			oldValues = values_to_string(values);
			
			var data = {
					action: 'count_posts',
					ctrl: controls,
					val: values
			};

			jQuery.post(ajaxurl, data, function(r){
				var iresult = parseInt(r);
				var submit_btn = jQuery('#submit_button');
				if (iresult == 0)// No results
				{
					 if (submit_btn.is(':disabled')== false)
					 {
						 // TODO: Chage CSS to disabled
						 submit_btn.attr('disabled','disabled');
						 submit_btn.css('opacity', '0.2');
					 }
				}
				else // Some results
				{
					if (submit_btn.is(':disabled')== true)
					{
						// TODO: Change CSS to enabled
						submit_btn.removeAttr('disabled');
						submit_btn.css('opacity', '1.0');
					}
				}
				jQuery('#total_value').fadeOut('fast').html("Total: "+r).fadeIn('fast');
			});
	});
	jQuery('.searchform-params').trigger('change');
});
</script>
<div id='total_value'></div>
</form>
<div class="clr"></div>
</div>
</div>





