<?php

/****************************************************************************
 *  generate.php
 *  Generates the application
 *  =========================================================================
 *  Copyright 2012 Tibor Szász
 *  This file is part of iScaffold.
 *
 *  GNU GPLv3 license
 *
 *  iScaffold is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  iScaffold is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with iScaffold.  If not, see <http://www.gnu.org/licenses/>.
 *
 ****************************************************************************/

class Generate extends CI_Controller  {

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('dircopy');
		$this->load->helper('string');
		$this->load->model('model_iscaffold');
		$this->load->model('conf_model');
		$this->load->model('idb');
   	}

   	/**
   	 *	This method is called via AJAX
   	 */
	function index( $database, $code_template )
	{
		$data_path = array();
		$data_path['code_template'] = $code_template;

	    $this->idb->connect( $database );

		$manifest = json_decode( file_get_contents( 'templates'.DS.$code_template.DS.'manifest.json' ), TRUE );

		$path_output = $manifest['output_directory'].DS;

		// Load the folder model
		$this->load->model('folder_model');
		
		// Get the folder permissions
		$folder_info = $this->folder_model->check_permissions( $manifest['output_directory'] );

		// Validate the folder permissions
		if ( $folder_info['is_writeable'] == true ) 
		{
			$tables = $this->db->list_tables();

			$path_templates	= 'templates';

			/**
			 *	Create input / output paths for the model_iscaffold.
			 */
			foreach ( $manifest['working_directories'] as $dir ) 
			{
				if( is_array( $dir ) )
				{
					list( $source, $target ) = $dir;
					$data_path[ 'input_' . $dir[0] ]  = $path_templates.DS.$code_template.DS.$manifest['working_root_directory'].DS.$source.DS;
					$data_path[ 'output_' . $dir[0] ] = $path_output.DS.$manifest['working_root_directory'].DS.$target.DS;
				}
				else
				{
					$data_path[ 'input_' . $dir ]  = $path_templates.DS.$code_template.DS.$manifest['working_root_directory'].DS.$dir.DS;
					$data_path[ 'output_' . $dir ] = $path_output.DS.$manifest['working_root_directory'].DS.$dir.DS;
				}
			}

			/**
			 *	 Nuke the output directory if neccessery
			 */
			if( $manifest['dump_output_directory'] === TRUE )
			{
				delete_files( $path_output, TRUE );
			}

			@mkdir( $path_output, 0777 );

			/**
			 *	Copdy additional resources
			 */
			foreach ( $manifest['copy_directories'] as $dir ) 
			{
				dircopy( $dir, $path_output );
			}

			/**
			 *	This is wehere the code generation is invoked
			 *	Each table is processed here
			 */
			foreach( $tables as $table )
			{
				if( $table !== 'sf_config' ) $this->model_iscaffold->Process_Table( $table, $data_path, $code_template, $manifest );
			}

			echo '{ "result": "success" }';
		}

		// The output directory isn't writeable, redirect to the main page
		else 
		{
			echo '{ "result": "error", "message": "There was a problem generating your application, ther output directory <strong>('.$manifest['output_directory'].')</strong> is not writable." }';
		}
	}
}

/* End of file generate.php */
