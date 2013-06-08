<?php

function c($name, $type='Core')
{
	$class_name = $type . '_Config';
	$settings = call_user_func(array($class_name, 'create'));
	return $settings->{$name};
}