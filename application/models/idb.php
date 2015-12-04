<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/****************************************************************************
 *  idb.php
 *  This class helps connect to any database, not just the one in the config
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
class idb extends CI_Model
{
	function __construct()
	{
		parent::__construct();

		include( APPPATH . '/config/database.php' );
		$this->db_conf = $db['iscaffold'];
	}	

	function connect( $database )
	{
        if( $database )
        {
        	$this->db_conf['database'] = $database;
        } 
        $this->load->database( $this->db_conf );

        if( $this->db->database == '' )
        {
        	return false;
        }
        else 
        {
        	return true;
        }
	}
}
