<?php

/**
 * Adds method 'add_attachments_field' to ActiveRecord class. 
 * Use this class to add file attachments fields to your models.
 */

class Core_Model_Attachments extends Phpr_Extension_Base
{
	protected $_model;
	
	public function __construct($model)
	{
		$this->_model = $model;
	}
	
	/**
	 * Adds a file attachments column to the model
	 * @param string $column_name Specifies a column name. You may use any unique sql-compatible name here.
	 * @param string $display_name Specifies a name to display in lists and forms
	 * @param bool $show_in_lists Determines whether the field should be visible in record lists
	 * @return Db_FormFieldDefinition
	 */
	public function add_attachments_field($column_name, $display_name, $show_in_lists = false)
	{
		$this->_model->add_relation('has_many', $column_name, array(
			'class_name'=>'Db_File',
			'foreign_key'=>'master_object_id', 
			'conditions'=>"master_object_class='".get_class($this->_model)."' and field='".$column_name."'",
			'order'=>'sort_order, id',
			'delete'=>true));

		$column = $this->_model->define_multi_relation_column($column_name, $column_name, $display_name, '@name');
		if (!$show_in_lists)
			$column->invisible();

		$column->validation();
			
		return $this->_model->add_form_field($column_name)->renderAs(frm_file_attachments);
	}

    public function add_attachment_from_post($field='files', $file_info, $delete = false, $session_key = null)
    {
        if ($session_key === null)
            $session_key = post('secure_token');

        if (!array_key_exists('error', $file_info) || $file_info['error'] == UPLOAD_ERR_NO_FILE)
            return;

        Phpr_Files::validate_uploaded_file($file_info);

        $this->_model->init_columns_info();

        if ($delete) 
        {
        	$files = $this->_model->list_related_records_deferred($field, $session_key);
        	foreach ($files as $existing_file)
        	{
	        	$this->_model->{$field}->delete($existing_file, $session_key);
        	}
        }

        $file = Db_File::create();
        $file->is_public = true;

        $file->fromPost($file_info);
        $file->master_object_class = get_class($this->_model);
        $file->master_object_id = $this->_model->id;
        $file->field = $field;
        $file->save(null, $session_key);

        $this->_model->{$field}->add($file, $session_key);
        return $file;
    }

}