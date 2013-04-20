<?php

$table = Db_Structure::table('core_settings');
	$table->primary_key('id');
	$table->column('record_code', db_varchar, 50)->index();
	$table->column('config_data', db_text);
	$table->save();

$table = Db_Structure::table('phpr_generic_binds');
  $table->primary_key('id');
  $table->column('primary_id', db_number, 11)->index();
  $table->column('secondary_id', db_number, 11)->index();
  $table->column('field_name', db_varchar, 100)->index();
  $table->column('class_name', db_varchar, 100)->index();
  $table->save();
