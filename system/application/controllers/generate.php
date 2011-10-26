<?php

class Generate extends Controller {

	function Generate()
	{
		parent::Controller();	

		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('dircopy');
		$this->load->helper('string');
		$this->load->model('model_iscaffold');
		$this->load->model('conf_model');
		$this->load->model('idb');
   	}

	function index( $message = '', $database = '' )
	{
		// Load the folder model
		$this->load->database();
		$this->load->model('folder_model');

		// Check the folder permissions
		$folder_info = $this->folder_model->check_permissions('./output/');

        $is_config = $this->conf_model->check_config_table(); 

		// Array for system information		
		$data = array(
			'app_name' 		=> $this->config->item('app_name'),
			'app_codename' 	=> $this->config->item('app_codename'),
			'app_version' 	=> $this->config->item('app_version'),
			'app_website' 	=> $this->config->item('app_website'),
			'message_id' 	=> $folder_info['message_id'],
			'dir_message' 	=> $folder_info['dir_message'],
			'info_message' 	=> $message,
			'is_config' 	=> $is_config,
			'databases'		=> $this->conf_model->list_databases(),
			'database'		=> $database,
		);

		// Load the view
		$this->load->view('welcome_view',$data);
	}


	function create( $database )
	{
	    $this->idb->connect( $database );

		// Load the folder model
		$this->load->model('folder_model');
		
		// Get the folder permissions
		$folder_info = $this->folder_model->check_permissions( './output/' );
		
		// Validate the folder permissions
		if ( $folder_info['is_writeable'] == true ) 
		{		
			$tables = $this->db->list_tables();

			$path_templates				= './templates/';
			$path_input_controller 		= $path_templates . 'controllers/';
			$path_input_model			= $path_templates . 'models/';   
			$path_input_view 			= $path_templates . 'views/';  

			$path_output 				= './output/' . $database . '/';
			$path_output_application 	= $path_output . 'application/';
			$path_output_controller 	= $path_output . 'application/controllers/';
			$path_output_model			= $path_output . 'application/models/';   
			$path_output_view 			= $path_output . 'application/views/';
			$path_output_languages   	= $path_output . 'application/language/';
			$path_output_lang    		= $path_output . 'application/language/english/';

			delete_files( $path_output		, TRUE );
			@mkdir( $path_output 			, 0777 );
			mkdir( $path_output_application , 0777 );
			mkdir( $path_output_controller 	, 0777 );
			mkdir( $path_output_model 		, 0777 );
			mkdir( $path_output_view 		, 0777 );
			mkdir( $path_output_languages	, 0777 );
			mkdir( $path_output_lang 		, 0777 );

			// Copy CodeIgniter application to the output directory
			dircopy( 'repo/codeigniter_203', $path_output );

			// Copy additional files to the CodeIgniter installation
			dircopy( 'repo/ci_extension', $path_output );

			// Copy iScaffold's administration asset files to the target
			dircopy( 'repo/iscaffold_assets', $path_output );

			// Prepare the paths for the model
			$data_path['input_controller'] 	= $path_input_controller;
			$data_path['input_model'] 		= $path_input_model;
			$data_path['input_view'] 		= $path_input_view;
			$data_path['output_controller'] = $path_output_controller;
			$data_path['output_model'] 		= $path_output_model;
			$data_path['output_view'] 		= $path_output_view;

            // Needs no input file, generated from scratch
			$data_path['output_lang'] 		= $path_output_lang; 
	
			foreach ($tables as $table)
			{                                                
				if( $table !== 'sf_config' ) $this->model_iscaffold->Process_Table ( $table, $data_path );
			}

			redirect('generate/index/success/' . $database );
		}

		// The output directory isn't writeable, redirect to the main page
		else {
			redirect('generate/index/directory');
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */