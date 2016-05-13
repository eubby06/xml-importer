<?php

$defaults = array(
	'prop_type' => 9, //office
	'prop_category' => 5, //for rent
	'prop_status' => 3, // open
	'prop_pub_unpub' => 1, //publish default
	'post_content' => '[es_single_property]', //default for property
	'post_status' => 'publish', //auto publish
	'post_type' => 'properties',
	'prop_meta_key' => 'images'
	);

$propertyFields = array(
				'prop_id',
				'agent_id',
				'prop_number',
				'prop_pub_unpub',
				'prop_date_added',
				'prop_title',
				'prop_type',
				'prop_category',
				'prop_status',
				'prop_price',
				'prop_bedrooms',
				'prop_bathrooms',
				'prop_floors',
				'prop_area',
				'prop_lotsize',
				'prop_builtin',
				'prop_description',
				'country_id',
				'state_id',
				'city_id',
				'prop_zip_postcode',
				'prop_street',
				'prop_address',
				'prop_longitude',
				'prop_latitude'
			);