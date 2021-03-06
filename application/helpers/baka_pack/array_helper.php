<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Baka Pack
 *
 * My very own Codeigniter Boilerplate Library that used on all of my projects
 *
 * NOTICE OF LICENSE
 *
 * Everyone is permitted to copy and distribute verbatim or modified 
 * copies of this license document, and changing it is allowed as long 
 * as the name is changed.
 *
 *            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE 
 *  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION 
 *
 * 0. You just DO WHAT THE FUCK YOU WANT TO.
 *
 * @package     Baka_pack
 * @author      Fery Wardiyanto
 * @copyright   Copyright (c) Fery Wardiyanto. (ferywardiyanto@gmail.com)
 * @license     http://www.wtfpl.net
 * @since       Version 0.1.3
 */

// -----------------------------------------------------------------------------

class bakaObject extends stdClass {}

// -----------------------------------------------------------------------------

function print_pre($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

// -----------------------------------------------------------------------------

/**
 * Converting an array into object
 * 
 * @param  array  $array array source
 * @return object
 */
function array_to_object( array $array )
{
    $obj = new bakaObject;

    foreach ( $array as $modul => $prop )
    {
        $obj->$modul = ( is_array( $prop ) ? array_to_object( $prop ) : $prop );
    }

    return $obj;
}

// -----------------------------------------------------------------------------

/**
 * Update array value by array key
 * 
 * @param  array
 * @param  array
 * @return array
 */
function array_edit_val( array $array1, array $array2 )
{
    $array = array();

    foreach ( $array1 as $key => $val)
    {
        $array[$key] = $val;

        if ( isset($array2[$key]) AND $array[$key] != $array2[$key] )
            $array[$key] = $array2[$key];
    }

    return $array;
}

// -----------------------------------------------------------------------------

// Some array helper
function baka_get_value_from_key( $value , $array )
{
    if ( isset( $array[$value] ) ) { return $array[$value]; }
}

// -----------------------------------------------------------------------------

function baka_array_search ( $needle, $haystack )
{
    foreach ( $haystack as $key => $value )
    {
        $current_key = $key;

        if (is_array($value))
        {
            $value = baka_array_search ( $needle, $value );
        }
        else
        {
            if ($needle === $value OR ($value != FALSE AND $value != NULL))
            {
                if ($value == NULL)
                    return array($current_key);
                
                return array_merge(array($current_key), $value);
            }
        }
    }

    return FALSE;
}

// -----------------------------------------------------------------------------

function array_insert_after_node( $array, $after_key, $index, $value)
{
    $result = array();
    $keys   = array_keys($array);

    if (in_array($after_key, $keys) === FALSE)
        return FALSE;

    foreach ($array as $id => $item)
    {
        $result[$id] = $item;

        if ($id === $after_key)
            $result[$index] = $value;
    }

    return $result;
}

/* End of file assets_helper.php */
/* Location: ./application/helpers/baka_pack/assets_helper.php */