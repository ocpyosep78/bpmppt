<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 

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

/**
 * BAKA Errror Class
 *
 * PHP-Error handler for Codeigniter
 * PHP Error cannot take over 100% of CI's errors, at least not out of the box, but can
 * still be used for general PHP errors, such as for pointing out undefined variables
 *
 * @link        https://github.com/JosephLenton/PHP-Error/wiki/Example-Setup#code-igniter
 * @subpackage  Hooks
 * @category    Errors
 */
class Errror
{
    /**
     * Codeigniter superobject
     *
     * @var  mixed
     */
    protected static $_ci;

    /**
     * PHP-Error Options
     *
     * @link  https://github.com/JosephLenton/PHP-Error/wiki/Options
     * @var   array
     */
    protected $_config = array(
        // 'clear_all_buffers'         => FALSE,
        'application_folders'       => '',
        'application_root'          => '',
        'background_text'           => '',
        'catch_ajax_errors'         => TRUE,
        'catch_supressed_errors'    => FALSE,
        'catch_class_not_found'     => TRUE,
        'display_line_numbers'      => TRUE,
        'enable_saving'             => FALSE,
        // 'error_reporting_on'        => -1,
        // 'error_reporting_off'       => '',
        'ignore_folders'            => '',
        'save_url'                  => '',
        'server_name'               => '',
        'wordpress'                 => FALSE,
        );

    /**
     * Default class constructor
     */
    public function __construct()
    {
        self::$_ci =& get_instance();

        /**
         * PHP_Error Options Array
         *
         * @link  https://github.com/JosephLenton/PHP-Error/wiki/Options
         * @var   array
         */
        $this->_config['application_folders']   = APPPATH;
        $this->_config['ignore_folders']        = BASEPATH;

        if (!class_exists('\php_error\ErrorHandler'))
        {
            require_once(APPPATH.'libraries/vendor/php_error'.EXT);
        }

        log_message('debug', "#Baka_pack: Errror Class Initialized");
    }

    /**
     * Error handler method
     *
     * @return  void
     */
    public function reload()
    {
        if (class_exists('\php_error\ErrorHandler'))
		{
            $handler = new \php_error\ErrorHandler($this->_config);

            switch (ENVIRONMENT)
            {
                case 'development':
                    $handler->turnOn();
                break;
            
                case 'testing':
                case 'production':
                    $handler->turnOff();
                break;
            }
        }
    }
}

/* End of file Errror.php */
/* Location: ./application/hooks/Errror.php */
