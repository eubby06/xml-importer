<?php


function exi_front_settings(){
	
	global $wpdb;
	$es_settings = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'estatik_settings');
	return $es_settings; 
		
}

function exi_crop($file,$saveAs=NULL,$width=100,$height=100)
{
	if (!class_exists('WideImage'))
		die('"smart_crop" requires "WideImage Library" to be included.');
	if ($saveAs == NULL)
		$saveAs = $file;
	list($w, $h) = getimagesize($file);
	$wpercent = ($w/$width)*100;
	$hpercent = ($h/$height)*100;
	$img = WideImage::load($file);
	if ($wpercent >= 100 && $hpercent >= 100) {
		// Both is over 100% of the allowed size, then resize so the smalle side match.
		if ($wpercent > $hpercent) {
			// height is smallest
			$remove_percent = $hpercent - 100;
		}else{
			// width is smallest
			$remove_percent = $wpercent - 100;
		}
		$new_h_percent = $hpercent - $remove_percent;
		$new_w_percent = $wpercent - $remove_percent;
		$new_w = $width*(1+($new_w_percent-100));
		$new_h = $height*(1+($new_h_percent-100));
		$img = $img->resize($new_w, $new_h, 'inside');
		$img = $img->crop("center", "middle", $width, $height);
	}else {
		// One of them is smaller than 100% of the allowed size
		if ($wpercent > $hpercent) {
			// height is smallest
			$add_percent = 100 - $hpercent;
		}else{
			// width is smallest
			$add_percent = 100 - $wpercent;
		}
		$new_h_percent = $hpercent + $add_percent;
		$new_w_percent = $wpercent + $add_percent;
		$new_w = $width*(1+($new_w_percent-100));
		$new_h = $height*(1+($new_h_percent-100));
		$img = $img->resize($new_w, $new_h, 'inside');
		$img = $img->crop("center", "middle", $width, $height);
	}
	$img->saveToFile($saveAs);
}