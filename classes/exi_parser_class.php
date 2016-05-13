<?php

class EXI_Parser_Class
{

	protected $xml;
	protected $elements;
	protected $config;
	protected $mapping;
	protected $allowedAttributes = array(
		'prop_number',
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
		'prop_latitude',
		'photos'
		);

	public function load($config = array())
	{

		if( !isset($config['xml']) && !isset($config['xpath']) )
		{
			return false;
		}

		//set configuration
		$this->config = $config;

		$xmlstring = $this->config['xml'];

		$this->xml = simplexml_load_file($xmlstring);

		foreach($this->xml->xpath('//'.$config['xpath']) as $element)
		{
			//add each element to elements array
			$this->elements[] = $element;
		}
	}

	public function setMapping($mapping)
	{
		$this->mapping = $mapping;
	}

	public function process()
	{
		$elements = $this->getElements();
		$mapping = $this->getMapping();
		$objs = array();

		foreach($elements as $el)
		{
			$photos = array();
			$obj = new stdClass();

			//check if it has children, if no then should be in attributes
			if(count($el->children()))
			{
				//loop through our mapping data
				foreach($mapping as $k => $v)
				{
					if($v != 'Select Attribute' && $v != 'Select Field')
					{
						if($k == 'photos')
						{
							$obj->$k = (array) $el->images->image;
						}
						else
						{
							$arrayedValue = (array)$el->$v;
							$attr = $arrayedValue[0];
							$obj->$k = $attr;			
						}

					}
				}
			}
			//data as attributes
			else
			{
				$attributes = (array) $el->attributes();
				$attributes = $attributes['@attributes'];

				//loop through our mapping data
				foreach($mapping as $k => $v)
				{
					if($v != 'Select Attribute' && $v != 'Select Field')
					{
						//since photo is an array, loop through its value
						if($k == 'photos')
						{
							foreach($v as $a => $b)
							{
								$photos[] = $attributes[$b];
							}

							$obj->$k = $photos;
						}
						else
						{
							$obj->$k = $attributes[$v];
						}
					}
				}
			}

			$objs[] = $obj;
		}

		return $objs;

	}

	public function getMapping()
	{
		return $this->mapping;
	}

	public function getElements()
	{
		return $this->elements;
	}

	public function getAllowedAttributes()
	{
		return $this->allowedAttributes;
	}

	public function getAttributes($template, $dataType)
	{
		$data = array();

		if($dataType == 'tags')
		{
			foreach($template->children() as $k => $v)
			{
				$data[] = $k;
			}
		}
		else
		{
			foreach($template->attributes() as $k => $v)
			{
				$data[] = $k;
			}
		}

		return $data;
	}
}