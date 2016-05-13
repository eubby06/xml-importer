<?php

include(WP_CONTENT_DIR . "/lib/es_image_watermark.php");

class EXI_Property_Class
{
	protected static $propertyTable = 'estatik_properties';
	protected static $propertyMetaTable = 'estatik_properties_meta';
	protected static $postTable = 'posts';
	protected $defaults = array();
	protected $propertyFields = array();

	protected $objects = array();

	public function __construct()
	{
		include(EXI_DIR_PATH . 'defaults.php');

		$this->propertyFields = $propertyFields;
		$this->defaults = $defaults;
	}

	public function loadObjects($objects)
	{
		
		$this->objects = $objects;

	}

	public function process()
	{
		$count = 0;

		foreach($this->objects as $obj)
		{
			//return obj with id from post
			$obj = $this->createPost($obj);

			//now create the property with the post id
			$result = $this->createProperty($obj);


			if($result)
			{
				$downloadedImages = $this->downloadImages($obj->photos);

				$obj->photos = $downloadedImages;
				$this->createPropertyMeta($obj);
				
				$count++;
			}
		}

		return $count;
	}

	public function downloadImages($photos)
	{
		$downloadedImages = array();

		if(!empty($photos))
		{
			foreach($photos as $url)
			{
				$filename = $this->downloadImage($url);
				$downloadedImages[] = $filename;
 			}
		}

		return $downloadedImages;
	}

	public function downloadImage($url)
	{
		$nameBits = explode('/', $url);

		$upload_dir = wp_upload_dir(); 
		$img = $this->imageCreateFromAny($url);
		$filename =  time() . '_' . end($nameBits);
		$path = $upload_dir['path'] . '/' . $filename;

		imagejpeg($img, $path, 100);
		$this->imageCropper($filename);

		return $upload_dir['subdir']."/".$filename;
	}

	public function imageCropper($filename)
	{
		$exi_settings = exi_front_settings();
		$upload_dir = wp_upload_dir(); 
		$targetPath = $upload_dir['path'] . '/' . $filename;
		$save_image_array = array();

		//apply watermark to this image :: ADDED
		app_watermark($targetPath);

		exi_crop($targetPath, $upload_dir['path']."/list_".$filename,$exi_settings->prop_listview_list_width, $exi_settings->prop_listview_list_height);
		exi_crop($targetPath, $upload_dir['path']."/2column_".$filename,$exi_settings->prop_listview_2column_width, $exi_settings->prop_listview_2column_height);
		exi_crop($targetPath, $upload_dir['path']."/table_".$filename,$exi_settings->prop_listview_table_width, $exi_settings->prop_listview_table_height);
		
		exi_crop($targetPath, $upload_dir['path']."/single_lr_".$filename,$exi_settings->prop_singleview_photo_lr_width, $exi_settings->prop_singleview_photo_lr_height);
		exi_crop($targetPath, $upload_dir['path']."/single_center_".$filename,$exi_settings->prop_singleview_photo_center_width, $exi_settings->prop_singleview_photo_center_height);
		exi_crop($targetPath, $upload_dir['path']."/single_thumb_".$filename,$exi_settings->prop_singleview_photo_thumb_width, $exi_settings->prop_singleview_photo_thumb_height);
		 
		$save_image_array[]	=	$upload_dir['subdir']."/".$filename;
	}

	public function imageCreateFromAny($filepath) { 

	    $type = exif_imagetype($filepath); 

	    //1 = gif, 2 = jpg, 3 = png
	    $allowedTypes = array(1, 2, 3); 

	    if (!in_array($type, $allowedTypes)) { 
	        return false; 
	    } 

	    switch ($type) { 
	        case 1 : 
	            $image = imageCreateFromGif($filepath); 
	        break; 
	        case 2 : 
	            $image = imageCreateFromJpeg($filepath); 
	        break; 
	        case 3 : 
	            $image = imageCreateFromPng($filepath); 
	        break; 
	        case 6 : 
	            $image = imageCreateFromBmp($filepath); 
	        break; 
	    }  

	    return $image;  
	} 

	public function createPropertyMeta($obj)
	{
		global $wpdb;

		$data = array();

		$data['prop_id'] = $obj->prop_id;
		$data['prop_meta_key'] = $this->defaults['prop_meta_key'];
		$data['prop_meta_value'] = serialize($obj->photos);

		$result = $wpdb->insert($wpdb->prefix.static::$propertyMetaTable, $data);

		return $result;
	}

	public function createProperty($obj)
	{
		global $wpdb;

		$date = new DateTime();

		$data = array();

		foreach($this->propertyFields as $field)
		{
			if(isset($obj->$field))
			{
				$data[$field] = $obj->$field;
			}
		}

		//set defaults values
		$data['prop_pub_unpub'] = $this->defaults['prop_pub_unpub'];
		$data['prop_date_added'] = $date->getTimestamp();
		$data['prop_type'] = $this->defaults['prop_type'];
		$data['prop_category'] = $this->defaults['prop_category'];
		$data['prop_status'] = $this->defaults['prop_status'];

		$result = $wpdb->insert($wpdb->prefix.static::$propertyTable, $data);

		return $result;
	}

	public function createPost($obj)
	{
		global $wpdb;

		$data = array();

		$data['post_content'] =  $this->defaults['post_content'];
		$data['post_title'] = $obj->prop_title;
		$data['post_status'] = $this->defaults['post_status'];
		$data['post_name'] = str_replace(' ', '-', $obj->prop_title);
		$data['post_type'] = $this->defaults['post_type'];

		$wpdb->insert($wpdb->prefix.static::$postTable, $data);

		//get the id of the new record
		$obj->prop_id = $wpdb->insert_id;

		return $obj;
	}
}