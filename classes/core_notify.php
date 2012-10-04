<?php

class Core_Notify 
{

    protected $_object = null;
    protected $_class_name = null;

    public static function trigger($class_name, $params=array())
    {
        $notify = new self();
        $notify->_class_name = $class_name;
        $notify->_object = $notifier = new $class_name();

        // @TODO Check prefs
        // if (!User_Preference::get_preference($provider->user_id, 'service', 'email_job_booked'))
        //     return;

        $notify->trigger_email($params);
        $notify->trigger_sms($params);

        return $notify;
    }

    public function trigger_email($params=array())
    {
        $notifier = $this->_object;
        $info = (object)$notifier->get_info();

        foreach ($notifier->required_params as $name)
        {
            if (!isset($params[$name]))
            {
                trace_log('Notification trigger ('.$this->_class_name.') is missing required parameter: '. $name);
                return;
            }
        }
        
        $template = Email_Template::create()->find_by_code($info->code);
        if (!$template)
        {
            $template = Email_Template::create();
            $template->code = $info->code;
            $template->description = $info->description;
            $template->subject = $notifier->get_subject();
            $template->content = $notifier->get_content();
            $template->save();
        }

        $notifier->on_send_email($template, $params);
    }    

    public function trigger_sms($params=array())
    {

    }

}