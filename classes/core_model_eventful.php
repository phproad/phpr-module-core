<?php

/**
 * Eventful behavior... TODO: doesn't work unfortunately :( Remove?
 *
 * Adds standard extensible events to a modal 
 *
 * Events generated:
 * 
 * location:on_extend_country_model
 * location:on_extend_country_form
 * location:on_get_country_field_options
 * location:on_after_load_country
 * location:on_before_update_country
 * location:on_before_create_country
 * location:on_before_save_country
 *
 * Usage:
 *
 * public $implement = 'Core_Eventful_Model';
 * 
 * public $eventful_model_prefix = "location";
 * public $eventful_model_name = "country";
 * 
 * public function define_form_fields($context = null)
 * {
 *     $this->eventful_model_define_form_fields($context);
 * }
 *
 * public function get_added_field_options($db_name, $current_key_value = -1)
 * {
 *     return $this->eventful_model_get_added_field_options($db_name, $current_key_value);
 * }
 *
 */

class Core_Model_Eventful extends Phpr_Extension
{

    protected $_model;
    protected $api_added_columns = array();
    public $eventful_model_prefix = "service";
    public $eventful_model_name = "category";

    public function __construct($model)
    {
        $this->_model = $model;

        if (isset($model->eventful_model_prefix))
            $this->eventful_model_prefix = $model->eventful_model_prefix;

        if (isset($model->eventful_model_name))
            $this->eventful_model_name = $model->eventful_model_name;

        $this->_model->add_event('db:on_define_columns', $this, 'eventful_model_define_columns');
        $this->_model->add_event('db:on_after_load', $this, 'eventful_model_after_load');
        $this->_model->add_event('db:on_before_update', $this, 'eventful_model_before_update');
        $this->_model->add_event('db:on_before_create', $this, 'eventful_model_before_create');
        $this->_model->add_event('db:on_define_form_fields', $this, 'eventful_model_define_form_fields');
    }

    public function eventful_model_after_load()
    {
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_after_load_'.$this->eventful_model_name, $this->_model);
    }

    public function eventful_model_before_update($deferred_session_key = null)
    {
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_before_update_'.$this->eventful_model_name, $this->_model, $deferred_session_key);
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_before_save_'.$this->eventful_model_name, $this->_model, $deferred_session_key);
    }

    public function eventful_model_before_create($deferred_session_key = null)
    {
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_before_create_'.$this->eventful_model_name, $this->_model, $deferred_session_key);
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_before_save_'.$this->eventful_model_name, $this->_model, $deferred_session_key);
    }

    public function eventful_model_define_columns($context = null)
    {

        $this->_model->defined_column_list = array();
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_extend_'.$this->eventful_model_name.'_model', $this->_model, $context);
        $this->api_added_columns = array_keys($this->_model->defined_column_list);
    }

    public function eventful_model_define_form_fields($context = null)
    {
        trace_log($this->eventful_model_prefix.':on_extend_'.$this->eventful_model_name.'_form');
        Phpr::$events->fire_event($this->eventful_model_prefix.':on_extend_'.$this->eventful_model_name.'_form', $this->_model, $context);
        foreach ($this->api_added_columns as $column_name)
        {
            $form_field = $this->_model->find_form_field($column_name);
            if ($form_field)
                $form_field->options_method('get_added_field_options');
        }
    }

    public function eventful_model_get_added_field_options($db_name, $current_key_value = -1)
    {
        $result = Phpr::$events->fire_event($this->eventful_model_prefix.':on_get_'.$this->eventful_model_name.'_field_options', $db_name, $current_key_value);
        foreach ($result as $options)
        {
            if (is_array($options) || (strlen($options && $current_key_value != -1)))
                return $options;
        }

        return false;
    }
}
