<?php
/*
Plugin Name: Estatik XML Importer by Boolex
Plugin URI: http://www.boolex.com
Description: Real Estate XML Importer Plugin by Boolex
Version: 1.0.0
Author: Boolex
Author URI: http://www.boolex.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('EXI_DIR_PATH') || define('EXI_DIR_PATH', plugin_dir_path( __FILE__ ));
defined('EXI_UPLOAD_URL') || define('EXI_UPLOAD_URL', wp_upload_dir()['baseurl']);

require_once(EXI_DIR_PATH . 'classes/exi_property_class.php');
require_once(EXI_DIR_PATH . 'classes/exi_parser_class.php');
require_once(EXI_DIR_PATH . 'classes/exi_validator_class.php');
require_once(EXI_DIR_PATH . 'helpers/functions.php');

class Estatik_XML_Importer_Class
{

	public function init()
	{
		add_action('admin_menu', array($this, 'adminRegisterMenuHook'));
		add_action('admin_head', array($this, 'adminURLs'));
		add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScriptsHook'));
		add_action('wp_ajax_new_import', array($this, 'ajaxNewImport')); 
		add_action('wp_ajax_nopriv_new_import', array($this, 'ajaxNewImport'));
	}

	public function adminURLs()
	{
		$pluginUrl = plugins_url('estatik-xml-importer');
    	echo '<script type="text/javascript"> var PLUGIN_URL = "'.$pluginUrl.'"; </script>';
	}

	public function adminRegisterMenuHook()
	{
		add_menu_page( 'Estatik XML Importer', 'Estatik XML Importer', 'manage_options', 'new_import', array($this, 'TemplateNewImport'), plugins_url( 'estatik-xml-importer/icon.png' ), '22' );
		add_submenu_page( 'new_import', __( "New Import", "exi-plugin" ), __( "New Import", "exi-plugin" ), 'manage_options', 'new_import', array($this, 'TemplateNewImport'));
	}

	public function adminEnqueueScriptsHook()
	{
    	wp_enqueue_script( 'estatik-exi', plugins_url( 'estatik-xml-importer/js/app.js' ), array('jquery'), '1.0', true );
    	wp_enqueue_style( 'estatik-exi', plugins_url( 'estatik-xml-importer/css/app.css' ) );
	}

	public function ajaxNewImport() 
	{
	   if(isset($_REQUEST['data']))
	   {
	   		ob_clean();

	   		$data = $_REQUEST['data'];
	   		$file = $data['xml']; //

	   		$xmlValidator = new EXI_Validator_Class();
			$parser = new EXI_Parser_Class();

	   		switch ($data['action']) {
	   			case 'validate_xml':

			   		echo $xmlValidator->validate($file) ? 1 : 0;

	   				break;

	   			case 'get_xpaths':
	   				
	   				$xpaths = $xmlValidator->getXPaths($file);

	   				header('Content-type: application/json');
	   				echo json_encode($xpaths);

	   				break;

	   			case 'get_attributes':
	   				//our config for parser class
					$config = array(
						'xml' => $file,
						'xpath' => $data['xpath'] 
						);

					$parser->load($config);
					$elements = $parser->getElements();

					//get first element and use basis on available attributes
					$template = array_shift($elements);

					//determine how to get data from the element
					$dataType = count($template->children()) ? 'tags' : 'attributes';

					//get attributes whether its tags or attributes
					$attributes['source'] = $parser->getAttributes($template, $dataType);
					$attributes['allowed'] = $parser->getAllowedAttributes();

	   				header('Content-type: application/json');
	   				echo json_encode($attributes);

	   				break;

	   				case 'submit_form':
	   				//our config for parser class
					$config = array(
						'xml' => $file,
						'xpath' => $data['xpath']
						);

					//parse the params string
					parse_str($data['data'], $output);

	   				$parser->load($config);
					$parser->setMapping($output);
					$objects = $parser->process(); //return objs ready to be stored in db

					//create records
					$propObj = new EXI_Property_Class();
					$propObj->loadObjects($objects);
					$count = $propObj->process();

	   				header('Content-type: application/json');
	   				echo json_encode($count);

	   				break;
	   		}
	   }

	   wp_die();
	}

	public function TemplateImports()
	{
		include("templates/imports.php");
	}

	public function TemplateNewImport()
	{
		include("templates/new_import.php");
	}

}

// initialize the plugin
$plugin = new Estatik_XML_Importer_Class();
$plugin->init();