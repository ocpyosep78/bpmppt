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
 * BAKA Controller Class
 *
 * @subpackage  Libraries
 * @category    Libraries
 */
class BAKA_Controller extends CI_Controller
{
    protected $current_user;

    protected $_modules_arr = array();

    /**
     * Default class constructor
     */
    function __construct()
    {
        parent::__construct();

        if (Themee::verify_browser() AND !(php_sapi_name() === 'cli' OR defined('STDIN')))
        {
            log_message('error', lang("error_browser_jadul"));
            show_error(array('Peramban yang anda gunakan tidak memenuhi syarat minimal penggunaan aplikasi ini.','Silahkan gunakan '.anchor('http://www.mozilla.org/id/', 'Mozilla Firefox', 'target="_blank"').' atau '.anchor('https://www.google.com/intl/id/chrome/browser/', 'Google Chrome', 'target="_blank"').' biar lebih GREGET!'), 500, 'error_browser_jadul');
        }

        if (Authen::is_logged_in())
        {
            $this->current_user = $this->authen->get_current_user();
            // Adding sub of main and user navbar
            $this->navbar();
        }

        $this->data['load_toolbar'] = FALSE;
        $this->data['search_form']  = FALSE;
        $this->data['single_page']  = TRUE;
        $this->data['form_page']    = FALSE;
        
        $this->data['need_print']   = FALSE;

        $this->data['tool_buttons'] = array();

        $this->data['panel_title']  = '';
        $this->data['panel_body']   = '';

        log_message('debug', "#Baka_pack: Core Controller Class Initialized");
    }

    // -------------------------------------------------------------------------

    /**
     * Redirecting to notice page
     *
     * @param   string  $page  Page name
     *
     * @return  void
     */
    protected function _notice($page)
    {
        redirect('notice/'.$page);
    }

    // -------------------------------------------------------------------------

    /**
     * User login verification
     *
     * @return  void
     */
    protected function verify_login()
    {
        if (!Authen::is_logged_in() AND !Authen::is_logged_in(FALSE))
            redirect('login');
        
        if (Authen::is_logged_in(FALSE))
            redirect('resend');
    }

    // -------------------------------------------------------------------------

    /**
     * User status verification
     *
     * @return  void
     */
    protected function verify_status()
    {
        if (Authen::is_logged_in())
            redirect('data');
        else if (Authen::is_logged_in(FALSE))
           redirect('resend');
    }

    // -------------------------------------------------------------------------

    protected function navbar()
    {
        // Adding main navbar
        $this->themee->add_navbar('main_navbar', 'navbar-nav');
        // Adding user navbar
        $this->themee->add_navbar('user_navbar', 'navbar-nav navbar-right');

        if (is_permited('doc_manage'))
        {
            // Adding dashboard menu to main navbar
            $this->themee->add_navmenu('main_navbar', 'dashboard', 'link', 'data', 'Data Layanan');
            // Adding submenu to main_navbar-data
            // $this->data_navbar('main_navbar-master', 'top');

            $this->load->driver('bpmppt');
        }

        // Adding admin menu to main navbar
        $this->themee->add_navmenu('main_navbar', 'admin', 'link', 'admin/', 'Administrasi');
        // Adding account menu to user navbar
        $this->themee->add_navmenu('user_navbar', 'account', 'link', 'profile', $this->current_user['username']);
        // Adding submenu to main_navbar-admin
        // $this->admin_navbar('main_navbar-admin', 'top');
        // Adding submenu to user_navbar-account
        $this->account_navbar('user_navbar-account', 'top');
    }

    // -------------------------------------------------------------------------

    protected function data_navbar($parent, $position = 'top')
    {
        $link   = 'data/layanan/';
        $nama   = str_replace('/', '_', $link);

        $modules = $this->bpmppt->get_modules();

        if (count($modules ) > 0)
        {
            // Overview
            $this->themee->add_navmenu($parent, 'overview', 'link', 'data/utama', 'Overview', array(), $position);
            // Laporan
            $this->themee->add_navmenu($parent, 'laporan', 'link', 'data/laporan', 'Laporan', array(), $position);
            // Devider
            $this->themee->add_navmenu($parent, $nama.'d', 'devider', '', '', array(), $position);
            // Header
            $this->themee->add_navmenu($parent, 'au_head', 'header', '', 'Data Perizinan', array(), $position);
            // Datas
            foreach ($modules as $class => $prop )
            {
                $this->themee->add_navmenu(
                    $parent,
                    $nama.$prop['alias'],
                    'link',
                    $link.$class,
                    $prop['label'],
                    array(),
                    $position);
            }
        }

        $this->_modules_arr = $this->bpmppt->get_modules_assoc();
    }

