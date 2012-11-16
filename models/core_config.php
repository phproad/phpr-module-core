<?php

class Core_Config extends Core_Settings_Model
{
    public $record_code = 'core_config';

    public static function create()
    {
        $config = new self();
        return $config->load();
    }   
    
    protected function build_form()
    {
        $this->add_field('locale_code', 'Locale Setting', 'full', db_varchar)->renderAs(frm_dropdown)->tab('General')->comment('Used for determining date format, etc.');
    }

    protected function init_config_data()
    {
        $this->locale_code = 'en_US';
    }

    protected function get_locale_code_options($key_value = -1)
    {
        return array(
            'auto' => 'Detect Automatically',
            'en_US' => 'English',
            'en_AU' => 'English - Australia',
            'en_CA' => 'English - Canada',
            'en_CA' => 'English - United Kingdom',
            'fr_FR' => 'French',
            'ru_RU' => 'Russian',
        );
    }

    public function is_configured()
    {
        $config = self::create();
        if (!$config)
            return false;

        return true;
    }

}