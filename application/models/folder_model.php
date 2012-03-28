<?php

/****************************************************************************
 *  folder_model.php
 *	- check whether the folders have the correct permissions 
 *	   (in this case a CHMOD value of 777)
 *	- check the template folder and list it
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
class Folder_Model extends CI_Model {
	
	// Function to check the folder permissions. The function will check if a specified folder has the specified CHMOD values, if so it returns true. Else it will return false
	// $folder: The folder that needs to be checked.
	// $chmod: The CHMOD value that is required for the specified directory
	function check_permissions($folder) {
		
		// First check if the specified folder is actually a directory and check if the CHMOD permission is 4 characters in length (e.g 0777 is correct while 777 isn't)
		if(is_dir($folder)) {
			
			// Get the file permissions
			$permission = substr(decoct(fileperms($folder)), 2);			
			
			// Check if the folder is writable
			$dir_writeable = is_writable($folder);

			// Set up a message based on the permissions
			if($dir_writeable == true) {
				// Message when the directory is writable
				$dir_message = "The output directory is writable, have fun using iScaffold";
				$message_id = "succes-message";
			}
			else {
				$dir_message = "The output directory isn't writable, please change the CHMOD values. The current CHMOD value is $permission";
				$message_id = "error-message";
			}
			
			// Create an array containing the results
			$info = array (
				'is_writeable' 	=> $dir_writeable,
				'dir_message'   => $dir_message,
				'message_id' 	=> $message_id
			);
			
			// Return it
			return $info;			

		} else {

    		$dir_message = "The 'output' directory doesn't exists, please create in the root directory of iScaffold 2.0 it and make it writable.";
    		$message_id = "error-message";
            $dir_writeable = false;

			// Create an array containing the results
			$info = array (
				'is_writeable' 	=> $dir_writeable,
				'dir_message'   => $dir_message,
				'message_id' 	=> 'error-message',
			);
            return $info;
        }
	}

	/**
	 *	Returns the list of the template models and joins the manifest file to them
	 */
	function getCodeTemplates()
	{
		$templates = array();
		$path = 'templates';

        if( is_dir( $path ) )
        {
            $objects = scandir( $path );
  
            if( sizeof( $objects) > 0 )
            {
                foreach( $objects as $file )
                {
                	$manifest_file_path = $path.DS.$file.DS.'manifest.json';

                    if( $file !== "." && $file !== ".." )
                    {
	                    if( is_dir( $path.DS.$file ) && file_exists( $manifest_file_path ) )
	                    {
	                    	$manifest = json_decode( file_get_contents( $manifest_file_path ), TRUE );

	                    	if( $manifest )
	                    	{                   		
		                    	$template_arr = array(
		                    			'directory' => $file,
		                    			'manifest' => $manifest,
		                    		);

		                        $templates[] = $template_arr;
	                    	}
	                    }                    	
                    }
                }
            }
            return $templates;
        }
	}
}
