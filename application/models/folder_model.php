<?php
/* @name Folder Model *
 * @version 0.9
 * @package iScaffold
 * 
 * This model is being used to check whether the folders have the correct permissions (in this case a CHMOD value of 777).
 */
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
}
?>