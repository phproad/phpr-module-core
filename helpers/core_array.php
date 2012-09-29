<?php

/**
 * Core array helpers
 */
class Core_Array
{
	
	/**
	 * Returns the value of a specified key in a specified list.
	 * @param string $key The key to grab.
	 * @param array $list The list to be used.
	 * @return array Returns the value of the specified key.
	 * @example 1
	 * You can sanitized an array like this:
	 * $result = Core_Array::get_key_value('a', array('a' => 1, 'b' => 2));
	 * The result will be 1, the value of the 'a' key.
	 */
	public static function get_key_value($key, $list) 
	{
		return $list[$key];
	}

	/**
	 * Returns multi-dimensional array representation of all array parameters.
	 * @param array Unlimited arrays to merge.
	 * @return array Returns the merged array.
	 */
	public static function merge_recursive_distinct() 
	{
		$arrays = func_get_args();
		$base = array_shift($arrays);
		
		if (!is_array($base)) $base = empty($base) ? array() : array($base);
		
		foreach ($arrays as $append) 
		{
			if (!is_array($append)) $append = array($append);
			
			foreach ($append as $key => $value) 
			{
				if (!array_key_exists($key, $base) and !is_numeric($key)) 
				{
					$base[$key] = $append[$key];
					continue;
				}
				
				if (is_array($value) || (isset($base[$key]) && is_array($base[$key]))) 
				{
					$base[$key] = self::merge_recursive_distinct($base[$key], $append[$key]);
				} 
				else if (is_numeric($key)) 
				{
					if (!in_array($value, $base)) $base[] = $value;
				} 
				else 
				{
					$base[$key] = $value;
				}
			}
		}
		
		return $base;
	}
	
	/**
	 * Returns an array filtered by a list of keys.
	 * @param array $list The list to be filtered.
	 * @param array $keys The keys to filter by.
	 * @return array Returns the filtered array.
	 * @example 1
	 * You can serialize and filter a $model like this:
	 * Core_Array::filter_by_keys(Core_Array::get_key_value('fields', $model->serialize()), array('id', 'name', 'url_name'));
	 */
	public static function filter_by_keys($list, $keys = array()) 
	{
		$list_keys = array_keys($list);
		$good_keys = array_intersect($list_keys, array_values($keys));
		$good_list = array();
		
		foreach ($list as $key => $item) 
		{
			if (!in_array($key, $good_keys)) 
				continue;
			
			$good_list[$key] = $item;
		}
		
		return $good_list;
	}
	
	/**
	 * Returns an array whose values have been sanitized. It performs checks on the true type of a value, and converts it to the associated PHP type. (integer, string, float, etc).
	 * @param array $list The list to be sanitized.
	 * @return array Returns the sanitized array.
	 * @example 1
	 * You can sanitized an array like this:
	 * $result = Core_Array::sanitize_value_types(array('1', '1.0'));
	 * The result will be an array of 1 and 1.0, an int and a float.
	 */
	public static function sanitize_value_types($list) 
	{
		$good_list = array();
		
		foreach ($list as $key => $item) 
		{
			if (is_array($item)) 
			{
				$new_item = self::sanitize_value_types($item);
			}
			else if (is_object($item)) { // unhandled
				$new_item = $item;
			}
			else if ((string)intval($item) === (string)$item) { // we found an int
				$new_item = intval($item);
			}
			else 
			{
				$new_item = $item;
			}
			
			$good_list[$key] = $new_item;
		}
		
		return $good_list;
	}

}