<?php
header("Content-Type: text/css");
$changedDir = '';
if (!$changedDir)$changedDir = preg_replace('|wp-content.*$|','', __FILE__);
include_once($changedDir.'/wp-load.php');
$toget=array('fwpopup_width'=>'365px','fwpopup_height'=>'285px','fwpopup_bgcolor'=>'#ffffff','fwpopup_bordercolor'=>'#99CCFF');
if($toget)
{
	foreach($toget as $key=>$value)
	{
	$$key=(get_option($key))? get_option($key):$value;
	}
}

?>

.advertisement_popupbox{



 background-color: <?php echo $fwpopup_bgcolor;?>;

    border: 3px solid <?php echo $fwpopup_bordercolor;?>;

   border-radius: 5px 5px 5px 0;

    bottom: -2px;

    float: right;

    height: <?php echo $fwpopup_height;?>;

    position: fixed;

    right: -400px;

    width: <?php echo $fwpopup_width;?>;

    padding: 10px

}



.advertisement_popupbox_close{

  background-image: url("minus.png");

    color: #FF0000;

    cursor: pointer;

    display: inline;

    height: 16px;

    left: 3px;

    position: absolute;

    text-decoration: none;

    text-indent: -999em;

    top: 5px;

    width: 16px;

}

.advertisement_popupbox_max

{

	 background-image: url("plus.png");

	color: #FF0000;

    cursor: pointer;

    display: inline;

    height: 16px;

    left: 3px;

    position: absolute;

    text-decoration: none;

    text-indent: -999em;

    top: 5px;

    width: 16px;

	display:none;

}



/*------------------------------------......Popup-Css-Close.....---------------------------*/







.adv_popup_heading{



 font-family: Verdana;



    font-size: 13px;



    line-height: 20px;



    margin-top: 15px;



    padding: 10px;



}


