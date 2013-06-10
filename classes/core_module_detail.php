<?php

/**
 * Class to define a module
 */

class Core_Module_Detail
{
	public $id;
	
	public $name;
	public $author;
	public $url;
	public $description;

	public function __construct($name, $description, $author, $url = null)
	{
		$this->name = $name;
		$this->author = $author;
		$this->description = $description;
		$this->url = $url;
	}
	
	public function get_version()
	{
		return Phpr_Version::get_module_version($this->id);
	}
	
	public function get_build()
	{
		return Phpr_Version::get_module_build($this->id);
	}
}
