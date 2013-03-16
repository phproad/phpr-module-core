<?php

class Core_Number 
{
	/**
	 * Returns true if the passed value is a floating point number
	 * @param number $value number
	 * @return boolean Returns boolean
	 */
	public static function is_valid_float($value) {
		return preg_match('/^[0-9]*?\.?[0-9]*$/', $value);
	}
	
	/**
	 * Returns true if the passed value is an integer value
	 * @param number $value number
	 * @return boolean Returns boolean
	 */
	public static function is_valid_int($value) {
		return preg_match('/^[0-9]*$/', $value);
	}
}