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
        $document = new SimpleXMLExtended('<data></data>');
        foreach ($this->added_config_columns as $id=>$value)
        {
            $field_element = $document->addChild('field');
            $field_element->addChild('id', $id);
            $value_node = $field_element->addChild('value');
            $value_node->addCData(serialize($this->_model->{$id}));
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
            $code = $child->id;
            try 
            {
                $this->_model->$code = unserialize($child->value);
            }
            catch (Exception $ex)
            {
                $this->_model->$code = "NaN";
                trace_log(sprintf('Core_Config_Model was unable to parse %s in %s', $code, get_class($this->_model)));
            }
        }
    }

}

if (!class_exists('SimpleXMLExtended'))
{
    class SimpleXMLExtended extends SimpleXMLElement
    {
        public function addCData($cdata_text)
        {
            $node = dom_import_simplexml($this);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($cdata_text));
        }
    }
}