<?php

class Core_Settings
{
	protected $items;

	public function __construct()
	{
		$this->items = array();
	}

	public static function create()
	{
		return new self();
	}

	protected function load_items()
	{
		$modules = Core_Module_Manager::get_modules();

		foreach ($modules as $id=>$module)
		{
			$module->build_admin_settings($this);
		}

		uasort($this->items, array('Core_Settings', 'compare_settings_items'));
	}

	public function add($url, $title, $description, $icon, $position=500)
	{
		return $this->items[] = array('url'=>$url, 'title'=>$title, 'description'=>$description, 'icon'=>$icon, 'position'=>$position);
	}

	public function get()
	{
		$this->load_items();
		return (count($this->items)) ? $this->items : null;
	}

	public static function compare_settings_items($a, $b)
	{
		$sort_a = isset($a['position']) ? $a['position'] : 10000;
		$sort_b = isset($b['position']) ? $b['position'] : 10000;

		if ($sort_a == $sort_b)
			return 0;
		else if ($sort_a > $sort_b)
			return 1;
		else
			return -1;
	}

}