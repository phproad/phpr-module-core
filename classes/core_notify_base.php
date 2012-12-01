<?php

class Core_Notify_Base
{
    public $required_params = array();

    public function get_info()
    {
        return array(
            'name'=> 'Notification',
            'description' => 'Generic Notification',
            'code' => 'module:notification_template'
        );
    }
    
    // Subject
    public function get_subject() { }

    // Long content (eg: Email)
    public function get_content() { }
    
    // Short content (eg: SMS)
    public function get_summary() { }

    // Email
    public function on_send_email($template, $params=array()) { }

    // SMS @todo
    public function on_send_sms() { }

    // Helper
    public function get_partial_path($partial_name = null)
    {
        $class_name = get_class($this);
        $class_path = File_Path::find_path_to_class($class_name);
        return $class_path.'/'.strtolower($class_name).'/'.$partial_name;
    }
}