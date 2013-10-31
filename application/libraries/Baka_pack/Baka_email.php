<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Baka_email Extends Baka_lib
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('email');

		$this->initialize();

		log_message('debug', "Baka_email Class Initialized");
	}

	public function initialize()
	{
		$config['protocol']			= get_app_setting('email_protocol');
		$config['mailpath']			= get_app_setting('email_mailpath');
		$config['smtp_host']		= get_app_setting('email_smtp_host');
		$config['smtp_user']		= get_app_setting('email_smtp_user');
		$config['smtp_pass']		= get_app_setting('email_smtp_pass');
		$config['smtp_port']		= get_app_setting('email_smtp_port');
		$config['smtp_timeout']		= get_app_setting('email_smtp_timeout');
		$config['wordwrap']			= get_app_setting('email_wordwrap');
		$config['wrapchars']		= 80;
		$config['mailtype']			= get_app_setting('email_mailtype');
		$config['charset']			= 'utf-8';
		$config['validate']			= TRUE;
		$config['priority']			= get_app_setting('email_priority');
		$config['crlf']				= "\r\n";
		$config['newline']			= "\r\n";
		
		$this->email->initialize($config);
	}

	public function send( $email_reciever, $subject, &$data )
	{
		$this->email->from( get_app_setting('skpd_email'), get_app_setting('skpd_name') );
		$this->email->reply_to( get_app_setting('skpd_email'), get_app_setting('skpd_name') );
		
		$this->email->to( $email_reciever );
		$this->email->cc( get_app_config('app_author_email') );
		
		$this->email->subject(sprintf($this->lang->line('auth_subject_'.$subject), $this->config->item('website_name', 'tank_auth')));
		$this->email->message($this->load->view('email/'.$subject.'-html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('email/'.$subject.'-txt', $data, TRUE));
		
		$this->email->send();
	}
}

/* End of file Baka_email.php */
/* Location: ./system/application/libraries/Baka_pack/Baka_email.php */