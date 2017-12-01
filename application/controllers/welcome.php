<?php

/****************************************************************************
 *  welcome.php
 *  Default controller, shows the default view
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

class Welcome extends CI_Controller  {

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('dircopy');
		$this->load->helper('string');
		$this->load->model('model_iscaffold');
		$this->load->model('conf_model');
   	}

	function index( $message = '', $database = FALSE ){

		$this->load->model('folder_model');

		// Check the folder permissions
		$folder_info = $this->folder_model->check_permissions('./output/');
		$code_templates = $this->folder_model->getCodeTemplates();

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
			'code_templates'=> $code_templates,
		);

		// Load the view
		$this->load->view( 'welcome_view', $data );
	}
}
/* End of file welcome.php */
