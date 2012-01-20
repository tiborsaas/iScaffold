<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class %NAME_MODEL% extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
 
		$this->load->database();

		// Paginaiton defaults
		$this->pagination_enabled = FALSE;
		$this->pagination_per_page = 10;
		$this->pagination_num_links = 5;
		$this->pager = '';

        /**
		 *    bool $this->raw_data		
		 *    Used to decide what data should the SQL queries retrieve if tables are joined
		 *     - TRUE:  just the field names of the %NAME_TABLE% table
		 *     - FALSE: related fields are replaced with the forign tables values
		 *    Triggered to TRUE in the controller/edit method		 
		 */
        $this->raw_data = FALSE;  
    }

	function get ( $id, $get_one = false )
	{
        %MODELL_CALL_METADATA%
	    $select_statement = ( $this->raw_data ) ? '%RAW_FIELDS_STRING%' : '%FIELDS_STRING%';
		$this->db->select( $select_statement );
		$this->db->from('%NAME_TABLE%');
        %MODEL_JOINS%

		// Pick one record
		// Field order sample may be empty because no record is requested, eg. create/GET event
		if( $get_one )
        {
            $this->db->limit(1,0);
        }
		else // Select the desired record
        {
            $this->db->where( '%FIELDS_ID%', $id );
        }

		$query = $this->db->get();

		if ( $query->num_rows() > 0 )
		{
			$row = $query->row_array();
			return %MODEL_ROW_ARRAY%
		}
        else
        {
            return array();
        }
	}



	function insert ( $data )
	{
		$this->db->insert( '%NAME_TABLE%', $data );
		return $this->db->insert_id();
	}
	


	function update ( $id, $data )
	{
		$this->db->where( '%FIELDS_ID%', $id );
		$this->db->update( '%NAME_TABLE%', $data );
	}


	
	function delete ( $id )
	{
        if( is_array( $id ) )
        {
            $this->db->where_in( '%FIELDS_ID%', $id );            
        }
        else
        {
            $this->db->where( '%FIELDS_ID%', $id );
        }
        $this->db->delete( '%NAME_TABLE%' );
        %MODEL_DELETE_RELATIONS%
	}



	function lister ( $page = FALSE )
	{
        %MODELL_CALL_METADATA%
	    $this->db->start_cache();
		$this->db->select( '%FIELDS_STRING%');
		$this->db->from( '%NAME_TABLE%' );
		//$this->db->order_by( '', 'ASC' );
        %MODEL_JOINS%

        /**
         *   PAGINATION
         */
        if( $this->pagination_enabled == TRUE )
        {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('%NAME_TABLE%');
            $config['base_url']    = '%NAME_TABLE%/index/';
            $config['uri_segment'] = 3;
            $config['cur_tag_open'] = '<span class="current">';
            $config['cur_tag_close'] = '</span>';
            $config['per_page']    = $this->pagination_per_page;
            $config['num_links']   = $this->pagination_num_links;

            $this->load->library('pagination');
            $this->pagination->initialize($config);
            $this->pager = $this->pagination->create_links();
    
            $this->db->limit( $config['per_page'], $page );
        }

        // Get the results
		$query = $this->db->get();
		
		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result[] = %MODEL_ROW_ARRAY%
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}



	function search ( $keyword, $page = FALSE )
	{
	    $meta = $this->metadata();
	    $this->db->start_cache();
		$this->db->select( '%FIELDS_STRING%');
		$this->db->from( '%NAME_TABLE%' );
        %MODEL_JOINS%

		// Delete this line after setting up the search conditions 
        die('Please see models/model_%NAME_TABLE%.php for setting up the search method.');
		
        /**
         *  Rename field_name_to_search to the field you wish to search 
         *  or create advanced search conditions here
		 */
        $this->db->where( 'field_name_to_search LIKE "%'.$keyword.'%"' );

        /**
         *   PAGINATION
         */
        if( $this->pagination_enabled == TRUE )
        {
            $config = array();
            $config['total_rows']  = $this->db->count_all_results('%NAME_TABLE%');
            $config['base_url']    = '/%NAME_TABLE%/search/'.$keyword.'/';
            $config['uri_segment'] = 4;
            $config['per_page']    = $this->pagination_per_page;
            $config['num_links']   = $this->pagination_num_links;
    
            $this->load->library('pagination');
            $this->pagination->initialize($config);
            $this->pager = $this->pagination->create_links();
    
            $this->db->limit( $config['per_page'], $page );
        }

		$query = $this->db->get();

		$temp_result = array();

		foreach ( $query->result_array() as $row )
		{
			$temp_result[] = %MODEL_ROW_ARRAY%
		}
        $this->db->flush_cache(); 
		return $temp_result;
	}

%RELATED_TABLES%

%MANY_FUNCTIONS%

    /**
     *  Some utility methods
     */
    function fields( $withID = FALSE )
    {
        $fs = %FIELDS_ARRAY%

        if( $withID == FALSE )
        {
            unset( $fs[0] );
        }
        return $fs;
    }  
    


    function pagination( $bool )
    {
        $this->pagination_enabled = ( $bool === TRUE ) ? TRUE : FALSE;
    }



    /**
     *  Parses the table data and look for enum values, to match them with language variables
     */             
    function metadata()
    {
        $this->load->library('explain_table');

        $metadata = $this->explain_table->parse( '%NAME_TABLE%' );

        foreach( $metadata as $k => $md )
        {
            if( !empty( $md['enum_values'] ) )
            {
                $metadata[ $k ]['enum_names'] = array_map( 'lang', $md['enum_values'] );                
            } 
        }
        return $metadata; 
    }
}
