<?php

class Core_DateTime
{
	/**
	 * Creates an array with every possible time
	 *
	 * @param  Array  $add_items=array() Extra items to inject
	 * @return Array
	 */
	public static function time_array($add_items=array())
	{
		// Build an array of times
		$return_array = $add_items;
		$time = strtotime("00:00:00");
		$datetime = new Phpr_DateTime();
		$datetime->set_php_datetime($time);
		$return_array["00:00:00"] = $datetime->format('%I:%M %p');
		for ($i = 1; $i < 48; $i++)
		{
			$time = strtotime("+ 30 minutes", $time);
			$datetime->set_php_datetime($time);
			$key = $datetime->format('%H:%M:%S');
			$return_array[$key] = $datetime->format('%I:%M %p');
		}

		return $return_array;
	}

	public static function interval_as_string($datetime, $word_day='day', $word_hour='hr', $word_minute='min',  $empty='less than a minute')
	{
		$days = $datetime->get_days();
		$hours = $datetime->get_hours();
		$minutes = $datetime->get_minutes();

		$word_day = strlen($word_day > 1) ? Phpr_String::word_form($days, $word_day) : $word_day;
		$word_hour = strlen($word_hour > 1) ? Phpr_String::word_form($days, $word_hour) : $word_hour;
		$word_minute = strlen($word_minute > 1) ? Phpr_String::word_form($days, $word_minute) : $word_minute;

		$datetime_days = ($days > 0) ? $days . $word_day : "";
		$datetime_hours = ($hours > 0) ? $hours . $word_hour : "";
		$datetime_mins = ($minutes > 0) ? $minutes . $word_minute : "";

		$datetime = ($datetime_days=="" && $datetime_mins=="" && $datetime_hours=="") 
			? $empty 
			: trim($datetime_days . " " . $datetime_hours . " " . $datetime_mins);
			
		return $datetime;
	}

	public static function interval_to_now($datetime, $default_text='Some time')
	{
		if (!($datetime instanceof Phpr_DateTime))
			return $default_text;

		return Phpr_DateTime::now()->substract_datetime($datetime)->interval_as_string();		
	}

	public static function interval_from_now($datetime, $default_text='Some time')
	{
		if (!($datetime instanceof Phpr_DateTime))
			return $default_text;

		return $datetime->substract_datetime(Phpr_DateTime::now())->interval_as_string();		
	}

	public static function format_safe($value, $format='%x')
	{
		if ($value instanceof Phpr_DateTime) 
			return $value->format($format);
		else
		{
			$len = strlen($value);
			if (!$len)
				return null;
			if ($len <= 10)
				$value .= ' 00:00:00';

			$value = new Phpr_Datetime($value);
			return $value->format($format);
		}

		return __('Not set', true);
	}
}