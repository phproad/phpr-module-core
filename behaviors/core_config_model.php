<?php

/**
 * Configuration Data Behavior
 *
 * Adds a special field type config_data that stores flexible data.
 *
 * Usage:
 *
 * public $implement = 'Core_Config_Model';
 *
 */

class Core_Config_Model extends Phpr_Extension
{

    protected $_model;
    public $added_config_fields = array();
    public $added_config_columns = array();
    public $config_model_field = "config_data";

	public function __construct($model)
	{
		$this->_model = $model;

        if (isset($model->config_model_field))
            $this->config_model_field = $model->config_model_field;

        $this->_model->add_event('onAfterLoad', $this, 'load_config_data');
        $this->_model->add_event('onBeforeUpdate', $this, 'set_config_data');
        $this->_model->add_event('onBeforeCreate', $this, 'set_config_data');
	}

    public function define_config_column($code, $title, $type = db_text)
    {
        return $this->added_config_columns[$code] = $this->_model->define_custom_column($code, $title, $type);
    }

    public function add_config_field($code, $side = 'full')
    {
        return $this->added_config_fields[$code] = $this->_model->add_form_field($code, $side)->optionsMethod('get_added_field_options');
    }

    public function set_config_field($field)
    {
        return $this->added_config_columns[$field];
    }

    public function set_config_data()
    {
        $document = new SimpleXMLElement('<data></data>');
        foreach ($this->added_config_columns as $field_id=>$value)
        {
            $value = serialize($this->_model->{$field_id});
            $field_element = $document->addChild('field');
            Core_Xml::create_dom_element($document, $field_element, 'id', $field_id);
            Core_Xml::create_dom_element($document, $field_element, 'value', $value, true);            
        }

        $config_field = $this->config_model_field;
        $this->_model->{$config_field} = $document->asXML();
    }

    public function load_config_data()
    {
        $config_field = $this->config_model_field;

        if (!strlen($this->_model->{$config_field}))
            return;

        $object = new SimpleXMLElement($this->_model->{$config_field});
        foreach ($object->children() as $child)
        {
            $field_id = (string)$child->id;
            try 
            {
                $this->_model->$field_id = unserialize($child->value);
                $this->_model->fetched[$field_id] = unserialize($child->value);
            }
            catch (Exception $ex)
            {
                $this->_model->$field_id = "NaN";
                $this->_model->fetched[$field_id] = "NaN";
                trace_log(sprintf('Core_Config_Model was unable to parse %s in %s', $field_id, get_class($this->_model)));
            }
        }
    }

}
