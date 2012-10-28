<?php

class Core_Db 
{

    // Slugifys and caps a string
    // Returns a uri code
    public static function slugify_safe($table, $field, $string, $max_length = null)
    {
        $code = Phpr_Inflector::slugify($string);
        if ($max_length)
            $code = substr($code, 0, $max_length);

        return self::create_safe_code($table, $field, $code);
    }

    // Checks db for exisiting code and returns one not in use
    // Usage:
    //   Core_Db::create_safe_code('service_providers', 'url_name', 'some-uri-code');
    public static function create_safe_code($table, $field, $code)
    {
        $code = mb_strtolower($code);
        $counter = 1;
        $original_code = $code;
        while (Db_DbHelper::scalar('select count(*) from '.$table.' where '.$field.'=:code', array('code'=>$code)))
        {
            $counter++;
            $code = $original_code .= '-'.$counter;
        }
        return $code;
    }

}