<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
    
	    $this->load->database();
		$this->load->library( 'template' );
		$this->load->helper( 'url' );

        $this->rootUser = 'root';
        $this->rootPass = 'root';

        // Quick logged in test, if check() recieves true, then it redirects to the index page
		$this->load->model( 'model_auth' );
		$this->template->assign( 'logged_in', $this->model_auth->check( FALSE ) );
	}

	function index( $error = FALSE )
	{
        if( $error )
        {
            $this->template->assign( 'error', TRUE );
        }
        $this->template->assign( 'template', 'form_login' );
        $this->template->display( 'frame_admin.tpl' );
    }


    /**
     *  Validate login
     */
    function validate()
    {
        $user = $this->input->post( 'user' );
        $pass = $this->input->post( 'pass' ); 
        
        if( $this->rootUser == $user && $this->rootPass == $pass )
        {
            $this->model_auth->login( array( 'valid' => 'yes' ) );
            redirect( base_url() . '/dashboard' );
            die();
        }
        else
        {
            $this->model_auth->login( array('valid' => 'no') );
            $this->index( TRUE );
        }
    }


	function logout()
	{
        $this->model_auth->logout();
        redirect( base_url() . '/login' );
    }
}
