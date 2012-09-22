<?php

/**
 * Adds method 'add_attachments_field' to ActiveRecord class. 
 * Use this class to add file attachments fields to your models.
 */

class Core_Model_Attachments extends Phpr_Extension
{
	protected $_model;
	
	public function __construct($model)
	{
		$this->_model = $model;
	}
	
	/**
	 * Adds a file attachments column to the model
	 * @param string $columnName Specifies a column name. You may use any unique sql-compatible name here.
	 * @param string $displayName Specifies a name to display in lists and forms
	 * @param bool $showInLists Determines whether the field should be visible in record lists
	 * @return Db_FormFieldDefinition
	 */
	public function add_attachments_field($columnName, $displayName, $showInLists = false)
	{
		$this->_model->add_relation('has_many', $columnName, array(
			'class_name'=>'Db_File',
			'foreign_key'=>'master_object_id', 
			'conditions'=>"master_object_class='".get_class($this->_model)."' and field='".$columnName."'",
			'order'=>'sort_order, id',
			'delete'=>true));

		$column = $this->_model->define_multi_relation_column($columnName, $columnName, $displayName, '@name');
		if (!$showInLists)
			$column->invisible();

		$column->validation();
			
		return $this->_model->add_form_field($columnName)->renderAs(frm_file_attachments);
	}
}