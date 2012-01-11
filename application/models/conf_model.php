<?php
/* @name Conf_model
 * @version 1.0
 * @package iScaffold
 * 
 * This model is being used to wrap the functions of the configurator
 */
class Conf_Model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }


    /**
     *  Returns an array of the database selected
     */             
    function get_schema()
    {
	    $schema = array();
	    $order_base = array();

        $config = $this->get_config();

        // List tables first and generate schema array
        $tables = $this->db->list_tables();
        foreach ( $tables as $k => $t )
        {
            $fields = $this->db->list_fields( $t );
            $matched_fields = array();

            if( !empty( $config ) )
            {
                // Reorder fields based on config data
                foreach( $config as $ck=>$c )
                {
                    if( $c['sf_table'] == $t )
                    {
                        foreach ( $fields as $f )
                        {
                            if( $f == $c['sf_field'] ) $matched_fields[] = $f;
                        }
                    }
                }
                foreach ( $matched_fields as $f )
                {
                    if( $t !== 'sf_config' ) $schema[ $t ][] = $f;
                }
            }
            else
            {
                foreach ( $fields as $f )
                {
                    if( $f !== 'sf_config' ) $schema[ $t ][] = $f;
                }
            }
        }
        return $schema; 
    }


    /**
     *  Get the user's settings of the database
     */         
    function get_config()
    {
        $this->db->order_by('sf_table', 'ASC');
        $this->db->order_by('sf_order', 'ASC');
        $res = $this->db->get('sf_config');
        return $res->result_array();
    }


    /**
     *  Resets sf_config table
     */         
    function reset()
    {
        $data = array(
            'sf_type'	 => 'default',
            'sf_related' => NULL,	
            'sf_label'   => NULL,	
            'sf_desc'	 => NULL,
            'sf_order'	 => NULL,
            'sf_hidden'  => 0,
        );
        $res = $this->db->update('sf_config', $data);
    }
    
    
    /**
     *  Truncatese and populates sf_config table
     */         
    function generate()
    {
        $this->db->truncate('sf_config');
        
        $schema = $this->get_schema();
        
        // Insert table scheme to config table
        foreach( $schema as $k => $table )
        {
            foreach( $table as $f )
            {
                $this->db->set( 'sf_table', $k );
                $this->db->set( 'sf_field', $f );
                $this->db->insert('sf_config');
            }
        }
    }

    /**
     *  Checks if sf_config table exists    
     *  @returns bool
     */
     
    function check_config_table()
    {
        return ( $this->db->table_exists('sf_config') ) ? TRUE : FALSE;
    }


    /**
     *  Return values:
     *   - 'created' - table didn't existed, it exists now
     *   - 'false'   - table creation error
     *   - 'exists'  - table already exists
     */
    function create_config_table()
    {
        // Load DB manipulation library
        $this->load->dbforge();
        
        if ( !$this->db->table_exists('sf_config') )
        {
            $this->dbforge->add_field("sf_id INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY");
            $fields = array(
                    'sf_table' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '64',
                                'default' => '',
                                'null' => FALSE,
                    ),
                    'sf_field' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '64',
                                'default' => '',
                                'null' => FALSE,
                    ),
                    'sf_type' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '16',
                                'default' => 'default',
                                'null' => TRUE,
                    ),
                    'sf_related' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100',
                                'default' => NULL,
                                'null' => TRUE,
                    ),
                    'sf_label' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '64',
                                'default' => NULL,
                                'null' => TRUE,
                    ),
                    'sf_desc' => array(
                                'type' => 'TINYTEXT',
                                'null' => TRUE,
                    ),
                    'sf_order' => array(
                                'type' => 'INT',
                                'constraint' => '3',
                                'null' => TRUE,
                    ),
                    'sf_hidden' => array(
                                'type' => 'INT',
                                'constraint' => '1',
                                'default' => 0,
                                'null' => TRUE,
                    ),
            );
            $this->dbforge->add_field( $fields );
            $t = $this->dbforge->create_table('sf_config', TRUE);
        }
    }


    /**
     *  Updates sf_config table
     *  Adds / removes fields      
     */
    function update()
    {
        /**
         *  Build acutal schema array of the database
         */
        $schema = array();
        $tables = $this->db->list_tables();

        foreach ( $tables as $k => $t )
        {
            $fields = $this->db->list_fields( $t );
            foreach ( $fields as $f )
            {
                if( $f !== 'sf_config' ) $schema[ $t ][] = $f;
            }
        }


        /**
         *  Create configuration schema  
         */                 
        $config = $this->get_config();
        $config_schema = array();
        
        foreach( $config as $c )
        {
            if( array_key_exists( $c['sf_table'],  $config_schema ) )
            {
                $config_schema[ $c['sf_table'] ][] = $c['sf_field'];
            }
            else
            {
                $config_schema[ $c['sf_table'] ] = array( $c['sf_field'] );
            }
        }

        
        /**
         *  Collectors to keep the "sf_config" table up to date
         *  contents: array( 'table_name', 'field_name' );
         */
        $tables_to_insert = array();
        $tables_to_delete = array();
 
        $fields_to_insert = array();
        $fields_to_delete = array();

      

        /**
         *  First, look for differences on the table level
         *  Test #1: what needs to be inserted?
         */
        $tables_to_insert = array_diff_assoc( $schema, $config_schema );

        /**
         *  Execute Insert action
         */                         
        if( !empty( $tables_to_insert ) )
        {
            foreach( $tables_to_insert as $table => $fields  )
            {
                foreach( $fields as $f )
                {
                    $to_insert = array(
                       	'sf_table'   => $table,	
                        'sf_field'   => $f,
                    );
                    $this->db->insert( 'sf_config', $to_insert );
                }
            }
        }



        /**
         *  First, look for differences on the table level
         *  Test #2: what needs to be deleted?
         */
        $tables_to_delete = array_diff_assoc( $config_schema, $schema );

        /**
         *  Execute Delete action
         */                         
        if( !empty( $tables_to_delete ) )
        {
            foreach( $tables_to_delete as $table => $fields  )
            {
                $this->db->where( 'sf_table', $table );
                $this->db->delete( 'sf_config' );
            }
        }




        /**
         *  Second, look for differences on the fields level
         *  Test #1: what needs to be inserted?
         *           
         *  Note:
         *  Skip new table's fields, because news tables will inserted completly elsewhere
         *  This part looks for differences on the existing tables         
         */
        foreach( $schema as $key => $table )
        {
            if( $key !== 'sf_config' && $key !== 'sf_config_backup' && !array_key_exists( $key, $tables_to_insert ) )
            {
                $field_to_insert = array_diff( $table, $config_schema[ $key ] );
                
                if( !empty( $field_to_insert ) )
                {
                    foreach( $field_to_insert as $field )
                    {
                        $fields_to_insert[] = array( 
                                                'table' => $key, 
                                                'field' => $field 
                                            );
                    }
                }
            }
        }

        /**
         *  Execute Insert action
         */                         
        if( !empty( $fields_to_insert ) )
        {
            foreach( $fields_to_insert as $f )
            {
                $to_insert = array(
                   	'sf_table'   => $f['table'],	
                    'sf_field'   => $f['field'],
                );
                $this->db->insert( 'sf_config', $to_insert );
            }
        }



        /**
         *  Second, look for differences on the fields level
         *  Test #2: what needs to be deleted?
         *  
         *  Note: skip the tables that will be deleted from the sf_config table                            
         */

        foreach( $config_schema as $key => $table )
        {
            if( !array_key_exists( $key, $tables_to_delete ) )
            {
                $field_to_delete = array_diff( $table, $schema[ $key ] );
                
                if( !empty( $field_to_delete ) )
                {
                    foreach( $field_to_delete as $field )
                    {
                        $fields_to_delete[] = array( 
                                                'table' => $key, 
                                                'field' => $field 
                                            );
                    }
                }
            }
        }

        /**
         *  Execute Delete action
         *  ---         
         *  Keep the many_realted table joins, 
         *  you probably don't want these to be deleted         
         */                         
        if( !empty( $fields_to_delete ) )
        {
            foreach( $fields_to_delete as $f  )
            {
                $this->db->where( 'sf_table', $f['table'] );
                $this->db->where( 'sf_field', $f['field'] );
                $this->db->where( 'sf_type != "many_related"' );

                $this->db->delete( 'sf_config' );
            }
        }

    } /* End of update function */



    /**
     *  List databases
     *  @return array
     */
    function list_databases()
    {
        $this->load->dbutil();
        return $this->dbutil->list_databases();
    }


    /**
     *  Save the posted values to the sf_config table
     */         
    function save( $data )
    {
        $success = true;
        foreach( $data as $table => $fields )
        {
            $c = 0;
            foreach( $fields as $field => $config )
            {
                $this->db->where( 'sf_table', $table );
                $this->db->where( 'sf_field', ( $config->field_id ) ? $config->field_id : $field );

                $data = array(
                    'sf_type'    => $config->type,
                    'sf_desc'    => $config->desc,
                    'sf_related' => $config->related_id . '|' . $config->related_name,
                    'sf_label'   => $config->label,
                    'sf_order'   => $c,
                    'sf_hidden'  => ( $config->hidden ) ? 1 : 0,
                );

                if( $config->field_id )
                {
                    $data['sf_table'] = $table;
                    $data['sf_field'] = $config->field_id;
                } 
                
                $method = ( $config->field_id ) ? 'insert' : 'update';
                
                $test = $this->db->$method( 'sf_config', $data );
                
                if( !$test ) $success = false;
                $c++;
            }
        }
        return $success;
    }
}
