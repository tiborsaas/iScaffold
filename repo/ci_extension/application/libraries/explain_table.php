<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
