<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_auth extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

        $this->load->library( 'session' );
    }

    /**
     *  Function check
     *  @return false / user data
     */
    function check( $redirect = TRUE )
    {
        $udata = $this->session->userdata( 'logged_in' );

        if( $udata['valid'] == 'yes' )
        {
            return $udata;
        }
        else
        {
            if( $redirect )
            {
                redirect( base_url() );
                die();
            }
            return FALSE;
        }
    }


    /** 
     *  Function login
     *  Makes the user logged in, by updating the session     
     */
    function login( $compData )
    {
        $this->session->set_userdata( 'logged_in', $compData );
    }


    /** 
     *  Function logout
     *  Makes the user logged out, by updating the session     
     */
   function logout()
    {
        $data = array(
					'valid' => 'no',
					'uid'   => FALSE,
                    'name'  => '');
    
    	$this->session->set_userdata( 'logged_in', $data );
    }    
}

/* End of file model_auth.php */
/* Location: ./application/models/model_auth.php */