<?php

class Uploader extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

		$config['upload_path']   = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	     = '8192'; // kbytes
		$config['encrypt_name']	 = TRUE;

		$this->load->library( 'upload', $config );
        $this->response = '';
		$this->success  = TRUE;
	}

    /**
     *  Triggers upload
     *  returns the file's details on success or false
     */
	function upload( $field_name )
	{
		if ( !$this->upload->do_upload( $field_name ) )
		{
			$this->success  = FALSE;
            $this->response = $this->upload->display_errors();
			return false;
		}
		else
		{
			$this->success  = TRUE;
			$this->success  = TRUE;
            $this->response = $this->upload->data();
			return $this->response['file_name']; 
		}
    }
}
