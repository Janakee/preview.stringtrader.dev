<?php
global $wpdb,$blog_id;
$table =  TBL_ADS_SORTS; 
if($_POST['ads_sorting_set_settings'])
{
	update_option('ads_sorts_enable_'.$blog_id,$_POST['ads_sorts_enable']);
	update_option('ads_sorts_dropdown_label_'.$blog_id,$_POST['ads_sorts_dropdown_label']);
	update_option('ads_sorts_dropdown_label_select_'.$blog_id,$_POST['ads_sorts_dropdown_label_select']);
	$ads_sorts_result  = $wpdb->get_results("SELECT * FROM $table"); 
	foreach($ads_sorts_result as $ads_sorts_rec): 
	if($_POST['sort_default']==$ads_sorts_rec->sort_id)
	$sort_default_value=1;
	else
	$sort_default_value=0;
	if($_POST['sort_status_'.$ads_sorts_rec->sort_id.''])
	$sort_status_value=1;
	else
	$sort_status_value=0;
    $tbl_sql = "update  $table set sort_label='".addslashes($_POST['sort_label_'.$ads_sorts_rec->sort_id.''])."' , sort_display_order=".$_POST['sort_display_order_'.$ads_sorts_rec->sort_id.''].", sort_status=".$sort_status_value." , sort_default=".$sort_default_value." where sort_id=".$ads_sorts_rec->sort_id." ";
	$wpdb->query($tbl_sql);
	endforeach;
	?>
	<form name="frm" action="" method="get">
	<input type="hidden" name="page" value="tc-ads-sorting" />
	<input type="hidden" name="msg" value="success" />
	</form>
	<script>document.frm.submit();</script>
	<?php
		exit;
}
?>

<div class="wrap">
	<div class="icon32 icon32-posts-blinds" id="icon-edit"><br></div>
	<h2><?php _e('Ads Sorting Settings','appthemes');?></h2>
	<?php if($_REQUEST['msg']=='success'){?>
		<p class="info"><?php _e('Settings Saved Successfully','appthemes');?></p>
	<?php }?>
	<form method="post" action="" name="ads_frm_settings">
		<input type="hidden" name="ads_sorting_set_settings" value="1" />
		<?php
		$ads_sorts_enable = get_option('ads_sorts_enable_'.$blog_id);
		
		?>
		<table cellpadding="5" cellspacing="5">
			
			<tr>
				<td colspan="2">
					<input type="checkbox" name="ads_sorts_enable" value="1" <?php if($ads_sorts_enable){echo 'checked="checked"';}?>  />  <b><?php _e('Enable Sorting Dropdown?','appthemes');?></b>
				</td>
			</tr>
			
			<tr>
				<td><strong><?php _e('Dropdown Label','appthemes');?></strong></td>
				<td><input type="text" size="40" name="ads_sorts_dropdown_label" value="<?php echo stripslashes(get_option('ads_sorts_dropdown_label_'.$blog_id)); ?>" />
					<br /><code><?php _e("eg: Sort By",'appthemes')?></code>
				</td>
			</tr>
			
			<tr>
				<td><strong><?php _e('Dropdown Select Value','appthemes');?></strong></td>
				<td><input type="text" size="40" name="ads_sorts_dropdown_label_select" value="<?php echo stripslashes(get_option('ads_sorts_dropdown_label_select_'.$blog_id)); ?>" />
					<br /><code><?php _e("eg: Select",'appthemes')?></code>
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><hr />
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><h3><?php _e('Dropdown Settings','appthemes');?></h3></td>
			</tr>
			</table>
			<table cellpadding="5" cellspacing="5">
			<tr>
			<td colspan="4"><hr />
			</td>
			</tr>
			<tr>
			<th colspan="2"><?php _e('Drodown Labels', 'appthemes'); ?></th>
			<th><?php _e('Order', 'appthemes'); ?></th>
			<th><?php _e('Default Selected', 'appthemes'); ?></th>
			<th><?php _e('Enable', 'appthemes'); ?></th>
			</tr>
			<?php
			$ads_sorts_result  = $wpdb->get_results("SELECT * FROM $table"); 
			foreach($ads_sorts_result as $ads_sorts_rec): 
			?>
			<tr>
			<td><?php _e($ads_sorts_rec->sort_admin_label, 'appthemes'); ?></td>
			<td><input type="text" size="40" name="sort_label_<?php echo $ads_sorts_rec->sort_id;?>" value="<?php echo stripslashes($ads_sorts_rec->sort_label);?>" /></td>
			<td><input type="text" size="1" name="sort_display_order_<?php echo $ads_sorts_rec->sort_id;?>" value="<?php echo $ads_sorts_rec->sort_display_order;?>" /></td>
			<td><input type="radio" name="sort_default" value="<?php echo $ads_sorts_rec->sort_id;?>" <?php if($ads_sorts_rec->sort_default){?> checked="checked" <?php }?> /></td>
			<td><input type="checkbox" name="sort_status_<?php echo $ads_sorts_rec->sort_id;?>" value="1" <?php if($ads_sorts_rec->sort_status==1){echo 'checked="checked"';}?>  /></td>
			</tr>
			<?php endforeach;?>
			<tr>
			<td colspan="4"><hr />
			</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save Settings','appthemes');?>"></p>
	</form>
	
</div>