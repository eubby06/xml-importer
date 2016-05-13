<?php

class EXI_Validator_Class
{

	protected $xml;

	public function validate($url)
	{

		$this->xml = simplexml_load_file($url);

		if($this->xml)
		{
			return true;
		}

		return false;
	}

	public function getXML()
	{
		return $this->xml;
	}

	public function getXPaths($url)
	{
		$xml = new XMLReader();
		$xml->open($url);

		$elements = array();

		while($xml->read())
		{
			if($xml->nodeType == XMLReader::ELEMENT)
			{
				$elements[$xml->name] = $xml->name;
			}
		}

		return array_keys($elements);
	}
}