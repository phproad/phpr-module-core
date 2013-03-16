<?php

class Core_String
{
	public static function decode_id($int)
	{
		return intval(self::base36_decode(str_rot13($int)))-100;
	}

	public static function encode_id($int)
	{
		return str_rot13(self::base36_encode($int+100));
	}

	public static function base36_encode($base10)
	{
		return base_convert($base10,10,36);
	}

	public static function base36_decode($base36)
	{
		return base_convert($base36,36,10);
	}

	public static function limit_and_highlight_words($string, $phrase, $limit = 100, $tag_open, $tag_close, $end_char = '&#8230;', $start_char = '&#8230;')
	{
		$sub_string = stristr($string, $phrase);

		if(!$sub_string)
		{
			  $sub_string = $string;
			  $start_char = '';
		}

		$sub_string = self::limit_words($sub_string, $limit, $end_char);

		return $start_char .= self::highlight_words($sub_string, $phrase, $tag_open, $tag_close);
	}

	public static function limit_words($string, $limit = 100, $end_char = '&#8230;', $is_html=true)
	{
		if (trim($string) == '')
			return $string;

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $string, $matches);

		if (strlen($string) == strlen($matches[0]))
			$end_char = '';

		$str = rtrim($matches[0]).$end_char;
		return ($is_html) ? $str.Phpr_Html::get_orphan_tags($str) : $str;
	}

	public static function highlight_words($string, $phrase, $tag_open = '<strong>', $tag_close = '</strong>')
	{
		if ($string == '')
			return '';

		if ($phrase != '')
			return preg_replace('/('.preg_quote($phrase, '/').')/i', $tag_open."\\1".$tag_close, $string);

		return $string;
	}

	// Supports strings without any HTML
	public static function show_more_link($string, $limit = 500, $more_text = 'Show more')
	{
		if (strlen($string) < $limit)
			return $string;

		$string = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $string));

		if (strlen($string) <= $limit)
			return $string;

		// true = start, false = end
		$switch = true;
		$start = "";
		$end = "";

		foreach (explode(' ', trim($string)) as $val)
		{
			if ($switch)
			{
				$start .= $val . ' ';
				if (strlen($start) >= $limit)
				{
					$start = trim($start);
					$switch = false;
				}
			}
			else
				$end .= $val . ' ';
		}

		$return = array();
		$return[] = $start;
		$return[] = ' ';
		$return[] = '<a href="javascript:;" onclick="jQuery(this).hide().next().show()">' . $more_text . '&#8230;</a>';
		$return[] = '<span style="display:none">';
		$return[] = $end;
		$return[] = '</span>';
		return implode(PHP_EOL, $return);

	}

}