<?php

// Base class used for models
//

abstract class Core_Module_Base
{
	private $module_info = null;

	public function get_module_info()
	{
		if ($this->module_info !== null)
			return $this->module_info;

		$this->module_info = $this->set_module_info();
		$this->module_info->id = basename($this->get_module_path());

		return $this->module_info;
	}

	public function get_module_path()
	{
		$reflect = new ReflectionObject($this);
		$path = dirname(dirname($reflect->getFileName()));
		return $path;
	}

	public function get_id()
	{
		return $this->get_module_info()->id;
	}

	abstract protected function set_module_info();

	//
	// Subscribe to core events
	//
	public function subscribe_events()
	{
		// Usage:
		// Phpr::$events->add_event('module:on_event_name', $this, 'local_module_method');
	}

	//
	// Subscribe to public access points
	//
	public function subscribe_access_points()
	{
		// Usage:
		// return array('phpr_api_access_url'=>'local_module_method');
		return array();
	}

	//
	// Subscribe to general cron table. Method must return true to indicate success.
	// Interval is in minutes.
	//
	public function subscribe_crontab()
	{
		// Usage:
		// return array('reset_counters' => array('method'=>'local_method', 'interval'=>60));
		return array();
	}

	//
	// Builds admin menu items
	//
	public function build_admin_menu($menu)
	{
		// Usage:
		// $bootstrap = $menu->add('bootstrap', 'Bootstrap', 'bootstrap', 999)->icon('bootstrap');
		// $boostrap->add_child('module', 'Create Module', 'bootstrap/index/create/module', 100);
	}

	//
	// Builds admin system tray items
	//
	public function build_admin_tray($tray)
	{
		// Usage:
		// $logout = $tray->add('logout', 'Logout', 999)->partial('tray_logout.htm')->icon('off');
	}

	//
	// Builds settings menu items
	//
	public function build_admin_settings($settings)
	{
		// Usage
		// $settings->add('/admin/setup', 'Name', 'Description', '/modules/admin/assets/images/icon.png', 300);
		return array();
	}

	//
	// Builds admin user permissions
	//
	public function build_admin_permissions($host)
	{
		// Usage:
		// $host->add_permission_field($this, 'manage_cms', 'Manage CMS')->display_as(frm_checkbox)->comment('Description');
	}

	//
	// Builds user preferences
	//
	public function build_user_preferences($host)
	{
		// Usage:
		// $host->add_preference_field($this, 'allow_emails', 'Allow Emails', true)->display_as(frm_checkbox)->comment('Description');
		return array();
	}
}