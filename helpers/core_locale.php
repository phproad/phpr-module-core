<?php

class Core_Locale
{

	public static function format_currency($value)
	{
		if (class_exists('Payment_Config'))
			return Payment_Config::format_currency($value);
		else
			return Phpr::$locale->get_currency($value);
	}

	public static function currency_symbol()
	{
		if (class_exists('Payment_Config'))
			return Payment_Config::currency_symbol();
		else
			return self::currency('local_currency_symbol');
	}

	public static function currency($value=null)
	{
		return Phpr::$locale->get_string('phpr.currency', $value);
	}

	public static function date($value=null)
	{
		return Phpr::$locale->get_string('phpr.dates', $value);
	}

}