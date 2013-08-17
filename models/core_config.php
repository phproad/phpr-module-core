<?php

class Core_Config extends Core_Settings_Base
{
	public $record_code = 'core_config';

	public static function create()
	{
		$config = new self();
		return $config->load();
	}   
	
	protected function build_form()
	{
		$this->add_field('site_name', 'Site Name', 'full', db_varchar)->tab('General');
		$this->add_field('locale_code', 'Locale Setting', 'full', db_varchar)->display_as(frm_dropdown)->tab('General')->comment('Used for determining date format, etc.');
	}

	protected function init_config_data()
	{
		$this->site_name = Phpr::$config->get('APP_NAME');
		$this->locale_code = 'en_US';
	}

	protected function get_locale_code_options($key_value = -1)
	{
		return array(
			// 'auto' => 'Detect Automatically',
			'en_US' => 'English',
			'en_AU' => 'English - Australia',
			'en_CA' => 'English - Canada',
			'en_CA' => 'English - United Kingdom',
			'fr_FR' => 'French',
			'ru_RU' => 'Russian',
			'es_CL' => 'Spanish - Chile',
			'pt_BR' => 'Portuguese - Brazil',
		);
	}

	public function get_locale_reigon()
	{
		if ($this->locale_code)
			return 'US';

		$parts = explode('_', $this->locale_code);
		return isset($parts[1]) ? strtoupper($parts[1]) : null;
	}

	public function get_locale_language()
	{        
		if ($this->locale_code)
			return 'EN';

		$parts = explode('_', $this->locale_code);
		return isset($parts[0]) ? strtoupper($parts[0]) : null;
	}

	public function is_configured()
	{
		$config = self::create();
		if (!$config)
			return false;

		return true;
	}

}