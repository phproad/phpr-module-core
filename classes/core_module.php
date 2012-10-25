<?php

class Core_Module extends Core_Module_Base
{

    protected function set_module_info()
    {
        return new Core_Module_Detail(
            "Core",
            "Core functions",
            "Scripts Ahoy!",
            "http://scriptsahoy.com/"
        );
    }

    public function subscribe_events()
    {
        Phpr::$events->add_event('phpr:onAfterLocaleInitialized', $this, 'on_after_phpr_locale_initialized');
    }

    function on_after_phpr_locale_initialized($localization)
    {
        $localization->load();
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
