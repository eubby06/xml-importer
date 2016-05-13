<?php

//process submitted form
if($_POST)
{

	$parser->setMapping($_POST);
	$objects = $parser->process(); //return objs ready to be stored in db

	//create records
	$propObj = new EXI_Property_Class();
	$propObj->loadObjects($objects);
	$propObj->process();
}

?>

<div class="es_wrapper estatik-ext">

    <div class="es_header clearFix">
        <h2><?php _e( "XML Importer By Boolex", "es-ext-plugin" ); ?></h2>
    </div>

    <div class="es_all_listing_search clearFix">
                
        <div class="es_manage_listing clearFix">
            <ul class="feedback">
                <li><a target="_blank" href="http://boolex.com/contact-us/" id="es_listing_custom"><?php _e( "We Customize for $30/hr", "es-ext-plugin" ); ?></a></li>
                <li><a target="_blank" href="http://boolex.com/contact-us/" id="es_listing_suggest"><?php _e( "Send Suggestions", "es-ext-plugin" ); ?></a></li>
            </ul>
        </div>
        
    </div>
</div>

<div class="exi-importer">
	<div class="exi-settings exi-box">
		<h1>Step 1</h1>
		<h2>Import Settings</h2>
		<form action="" method="post">
			<ul>
				<li>
					<input type="text" class="exi-url" placeholder="Please enter the url" />
					<button type="submit" class="exi-button exi-validate-xml">Validate XML</button>
					<span class="exi-result"></span>
				</li>
				<li class="exi-xpaths-dropdown"><span class="exi-loading-xpaths"></span></li>
			</ul>
		</form>
	</div>

	<form id="exi-attributes" action="" method="post">
		<div class="exi-data-mapping exi-box">
			<h1>Step 2</h1>
			<h2>Data Mapping</h2>
			
				<div class="add-attribute">
					<button type="submit" class="exi-button exi-add-attribute">Add Attribute</button>
				</div>
				
				<ul>
					<li>
						<div class="source-attributes-container"></div>
					</li>
				</ul>
			
		</div>
		<div class="exi-start-import exi-box">
			<h1>Step 3</h1>
			<h2>Start Import</h2>
			<button type="submit" class="exi-button exi-import-button">Import</button>
			<p class="exi-import-result"></p>
		</div>
	</form>
</div>