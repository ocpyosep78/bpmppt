<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
 * Maintenance Class
 *
 * @subpackage  Controller
 */
class Maintenance extends BAKA_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->verify_login();

        if ( !is_permited('sys_manage') )
            $this->_notice( 'access-denied' );

        $this->themee->add_navbar( 'admin_sidebar', 'nav-tabs nav-stacked nav-tabs-right', 'side' );
        $this->admin_navbar( 'admin_sidebar', 'side' );

        $this->themee->set_title('System Maintenance');
    }

    public function index()
    {
        $this->dbbackup();
    }

    public function dbbackup()
    {
        if ( !is_permited('sys_backstore_manage') )
            $this->_notice( 'access-denied' );

        $this->data['panel_title'] = $this->themee->set_title('Backup Database');

        $fields[]   = array(
            'name'  => 'db-driver',
            'type'  => 'static',
            'label' => 'Database driver',
            'std'   => $this->db->dbdriver );

        $fields[]   = array(
            'name'  => 'host-info',
            'type'  => 'static',
            'label' => 'Host info',
            'std'   => $this->db->conn_id->host_info );

        $fields[]   = array(
            'name'  => 'server-info',
            'type'  => 'static',
            'label' => 'Server info',
            'std'   => $this->db->conn_id->server_info.' Version. '.$this->db->conn_id->server_version );

        $fields[]   = array(
            'name'  => 'backup-all',
            'type'  => 'checkbox',
            'label' => 'Backup semua tabel',
            'option'=> array('semua' => 'Semua'),
            'validation'=>'required' );

        $buttons[]= array(
            'name'  => 'do-backup',
            'type'  => 'submit',
            'label' => 'lang:backup_btn',
            'class' => 'btn-primary pull-right' );

        $this->load->library('baka_pack/former');

        $form = $this->former->init( array(
            'name'      => 'backup',
            'action'    => current_url(),
            'fields'    => $fields,
            'buttons'   => $buttons,
            ));

        if ( $form_data = $form->validate_submition() )
        {
            $this->load->library('baka_pack/baka_dbutil');

            if ( $this->baka_dbutil->backup() )
            {
                $this->session->set_flashdata('success', $this->baka_lib->messages());
            }
            else
            {
                $this->session->set_flashdata('error', $this->baka_lib->errors());
            }
            
            redirect( current_url() );
        }

        $this->data['panel_body'] = $form->generate();

        $this->load->theme('pages/panel_form', $this->data);
    }

    public function dbrestore()
    {
        if ( !is_permited('sys_backstore_manage') )
            $this->_notice( 'access-denied' );

        $this->data['panel_title']  = $this->themee->set_title('Restore Database');

        $fields[]   = array(
            'name'  => 'restore-from-file',
            'type'  => 'upload',
            'label' => 'Restore dari berkas',
            'file_limit' => 1,
            'allowed_types' => 'zip|sql',
            'desc'  => 'Pilih berkas yang akan digunakan untuk me-restore database' );

        $buttons[]= array(
            'name'  => 'do-restore',
            'type'  => 'submit',
            'label' => 'lang:restore_btn',
            'class' => 'btn-primary pull-right' );

        $this->load->library('baka_pack/former');

        $form = $this->former->init( array(
            'name'      => 'restore',
            'action'    => current_url(),
            'fields'    => $fields,
            'buttons'   => $buttons,
            ));

        if ( $form_data = $form->validate_submition() )
        {
            // $this->load->library('baka_pack/baka_dbutil');

            // if ( $this->baka_dbutil->restore_upload('restore-from-file') )
            // {
            //     $this->session->set_flashdata('success', $this->baka_lib->messages());
            // }
            // else
            // {
            //     $this->session->set_flashdata('error', $this->baka_lib->errors());
            // }

            redirect( current_url() );
        }

        $this->data['panel_body'] = $form->generate();

        $this->load->theme('pages/panel_form', $this->data);
    }

    public function syslogs( $file = '' )
    {
        if ( !is_permited('sys_logs_manage') )
            $this->_notice( 'access-denied' );

        $this->load->helper('directory');

        $this->data['panel_title'] = $this->themee->set_title('Aktifitas sistem');

        $this->themee->add_navbar( 'log_sidebar', 'nav-tabs nav-stacked nav-tabs-left', 'panel' );

        $latest   = '';
        $log_path = config_item('log_path');
        $scan_dir = directory_map($log_path);

        arsort( $scan_dir );

        foreach ( $scan_dir as $log )
        {
            if ( $log != 'index.html')
            {
                $log    = strtolower(str_replace(EXT, '', $log));
                $label  = 'Tanggal '.format_date(str_replace('log-', '', $log));
                $link   = 'admin/maintenance/syslogs/';

                $this->themee->add_navmenu( 'log_sidebar', $log, 'link', $link.$log, $label, array(), 'panel' );
            }
        }

        if ( $file != '' )
        {
            if ( !$this->load->is_loaded('file') )
                $this->load->helper('file');
            
            $this->data['panel_title'] .= ' Tanggal '.format_date(str_replace('log-', '', $file));

            foreach ( file( $log_path.$file.EXT, FILE_IGNORE_NEW_LINES ) as $log_line )
            {
                if ( strlen($log_line) > 0 AND !is_null($log_line) AND $log_line != '' )
                {
                    $state = explode(' - ', $log_line);

                    if ( isset($state[1]) )
                    {
                        $date = explode(' --> ', $state[1]);

                        $line[] = array(
                            'time'  => format_time( $date[0] ),
                            'state' => twb_label( $state[0], strtolower( $state[0] ) ),
                            'msg'   => ( strpos( $date[1], 'Severity: ' ) === false)
                                ? $date[1] : twb_label( $date[1], strtolower( $state[0] ) ).' '.$date[2] );
                    }
                }
            }

            $this->data['count_log'] = 'Terdapat '.count( $line ).' catatan error.';

            $this->load->library('table');

            $this->table->set_heading('Waktu', 'Status', 'Pesan');
            $this->table->set_template( array(
                'table_open' => '<table class="table table-striped table-bordered table-hover table-condensed">' ) );

            arsort( $line );

            $this->data['panel_body'] = $this->table->generate( $line );
        }

        $this->load->theme('pages/syslogs', $this->data);
    }
}

/* End of file maintenance.php */
/* Location: ./application/controllers/admin/maintenance.php */