    // -------------------------------------------------------------------------

    protected function admin_navbar($parent, $position)
    {
        // Internal settings sub-menu
        // =====================================================================
        // Adding skpd sub-menu (if permited)
        if (is_permited('internal_skpd_manage'))
            $this->themee->add_navmenu($parent, 'ai_skpd', 'link', 'admin/internal/skpd', 'SKPD', array(), $position);

        // Adding application sub-menu (if permited)
        if (is_permited('internal_application_manage'))
            $this->themee->add_navmenu($parent, 'ai_application', 'link', 'admin/internal/app', 'Pengaturan Aplikasi', array(), $position);

        // Adding security sub-menu (if permited)
        // if (is_permited('internal_security_manage'))
        //     $this->themee->add_navmenu($parent, 'ai_security', 'link', 'admin/internal/keamanan', 'Keamanan', array(), $position);

        // $this->themee->add_navmenu(
        // $parent, 'ai_property', 'link', 'admin/internal/prop', 'Properti', array(), $position);

        // Users Management sub-menu (if permited)
        // =====================================================================
        // Adding Users menu header
        $this->themee->add_navmenu($parent, 'au_def', 'devider', '', '', array(), $position);
        $this->themee->add_navmenu(
            $parent, 'au_head', 'header', '', 'Pengguna', array(), $position);
        
        // Adding Self Profile sub-menu
        $this->themee->add_navmenu(
            $parent, 'au_me', 'link', 'profile', 'Profil Saya', array(), $position);

        // Adding Users sub-menu (if permited)
        if (is_permited('users_manage'))
            $this->themee->add_navmenu($parent, 'au_users', 'link', 'admin/pengguna/data', 'Semua Pengguna', array(), $position);

        // Adding Groups sub-menu (if permited)
        if (is_permited('roles_manage'))
            $this->themee->add_navmenu($parent, 'au_groups', 'link', 'admin/pengguna/groups', 'Kelompok', array(), $position);

        // Adding Perms sub-menu (if permited)
        if (is_permited('perms_manage'))
            $this->themee->add_navmenu($parent, 'a_permission', 'link', 'admin/pengguna/permission', 'Hak akses', array(), $position);

        // Application Mantenances sub-menu
        // =====================================================================
        if (is_permited('sys_manage'))
        {
            // Adding System sub-menu (if permited)
            $this->themee->add_navmenu($parent, 'ad_def', 'devider', '', '', array(), $position);
            $this->themee->add_navmenu($parent, 'ad_head', 'header', '', 'Perbaikan', array(), $position);

            // Adding Backup & Restore sub-menu (if permited)
            if (is_permited('sys_backstore_manage'))
            {
                // Backup sub-menu
                $this->themee->add_navmenu(
                    $parent, 'ad_backup', 'link', 'admin/maintenance/dbbackup', 'Backup Database', array(), $position);
                // Restore sub-menu
                $this->themee->add_navmenu(
                    $parent, 'ad_restore', 'link', 'admin/maintenance/dbrestore', 'Restore Restore', array(), $position);
            }

            // Adding System Log sub-menu (if permited)
            if (is_permited('sys_logs_manage'))
                $this->themee->add_navmenu(
                    $parent, 'ad_syslogs', 'link', 'admin/maintenance/syslogs', 'Aktifitas sistem', array(), $position);
        }
    }

    // -------------------------------------------------------------------------

    protected function account_navbar($parent, $position)
    {
        // Adding submenu to user navbar profile
        $this->themee->add_navmenu($parent, 'profilse', 'link', 'profile', $this->current_user['username'], array(), $position);
        $this->themee->add_navmenu($parent, 'user_s', 'devider', '', '', array(), $position);
        $this->themee->add_navmenu($parent, 'user_logout', 'link', 'logout', 'Logout', array(), $position);
    }
}

/* End of file BAKA_Controller.php */
/* Location: ./application/core/BAKA_Controller.php */