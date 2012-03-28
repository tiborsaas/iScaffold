<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/****************************************************************************
 *  explain_table.php
 *  Helper class to parse a database scheme
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

class explain_table {

    public function __construct()
    {
        $this->ci =& get_instance();
	}

	function parse( $table )
	{
	    $returnARR = array();
	    
        $res = $this->ci->db->query( "EXPLAIN `$table`" );
        
        foreach( $res->result_array() as $field )
        {
            $enum = $this->extract_values( $field['Type'] );
                    
            $returnARR[ $field['Field'] ] = array(
                'type'        => ( strpos( $field['Type'], '(' ) !== FALSE ) ? $this->extract_type( $field['Type'] ) : $field['Type'],
                'null'        => strtolower( $field['Null'] ),
                'default'     => $field['Default'],
                'max_length'  => $this->extract_length( $field['Type'] ),
                'enum_values' => ( count( $enum ) == 1 ) ? NULL : $enum,
            );
        }
        return $returnARR;
    }
    
    function extract_type( $field_type )
    {
        $ret = explode( '(', $field_type );
        return $ret[0];
    }

    function extract_length( $field_type )
    {
        preg_match( '/\((.*)\)/e', $field_type, $matches );
        settype( $matches[1], 'int' );
        return ( substr( $field_type, 0, 4 ) == 'enum' ) ? NULL : $matches[1];
    }

    function extract_values( $field_type )
    {
        preg_match( '/\((.*)\)/e', $field_type, $matches );

        if( !empty( $matches ) ) 
        {
            $matches[1] = explode( ',', str_replace(  "'", '', $matches[1] ) );
            return $matches[1];
        }
        else
        {
            return array();
        }
    }	
}
