<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengguna extends BAKA_Controller
{
	private $current_user;

	public function __construct()
	{
		parent::__construct();

		$this->baka_theme->add_navbar( 'admin_sidebar', 'nav-tabs nav-stacked nav-tabs-right', 'side' );
		$this->app_main->admin_navbar( 'admin_sidebar', 'side' );

		$this->data['load_toolbar']	= TRUE;
		$this->data['page_link']	= 'admin/pengguna/';

		// if ( ! $this->load->is_loaded('table'))
		// 	$this->load->library('table');

		$this->current_user = $this->baka_auth->get_user_id();

		// $this->table->set_template( array('table_open' => '<table class="table table-striped table-hover table-condensed">' ) );

		$this->load->model('Baka_pack/app_users');
	}

	public function index()
	{
		$this->data();
	}

	public function data( $page = '', $user_id = '' )
	{
		$this->data['page_link'] .= 'data/';

		switch ( $page ) {
			case 'form':
				$this->_user_form( $user_id );
				break;

			case 'cekal':
				$this->_user_ban( $user_id );
				break;

			case 'hapus':
				$this->_user_del( $user_id );
				break;

			case 'data':
			default:
				$this->_user_table();
				break;
		}
	}

	private function _user_del( $user_id )
	{
		if ( $this->baka_users->delete_user( $user_id ) )
		{
			$this->session->set_flashdata('success', 'Berhasil menghapus keseluruhan data pengguna');
			redirect( $this->data['page_link'] );
		}
		else
		{
			$this->session->set_flashdata('error', 'Terjadi kesalahan penghapusan data pengguna');
			redirect( current_url() );
		}
	}

	private function _user_ban( $user_id )
	{
		$user = $this->baka_users->get_users( $user_id )->row();

		$username = strlen($user->fullname) > 0 ? $user->fullname : $user->username;

		$this->data['panel_title']	= $this->baka_theme->set_title('Cekal pengguna: '.$username);

		$fields[]	= array(
			'name'	=> 'ban-user',
			'type'	=> 'text',
			'label'	=> 'Nama Pengguna',
			'attr'	=> 'disabled',
			'std'	=> $username );

		$fields[]	= array(
			'name'	=> 'ban-reason',
			'type'	=> 'textarea',
			'label'	=> 'Alasan pencekalan',
			'desc'	=> 'Mohon tuliskan secara lengkap alasan pencekalan pengguna "'.$username.'".',
			'validation'=> 'required' );

		$form = $this->baka_form->add_form( current_url(), 'ban' )
								->add_fields( $fields );

		if ( $form->validate_submition() )
		{
			$form_data	=  $form->submited_data();

			$result		= $this->app_users->ban_user( $user_id, $form_data['ban-reason'] );

			if ( $result )
			{
				$this->session->set_flashdata('success', $this->baka_lib->message());
				redirect( $this->data['page_link'] );
			}
			else
			{
				$this->session->set_flashdata('error', $this->baka_lib->errors());
				redirect( current_url() );
			}
		}

		$this->data['panel_body'] = $form->render();

		$this->baka_theme->load('pages/panel_form', $this->data);
	}

	private function _user_table()
	{
		$query = $this->baka_users->get_users();

		$this->data['tool_buttons']['form'] = 'Baru|primary';

		$this->data['panel_title']	= $this->baka_theme->set_title('Semua data pengguna');
		$this->data['counter']		= $query->num_rows();

		$this->load->library('Baka_pack/baka_grid');

		$grid = $this->baka_grid->identifier('id')
								->set_baseurl($this->data['page_link'])
								->set_column('Pengguna', 'username, fullname', '45%', FALSE, '<strong>%s</strong><br><small class="text-muted">%s</small>')
								->set_column('Email', 'email', '40%', FALSE, '%s')
								->set_buttons('form/', 'eye-open', 'primary', 'Lihat data')
								->set_buttons('delete/', 'trash', 'danger', 'Hapus data');
						  
		$this->data['panel_body'] = $grid->make_table( $this->baka_users->get_users() );

		$this->baka_theme->load('pages/panel_data', $this->data);
	}

	public function profile()
	{
		$this->_user_form( $this->baka_auth->get_user_id() );
	}

	private function _user_form( $user_id = '' )
	{
		if ( $user_id == $this->current_user AND strpos( current_url(), 'profile') === FALSE )
			redirect('profile');

		$user	= ( $user_id != '' ? $this->baka_users->get_users( $user_id )->row() : FALSE );
		$judul	= ( $user_id == $this->current_user ? 'Profile anda: ' : 'Data pengguna: ');

		$this->data['panel_title'] = $this->baka_theme->set_title( ( $user ? $judul.( strlen($user->fullname) > 0 ? $user->fullname : $user->username ) : 'Buat pengguna baru' ));
		
		$this->data['tool_buttons']['data'] = 'Kembali|default';

		if ( $user )
		{
			if ( $user_id != $this->current_user )
			{
				$this->data['tool_buttons']['aksi|danger']	= array(
					'cekal/'.$user_id => 'Cekal',
					'hapus/'.$user_id => 'Hapus' );
			}

			$fields[]	= array(
				'name'	=> 'user-fullname',
				'type'	=> 'text',
				'label'	=> 'Nama lengkap',
				'std'	=> $user->fullname );
		}

		$use_username = (bool) get_app_config('use_username');

		if ( $use_username )
		{
			$username_min_length	= get_app_setting('auth_username_min_length');
			$username_max_length	= get_app_setting('auth_username_max_length');

			$fields[]	= array(
				'name'	=> 'user-username',
				'type'	=> 'text',
				'label'	=> 'Username',
				'std'	=> ( $user ? $user->username : '' ),
				'desc'	=> ( ! $user ? 'Username harus diisi dengan minimal '.$username_min_length.' dan maksimal '.$username_max_length.' karakter.' : '' ),
				'validation'=> ( ! $user ? 'required|is_username_blacklist|is_username_available|min_length['.$username_min_length.']|max_length['.$username_max_length.']' : '' ) );
		}

		$fields[]	= array(
			'name'	=> 'user-email',
			'type'	=> 'email',
			'label'	=> 'Email',
			'std'	=> ( $user ? $user->email : '' ),
			'desc'	=> ( ! $user ? 'Dengan alasan keamanan mohon gunakan email aktif anda.' : '' ),
			'validation'=> 'valid_email'.( ! $user ? '|required|is_email_available' : '' ) );

		if ( $user )
		{
			$fields[]	= array(
				'name'	=> 'app-fieldset-password',
				'type'	=> 'fieldset',
				'label'	=> 'Ganti password' );

			$fields[]	= array(
				'name'	=> 'user-last-password',
				'type'	=> 'password',
				'label'	=> 'Password lama' );
		}

		$password_min_length	= get_app_setting('auth_password_min_length');
		$password_max_length	= get_app_setting('auth_password_max_length');

		$fields[]	= array(
			'name'	=> 'user-new-password',
			'type'	=> 'password',
			'label'	=> ( ! $user ? 'Password' : 'Password baru' ),
			'desc'	=> ( ! $user ? 'Password harus diisi dengan minimal '.$password_min_length.' dan maksimal '.$password_max_length.' karakter.' : '' ),
			'validation'=> ( ! $user ? 'required|' : '' ).'min_length['.$password_min_length.']|max_length['.$password_max_length.']');

		$fields[]	= array(
			'name'	=> 'user-confirm-password',
			'type'	=> 'password',
			'label'	=> 'Konfirmasi Password',
			'desc'	=> ( ! $user ? 'Ulangi penulisan password diatas.' : '' ),
			'validation'=> ( ! $user ? 'required|' : '' ).'matches[user-new-password]');

		if ( $user )
		{
			$fields[]	= array(
				'name'	=> 'app-fieldset-roles',
				'type'	=> 'fieldset',
				'label'	=> 'Hak Akses' );

			$roles_option = array();

			foreach ( $this->baka_users->get_groups()->result() as $role )
			{
				$roles_option[$role->id] = $role->fullname;
			}

			$fields[]	= array(
				'name'	=> 'user-roles',
				'type'	=> 'checkbox',
				'label'	=> 'Kelompok pengguna',
				'option'=> $roles_option,
				'std'	=> ( $user ? explode(',', $user->role_id) : '' ),
				'validation'=> ( ! $user ? 'required' : '' ) );

			$fields[]	= array(
				'name'	=> 'app-fieldset-status',
				'type'	=> 'fieldset',
				'label'	=> 'Status' );

			$fields[]	= array(
				'name'	=> 'user-activated',
				'type'	=> 'static',
				'label'	=> 'Aktif',
				'std'	=> (bool) $user->activated ? twb_label('Ya', 'success') : twb_label('Tidak', 'danger') );

			$fields[]	= array(
				'name'	=> 'user-last-login',
				'type'	=> 'static',
				'label'	=> 'Dibuat pada',
				'std'	=> format_datetime($user->created) );

			$fields[]	= array(
				'name'	=> 'user-banned',
				'type'	=> 'static',
				'label'	=> 'Dicekal',
				'std'	=> (bool) $user->banned ? twb_label('Ya', 'danger') : twb_label('Tidak', 'success') );

			if ( (bool) $user->banned )
			{
				$fields[]	= array(
					'name'	=> 'user-ban-reason',
					'type'	=> 'static',
					'label'	=> 'Alasan pencekalan',
					'std'	=> $user->ban_reason );
			}

			$fields[]	= array(
				'name'	=> 'user-last-login',
				'type'	=> 'static',
				'label'	=> 'Login terakhir',
				'std'	=> format_datetime($user->last_login) );
		}

		$form = $this->baka_form->add_form( current_url(), 'user' )
								->add_fields( $fields );

		if ( $form->validate_submition() )
		{
			if ( ! $user )
			{
				$form_data	=  $form->submited_data();

				$user_data['username']	= $form_data['user-username'];
				$user_data['email'] 	= $form_data['user-email'];
				$user_data['password']	= $form_data['user-new-password'];

				$result		= $this->app_users->create_user( $user_data, $use_username );
			}
			else
			{
				$result		= $this->app_users->update_user( $user_id, $form->submited_data(), $use_username );
			}

			if ( $result )
			{
				$this->session->set_flashdata('success', $this->baka_lib->message());
				redirect( $this->data['page_link'] );
			}
			else
			{
				$this->session->set_flashdata('error', $this->baka_lib->errors());
				redirect( current_url() );
			}
		}

		$this->data['panel_body'] = $form->render();

		$this->baka_theme->load('pages/panel_form', $this->data);
	}

	public function groups( $page = '', $group_id = '' )
	{
		$this->data['page_link']	.= 'groups/';

		switch ( $page ) {
			case 'form':
				$this->_group_form( $group_id );
				break;

			case 'hapus':
				$this->_group_del( $group_id );
				break;

			case 'data':
			default:
				$this->_group_table();
				break;
		}
	}

	private function _group_del( $perm_id )
	{
		// 
	}

	private function _group_form( $group_id = NULL )
	{
		$group	= (! is_null( $group_id ) ? $this->baka_users->get_groups( $group_id )->row() : FALSE );

		$this->data['panel_title'] = $this->baka_theme->set_title( $group ? 'Ubah data Kelompok pengguna '.$group->fullname : 'Buat kelompok pengguna baru' );
		
		$this->data['tool_buttons']['data'] = 'Kembali|default';

		$fields[]	= array(
			'name'	=> 'group-full',
			'type'	=> 'text',
			'label'	=> 'Nama lengkap',
			'std'	=> ( $group ? $group->fullname : '' ),
			'desc'	=> 'Nama lengkap untuk kelompok pengguna, diperbolehkan menggunakan spasi.',
			'validation'=> ( !$group ? 'required' : '' ) );

		$fields[]	= array(
			'name'	=> 'group-role',
			'type'	=> 'text',
			'label'	=> 'Nama singkat',
			'std'	=> ( $group ? $group->name : '' ),
			'desc'	=> 'Nama singkan untuk kelompok pengguna, tidak diperbolehkan menggunakan spasi.',
			'validation'=> ( !$group ? 'required' : '' ) );

		$fields[]	= array(
			'name'	=> 'group-default',
			'type'	=> 'radiobox',
			'label'	=> 'Jadikan default',
			'option'=> array(
				0 => 'Tidak',
				1 => 'Ya'),
			'std'	=> ( $group ? $group->default : 0 ),
			'desc'	=> 'Pilih <em>Ya</em> untuk menjadikna group ini sebagai group bawaan setiap mendambahkan pengguna baru, atau pilih <em>Tidak</em> untuk sebaliknya.',
			'validation'=> ( !$group ? 'required' : '' ) );

		$fields[]	= array(
			'name'	=> 'group-fieldset-perms',
			'type'	=> 'fieldset',
			'label'	=> 'Wewenang Kelompok' );

		foreach ( $this->baka_users->get_perms()->result() as $perms )
		{
			$child_id	= strpos($perms->perm_id, ',') !== FALSE	? explode(',', $perms->perm_id) : array($perms->perm_id) ;
			$child_name	= strpos($perms->perm_name, ',') !== FALSE ? explode(',', $perms->perm_name) : array($perms->perm_name) ;
			$child_desc	= strpos($perms->perm_desc, ',') !== FALSE ? explode(',', $perms->perm_desc) : array($perms->perm_desc) ;

			$fields[]	= array(
				'name'	=> 'group-perms-'.str_replace(' ', '-', strtolower($perms->parent)),
				'type'	=> 'checkbox',
				'label'	=> $perms->parent,
				'option'=> array_combine($child_id, $child_desc),
				'std'	=> ( $group ? explode(',', $group->perm_id) : 0 ),
				'desc'	=> '' );
		}

		$form = $this->baka_form->add_form( current_url(), 'group' )
								->add_fields( $fields );

		if ( $form->validate_submition() )
		{
			if ( $user_id == '' )
			{
				$form_data	=  $form->submited_data();

				$user_data['role']		= $form_data['group-role'];
				$user_data['full'] 		= $form_data['group-full'];
				$user_data['default']	= $form_data['group-default'];

				$result = $this->app_users->create_user( $user_data, $use_username );
			}
			else
			{
				$result = $this->app_users->update_user( $user_id, $form->submited_data(), $use_username );
			}

			if ( $result )
			{
				$this->session->set_flashdata('success', $this->baka_lib->message());
				redirect( $this->data['page_link'] );
			}
			else
			{
				$this->session->set_flashdata('error', $this->baka_lib->errors());
				redirect( current_url() );
			}
		}

		$this->data['panel_body'] = $form->render();

		$this->baka_theme->load('pages/panel_form', $this->data);
	}

	private function _group_table()
	{
		$this->data['panel_title'] = $this->baka_theme->set_title('Semua data kelompok pengguna');
		$this->data['tool_buttons']['form'] = 'Baru|primary';

		$this->load->library('Baka_pack/baka_grid');

		$grid = $this->baka_grid->identifier('id')
								->set_baseurl($this->data['page_link'])
								->set_column('Kelompok', 'fullname', '65%', TRUE)
								->set_column('Default', 'callback_bool_to_str:default|Default', '20%', FALSE, '<span class="badge">%s</span>')
								->set_buttons('form/', 'eye-open', 'primary', 'Lihat data')
								->set_buttons('delete/', 'trash', 'danger', 'Hapus data');

		$this->data['panel_body'] = $grid->make_table( $this->baka_users->get_groups() );

		$this->baka_theme->load('pages/panel_data', $this->data);
	}

	public function permission( $page = '', $perm_id = '' )
	{
		switch ( $page ) {
			case 'form':
				$this->_perm_form( $perm_id );
				break;
			
			case 'hapus':
				$this->_perm_del( $perm_id );
				break;
			
			case 'data':
			case 'page':
			default:
				$this->_perm_table();
				break;
		}
	}

	private function _perm_del( $perm_id )
	{
		// 
	}

	private function _perm_form( $perm_id = '' )
	{
		$perm	= (! is_null( $perm_id ) ? $this->baka_users->get_perms( $perm_id )->row() : FALSE );

		$this->data['panel_title'] = $this->baka_theme->set_title( $perm ? 'Ubah data Kelompok pengguna '.$perm->fullname : 'Buat kelompok pengguna baru' );
		
		$this->data['tool_buttons']['data'] = 'Kembali|default';

		$fields[]	= array(
			'name'	=> 'perm-full',
			'type'	=> 'text',
			'label'	=> 'Nama lengkap',
			'std'	=> ( $perm ? $perm->fullname : '' ),
			'desc'	=> 'Nama lengkap untuk kelompok pengguna, diperbolehkan menggunakan spasi.',
			'validation'=> ( !$perm ? 'required' : '' ) );

		$fields[]	= array(
			'name'	=> 'perm-role',
			'type'	=> 'text',
			'label'	=> 'Nama singkat',
			'std'	=> ( $perm ? $perm->name : '' ),
			'desc'	=> 'Nama singkan untuk kelompok pengguna, tidak diperbolehkan menggunakan spasi.',
			'validation'=> ( !$perm ? 'required' : '' ) );

		$fields[]	= array(
			'name'	=> 'perm-default',
			'type'	=> 'radiobox',
			'label'	=> 'Jadikan default',
			'option'=> array(
				0 => 'Tidak',
				1 => 'Ya'),
			'std'	=> ( $perm ? $perm->default : 0 ),
			'desc'	=> 'Pilih <em>Ya</em> untuk menjadikna perm ini sebagai perm bawaan setiap mendambahkan pengguna baru, atau pilih <em>Tidak</em> untuk sebaliknya.',
			'validation'=> ( !$perm ? 'required' : '' ) );

		$form = $this->baka_form->add_form( current_url(), 'perm' )
								->add_fields( $fields );

		if ( $form->validate_submition() )
		{
			if ( $user_id == '' )
			{
				$form_data	=  $form->submited_data();

				$user_data['role']		= $form_data['perm-role'];
				$user_data['full'] 		= $form_data['perm-full'];
				$user_data['default']	= $form_data['perm-default'];

				$result = $this->app_users->create_user( $user_data, $use_username );
			}
			else
			{
				$result = $this->app_users->update_user( $user_id, $form->submited_data(), $use_username );
			}

			if ( $result )
			{
				$this->session->set_flashdata('success', $this->baka_lib->message());
				redirect( $this->data['page_link'] );
			}
			else
			{
				$this->session->set_flashdata('error', $this->baka_lib->errors());
				redirect( current_url() );
			}
		}

		$this->data['panel_body'] = $form->render();

		$this->baka_theme->load('pages/panel_form', $this->data);
	}

	private function _perm_table()
	{
		$this->data['page_link']	= 'admin/pengguna/permission/';
		$this->data['panel_title']	= $this->baka_theme->set_title('Semua data hak akses pengguna');
		$this->data['tool_buttons']['form'] = 'Baru|primary';

		$this->load->library('Baka_pack/baka_grid');

		$grid = $this->baka_grid->identifier('permission_id')
								->set_baseurl($this->data['page_link'])
								->set_column('Hak akses', 'permission, description', '45%', TRUE, '<strong>%s</strong><br>%s')
								->set_column('Keterangan', 'parent', '40%')
								->set_buttons('form/', 'eye-open', 'primary', 'Lihat data')
								->set_buttons('delete/', 'trash', 'danger', 'Hapus data');

		$this->data['panel_body'] = $grid->make_table( $this->baka_users->get_perms_query() );

		$this->baka_theme->load('pages/panel_data', $this->data);
	}

	private function _act_btn( $form_link, $delete_link )
	{
		$class	= 'bs-tooltip btn btn-sm btn-';
		$icons	= 'glyphicon glyphicon-';

		$output	= '<div class="btn-group btn-group-justified">'
				. anchor( $this->data['page_link'].$form_link, '<span class="'.$icons.'eye-open">', 'title="Lihat data" class="'.$class.'primary"' )
				. anchor( $this->data['page_link'].$delete_link, '<span class="'.$icons.'trash">', 'title="Hapus data" class="'.$class.'danger"')
				. '</div>';

		return $output;
	}
}

/* End of file pengguna.php */
/* Location: ./application/controllers/pengguna.php */