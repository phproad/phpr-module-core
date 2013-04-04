<?php

class Core_Settings_Base extends Db_ActiveRecord
{
	public $table_name = 'core_settings';

	public $record_code = null;
	protected $added_fields = array();
	protected static $loaded_objects = array();

	public function load()
	{
		if (array_key_exists($this->record_code, self::$loaded_objects))
			return self::$loaded_objects[$this->record_code];

		$this->disable_column_cache();

		$obj = $this->find_by_record_code($this->record_code);
		if (!$obj)
		{
			$class_name = get_class($this);
			$obj = new $class_name();
		}

		$obj->init_form_fields();

		self::$loaded_objects[$this->record_code] = $obj;

		return $obj;
	}

	public function define_form_fields($context = null)
	{
		$this->build_form();

		if (!$this->is_new_record())
			$this->load_xml_data();
		else
			$this->init_config_data();
	}

	protected function build_form()
	{

	}

	protected function add_field($code, $title, $side = 'full', $type = db_text)
	{
		$this->define_custom_column($code, $title, $type);

		$form_field = $this->add_form_field($code, $side);
		$this->added_fields[$code] = $form_field;

		return $form_field;
	}

	public function before_save($session_key = null)
	{
		$this->validate_config_on_save($this);

		$document = new SimpleXMLElement('<settings></settings>');
		foreach ($this->added_fields as $code=>$form_field)
		{
			$field_element = $document->addChild('field');
			$field_element->addChild('id', $code);
			$field_element->addChild('value', serialize($this->$code));
		}

		$this->config_data = $document->asXML();
	}

	protected function load_xml_data()
	{
		if (!strlen($this->config_data))
			return;

		$object = new SimpleXMLElement($this->config_data);
		foreach ($object->children() as $child)
		{
			$code = $child->id;
			$this->$code = unserialize($child->value);
		}

		$this->validate_config_on_load($this);
	}

	protected function validate_config_on_load()
	{
	}

	protected function init_config_data()
	{
	}

	protected function validate_config_on_save()
	{
	}
}

