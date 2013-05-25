<?php

$table = Db_Structure::table('core_settings');
	$table->primary_key('id');
	$table->column('record_code', db_varchar, 50)->index();
	$table->column('config_data', db_text);
