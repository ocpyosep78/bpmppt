<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BPMPPT driver
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
 * @package     BPMPPT
 * @author      Fery Wardiyanto
 * @copyright   Copyright (c) Fery Wardiyanto. (ferywardiyanto@gmail.com)
 * @license     http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @since       Version 1.0
 * @filesource
 */

// =============================================================================

/**
 * BPMPPT Izin Usaha Pariwisata Driver
 *
 * @subpackage  Drivers
 */
class Bpmppt_wisata extends CI_Driver
{
    /**
     * Document property
     *
     * @var  string  $alias
     * @var  string  $name
     */
    public $alias = 'pariwisata';
    public $name = 'Izin Usaha Pariwisata';

    /**
     * Default field
     *
     * @var  array
     */
    public $fields = array(
        'pemohon_nama'      => '',
        'pemohon_kerja'     => '',
        'pemohon_jabatan'   => '',
        'pemohon_alamat'    => '',
        'pemohon_telp'      => '',
        'usaha_nama'        => '',
        'usaha_jenis'       => '',
        'usaha_alamat'      => '',
        'usaha_luas'        => '',
        'usaha_an'          => '',
        'usaha_ket'         => '',
        );

    // -------------------------------------------------------------------------

    /**
     * Default class constructor,
     * Just simply log this class when it loaded
     */
    public function __construct()
    {
        log_message('debug', "#BPMPPT_driver: Usaha_pariwisata Class Initialized");
    }

    // -------------------------------------------------------------------------

    /**
     * Form fields from this driver
     *
     * @param   bool    $data_obj  Data field
     *
     * @return  array
     */
    public function form( $data_obj = FALSE )
    {
        /**
         * @todo
         *
         * Nomor Izin:
         * 557.142/19/PAR/RM/BPMPPT/XI/2013
         * + 557.142 -> Static
         * + 19 -> Nomor urut mulai per tahun
         * + PAR -> static
         * + RM -> Kode Jenis Usaha
         *
         * Bentuk cetak
         * + Surat Ijin
         * + kutipan
         */

        $fields[]   = array(
            'name'  => $this->alias.'_penetapan',
            'label' => 'No. &amp; Tgl. Ditetapkan',
            'type'  => 'subfield',
            'fields'=> array(
                array(
                    'col'   => '6',
                    'name'  => 'nomor',
                    'label' => 'Nomor Ditetapkan',
                    'type'  => 'text',
                    'std'   => ( $data_obj ? $data_obj->pemilik_lahir_tmpt : '' ),
                    'validation'=> ''
                    ),
                array(
                    'col'   => '6',
                    'name'  => 'tgl',
                    'label' => 'Tanggal Ditetapkan',
                    'type'  => 'datepicker',
                    'std'   => ( $data_obj ? $data_obj->pemilik_lahir_tgl : ''),
                    'callback'=> 'string_to_date',
                    'validation'=> ''
                    ),
                ));

        $fields[]   = array(
            'name'  => $this->alias.'_fieldset_data_pemohon',
            'label' => 'Data Pemohon',
            'attr'  => ( $data_obj ? 'disabled' : '' ),
            'type'  => 'fieldset' );

        $fields[]   = array(
            'name'  => $this->alias.'_pemohon_nama',
            'label' => 'Nama lengkap',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->pemohon_nama : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_pemohon_kerja',
            'label' => 'Pekerjaan',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->pemohon_kerja : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_pemohon_jabatan',
            'label' => 'Jabatan',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->pemohon_jabatan : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_pemohon_alamat',
            'label' => 'Alamat',
            'type'  => 'textarea',
            'std'   => ( $data_obj ? $data_obj->pemohon_alamat : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_pemohon_telp',
            'label' => 'Nomor Telpon/HP',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->pemohon_telp : ''),
            'validation'=> 'numeric|max_length[12]' );

        $fields[]   = array(
            'name'  => $this->alias.'_fieldset_data_perusahaan',
            'label' => 'Data Perusahaan',
            'attr'  => ( $data_obj ? 'disabled' : '' ),
            'type'  => 'fieldset' );

        $fields[]   = array(
            'name'  => $this->alias.'_usaha_nama',
            'label' => 'Nama Perusahaan',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->usaha_nama : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_usaha_jenis',
            'label' => 'Jenis Usaha',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->usaha_jenis : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_usaha_alamat',
            'label' => 'Alamat Kantor',
            'type'  => 'textarea',
            'std'   => ( $data_obj ? $data_obj->usaha_alamat : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_usaha_luas',
            'label' => 'Luas perusahaan (M<sup>2</sup>)',
            'type'  => 'number',
            'std'   => ( $data_obj ? $data_obj->usaha_luas : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_usaha_an',
            'label' => 'Atas Nama Pendirian',
            'type'  => 'text',
            'std'   => ( $data_obj ? $data_obj->usaha_an : '') );

        $fields[]   = array(
            'name'  => $this->alias.'_usaha_ket',
            'label' => 'Keterangan Lain',
            'type'  => 'textarea',
            'std'   => ( $data_obj ? $data_obj->usaha_ket : '') );

        return $fields;
    }

    // -------------------------------------------------------------------------

    /**
     * Format cetak produk perijinan
     *
     * @return  mixed
     */
    public function produk()
    {
        return false;
    }

    // -------------------------------------------------------------------------

    /**
     * Format output laporan
     *
     * @return  mixed
     */
    public function laporan()
    {
        return false;
    }
}

/* End of file Bpmppt_wisata.php */
/* Location: ./application/libraries/Bpmppt/drivers/Bpmppt_wisata.php */