<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Baka Pack
 *
 * My very own Codeigniter core library that used on all of my projects
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * @package     Baka_pack
 * @author      Fery Wardiyanto
 * @copyright   Copyright (c) Fery Wardiyanto. (ferywardiyanto@gmail.com)
 * @license     http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @since       Version 0.1
 */

// -----------------------------------------------------------------------------

function _x( $lang_line, $replacement = '' )
{
    $CI_lang =& get_instance()->lang;

    $lang_line = $CI_lang->line( $lang_line );

    if ( is_array( $replacement ) and count( $replacement ) > 0 )
    {
        return vsprintf( $lang_line, $replacement );
    }
    else if ( is_string( $replacement ) and strlen( $replacement ) > 0 )
    {
        return sprintf( $lang_line, $replacement );
    }
    else
    {
        return $lang_line;
    }
}

// -----------------------------------------------------------------------------

function return_bytes( $val )
{
    $val    = trim( $val );
    $last   = strtolower( $val[strlen($val)-1] );

    switch ( $last )
    {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }

    return $val;
}

// -----------------------------------------------------------------------------

function format_size( $size )
{
    $sizes  = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
    $y      = $sizes[0];

    for ( $i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++ )
    {
        $size   = $size / 1024;
        $y      = $sizes[$i];
    }

    return round($size, 2).' <span class="muted">'.$y.'</span>';
}

// -----------------------------------------------------------------------------

function baka_echo( $anu )
{
    if ( is_array( $anu ) OR is_object( $anu ) )
        var_dump( $anu );
    else
        echo $anu;
}

// -----------------------------------------------------------------------------
// Date and Time helper
// -----------------------------------------------------------------------------

function format_date( $string = '' )
{
    return bdate( Setting::get('app_date_format'), $string);
}

// -----------------------------------------------------------------------------

function format_datetime( $string = '' )
{
    return bdate( Setting::get('app_datetime_format'), $string);
}

// -----------------------------------------------------------------------------

function format_time( $string = '' )
{
    return bdate( 'H:i:s', $string);
}

// -----------------------------------------------------------------------------

function string_to_date( $string = '' )
{
    return bdate( 'Y-m-d', $string);
}

// -----------------------------------------------------------------------------

function string_to_datetime( $string = '' )
{
    return bdate( 'Y-m-d H:i:s', $string);
}

// -----------------------------------------------------------------------------

function bdate( $format = '', $strdate = '' )
{
    setlocale(LC_ALL, 'id');

    $strdate = $strdate != '' ? strtotime( $strdate ) : time();
    $format || $format = 'Y-m-d H:i:s';

    return date( $format, $strdate );
}

// -----------------------------------------------------------------------------

function baka_get_umur( $lahir, $sampai = '' )
{
    $tgllahir = strtotime($lahir);
    $sekarang = ($sampai == '') ? time() : strtotime($sampai) ;

    $umur = ($tgllahir < 0) ? ( $sekarang + ($tgllahir * -1) ) : $sekarang - $tgllahir; 

    $tahun = 60 * 60 * 24 * 365;

    $tahunlahir = $umur / $tahun;

    return floor($tahunlahir);
}

// -----------------------------------------------------------------------------

function second_to_day( $second )
{
    return $second / 60 / 60 / 24;
}

// -----------------------------------------------------------------------------

function get_month_assoc()
{
    $CI =& get_instance();

    if ( !in_array('calendar_lang.php', $CI->lang->is_loaded, TRUE))
    {
        $CI->lang->load('calendar');
    }
    
    $output = array();

    for ( $i=1; $i<=12; $i++ )
    {
        $month = date('F', mktime(0, 0, 0, $i, 1));
        $output[$i] = _x( 'cal_'.strtolower($month) );
    }

    return $output;
}

// -----------------------------------------------------------------------------

function get_year_assoc( $interfal = 10 )
{
    $output = array();

    for ( $i=0; $i<=$interfal; $i++ )
    {
        $output[$i] = ($i === 0) ? date('Y') : date('Y', mktime(0, 0, 0, $i, 1, date('Y')-$i));
    }

    return $output;
}

// -----------------------------------------------------------------------------
// Twitter Bootstrap helper
//
// Just another simplify to use twbs
// -----------------------------------------------------------------------------

/**
 * TWBS Label
 * 
 * @param   string
 * @param   string
 * @param   string
 * 
 * @return  string
 */
function twb_label( $text, $class = 'default', $tag = 'span' )
{
    return '<'.$tag.' class="label label-'.$class.'">'.$text.'</'.$tag.'>';
}

// -----------------------------------------------------------------------------

/**
 * TWBS Badge
 * 
 * @param   string
 * @param   string
 * 
 * @return  string
 */
function twb_badge( $text, $tag = 'span' )
{
    return '<'.$tag.' class="badge">'.$text.'</'.$tag.'>';
}

// -----------------------------------------------------------------------------

/**
 * TWBS Text
 * 
 * @param   string
 * @param   string
 * @param   string
 * 
 * @return  string
 */
function twb_text( $text, $class = '', $tag = 'span' )
{
    return '<'.$tag.' class="text-'.$class.'">'.$text.'</'.$tag.'>';
}

// -----------------------------------------------------------------------------

function make_tag( $texts, $limit = 10 )
{
    $out = '';
    $i   = 0;

    foreach ( explode(',', $texts) as $text )
    {
        $out .= twb_label( $text, 'info' ).' ';

        if (++$i == $limit) break;
    }

    return $out;
}

// -----------------------------------------------------------------------------

/**
 * CI default get spesific config item with 'baka_' prefix
 *
 * @param   string  $name  Config name
 *
 * @return  mixed
 */
function get_conf( $name )
{
    return config_item( 'baka_'.$name );
}

/* End of file common_helper.php */
/* Location: ./application/helpers/baka_pack/common_helper.php */