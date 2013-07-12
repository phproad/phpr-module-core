<?php

class Core_Module extends Core_Module_Base
{

	protected function set_module_info()
	{
		return new Core_Module_Detail(
			"Core",
			"Core functions",
			"PHPRoad",
			"http://phproad.com/"
		);
	}

	public function subscribe_events()
	{
		Phpr::$events->add_event('phpr:on_after_locale_initialized', $this, 'on_after_phpr_locale_initialized');
	}

	function on_after_phpr_locale_initialized($locale)
	{
		$locale->load();
	}

	public function build_admin_settings($settings)
	{
		$settings->add('/core/setup', 'Common Settings', 'Customise core features', '/modules/core/assets/images/core_config.png', 10);
	}

	public function subscribe_access_points()
	{
		return array('api_cron'=>'execute_cron');
	}

	public function execute_cron()
	{
		Phpr_Cron::execute_cron();
	}
}
