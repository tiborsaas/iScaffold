<?php

//  model_iscaffold
//
//  Created by Herr Kaleun on 2009-05-09.
//  Extended by Tibor Szász in 2009-10-*
//  Copyright (c) 2009 iScaffold. All rights reserved.

class model_iscaffold extends Model 
{
	function __construct()
	{
        parent::Model();

        // Big indents
        define( 'IN', "\t\t\t\t" );
        define( 'IN3', "\t\t\t" );
        define( 'IN4', "\t\t\t\t" );
        define( 'IN5', "\t\t\t\t\t" );
        define( 'IN6', "\t\t\t\t\t\t" );

		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('string');
        $this->load->helper('form');

		$this->load->model('form_factory');
		$this->load->model('conf_model');
		$this->load->library('explain_table');
		
		/**
		 *    Checkbox default value if not checked
		 */
		$this->checkbox_default = 0;

        /**
         *    These arrays helps to decide how to validate the values for each fields
         */                 		
		$this->sql_string_fields = array( 'char', 'varchar', 'tinytext', 'text', 'blob', 'mediumtext', 'mediumblob', 'longtext', 'longblob', 'enum', 'set' );
		$this->sql_integer_fields = array( 'int', 'tinyint', 'smallint', 'mediumint', 'bigint' );
		$this->sql_numeric_fields = array( 'float', 'double', 'decimal' );
	}

	function Process_Table ( $name_table, $data_path )
	{

        /**
            Select the current table's confgiuration settings
            Config data is accessible via looping through $fields:
            eg.: $this->table_config[$key]['sf_type']
            $key is the $fields array key
        **/
        $this->db->where( 'sf_table', $name_table );
        $this->db->order_by('sf_order', 'ASC');
        $res = $this->db->get('sf_config');
        $this->table_config = $res->result_array();

        /**
			Process fields data and create form for the fields
			also gather the ID field and the FIELDS in db->select() format
        **/
        $fields = array();
        foreach( $this->conf_model->get_config() as $f )
        {
            // Extract this table 
            if( $f['sf_table'] == $name_table ) $fields[] = $f['sf_field'];
        }

		// select table metadata
		$table_meta = $this->explain_table->parse( $name_table );

        //var_dump( $this->explain_table->parse( 'galleries' ) );
        //die();

		$fields_id		   = $fields[0];
		$fields_string 	   = '';
        $raw_fields_string = '';
        $fields_array      = 'array(';

		$count_field = count( $fields );

		/**
		 *    NOTE: This foreach should be moved to the main foreach
		 */
/*        
         if( $name_table == 'galleries' )
         {
            var_dump( $this->table_config );
         }
*/         		
		foreach ( $fields as $key => $field )
		{
		    $separator = ( $key == 0 ) ? '' : ',';
		    
            /**
             *  Related tables
             */
            if( $this->table_config[$key]['sf_type'] == 'related' )
            {
                $rel_data = explode( '|', $this->table_config[$key]['sf_related'] );

                // Detect self joined table
                // In this case the user selected the same table to be joined
                if( $rel_data[0] == $name_table )
                {
                    $fields_string .= $separator . "( SELECT CI_child.$rel_data[3] FROM $name_table AS CI_child WHERE CI_child.$rel_data[1] = $name_table.$field ) AS $field";                
                } 
                else
                {   
                    $fields_string .= $separator . "$rel_data[0].$rel_data[3] AS $field";
                }
            }
            elseif( $this->table_config[$key]['sf_type'] == 'many_related' )
            {
		        //$fields_array  .= ( $this->table_config[$key]['sf_type'] == 'many_related' ) ? '' : $separator . "\n\t'" . $field . "' => lang('" . $field . "')";
            }
            else
            {
			    $fields_string .= $separator . $field;
            }
            // SKIP 'Virtual fields'
            $raw_fields_string .= ( $this->table_config[$key]['sf_type'] == 'many_related' ) ? '' : $separator . $field;
		    $fields_array      .= $separator . "\n\t'" . $field . "' => lang('" . $field . "')";
		}

		$fields_array      = $fields_array . "\n);"; 

		/**
			Generate initialize values, validation rules & etc.
			- Looks complicated at first glance, but it really isnt.
			- those fields are filled and then processed prior writing the code.
		**/
		$name_controller 	= ucfirst( strtolower( $name_table ) );

		$name_view_show	    = 'show_' . $name_table . '.tpl'; 
		$name_view_list	    = 'list_' . $name_table . '.tpl';
		$name_view_form     = 'form_' . $name_table . '.tpl'; 

		$name_model 		= 'Model_' . strtolower( $name_table );
		$name_model_lower	= strtolower( $name_model );
		$model_call			= "\$this->". $name_model_lower;       

		$values_init_edit 	=  "\$data['values'] = ". $model_call ."->Info_$name_table( \$id );\n";
		$values_init 		=  "";                                                                 
		
		$set_rules		    = '';
		$set_value		    = '';

		$validation_true 	= '';
		$validation_false 	= '';

        $ctrl_related_calls    = ''; // %RELATED_TABLE_MODELS%
        $ctrl_related_assigns  = ''; // %RELATED_TABLE_ASSIGNS%
		
		$model_joins        = '';
        $model_related      = '';
        
        $model_call_metadata = '';
        
        $many_functions     = '';
        $many_relation_insert = '';
        $many_relation_update = '';
        
        $many_related_data_assigns  = ''; // %RELATED_TABLE_ASSIGNS%
        $many_related_empty_assigns  = ''; // %RELATED_TABLE_ASSIGNS%
        $many_related_post_assigns  = ''; // %RELATED_TABLE_ASSIGNS%

        $many_related_calls   = '';
        $many_related_assigns = '';
        		
		$model_row_array    = "array( \n";
		$model_row_coll     = array();

        $field_register     = array();

		foreach ( $fields as $key => $field )
		{
			if ( $field == $fields_id )
			{
                $model_row_coll[]    =  "\t'$field' => \$row['$field'],\n";
			}
			else
			{
                /**
                 *  MODEL Variable formatting
                 *  ---                                 
                 *  Preformat variables /date,enum/ for getting and listing tables
                 *  You can add different "elseif"-s for each field types
                 *  $meta['g_type']['enum_names'][ array_search( $row['g_type'], $meta['g_type']['enum_values'] ) ]                                  
                 */
                if( $this->table_config[$key]['sf_type'] == 'date' )
                {
                    $model_row_coll[] = "\t'$field' => date( 'Y-m-d', \$row['$field'] ),\n";
                }
                elseif( $this->table_config[$key]['sf_type'] == 'enum_values' )
                {
                    // Load metadata if needed
                    $model_call_metadata = '$meta = $this->metadata();';

                    $model_row_coll[] = "\t'$field' => ( array_search( \$row['$field'], \$meta['$field']['enum_values'] ) !== FALSE ) ? \$meta['$field']['enum_names'][ array_search( \$row['$field'], \$meta['$field']['enum_values'] ) ] : '',\n";                
                }
                else
                {
                    if( $this->table_config[$key]['sf_type'] !== 'many_related' ) $model_row_coll[] = "\t'$field' => \$row['$field'],\n";
                }

                $values_init 		 .=	"\$data['values']['$field'] = '';\n";

                /**
                 *  Dynamic validation
                 */
                if( isset( $table_meta[ $field ] ) )
                {
                    if( $table_meta[ $field ]['null'] == 'no' )
                    {
                        $rules = array('required');
                        
                        $max_length = ( $this->table_config[$key]['sf_type'] == 'date' ) ? 16 : $table_meta[ $field ]['max_length'];
                        if( $table_meta[ $field ]['max_length'] ) $rules[] = 'max_length['.$max_length.']';
    
                        // Skip 'date' types, because the unix timestamp is displayed in a human readable fromat and it wouldn't pass the validation
                        if( in_array( $table_meta[ $field ]['type'], $this->sql_integer_fields ) && $this->table_config[$key]['sf_type'] !== 'date' )
                        {
                            $rules[] = 'integer';
                        }

                        if( in_array( $table_meta[ $field ]['type'], $this->sql_numeric_fields ) )
                        {
                            $rules[] = 'numeric';
                        }

                        // Don't validate for 'virtual' fiels and 'files'
                        if( $this->table_config[$key]['sf_type'] !== 'many_related' && $this->table_config[$key]['sf_type'] !== 'file' )
                        {
                            $set_rules .= IN . "\$this->form_validation->set_rules( '$field', lang('$field'), '" . implode( '|', $rules ) . "' );\n";
                        }

                        // Special validation for files fields
                        if( $this->table_config[$key]['sf_type'] == 'file' )
                        {
                            $set_rules .= IN . "if( !\$_FILES['$field']['name'] && !\$this->input->post( '$field-original-name' ) ) \$this->uploader->required_empty .= '<p>'.lang('$field').' is required!</p>';\n";
                        }
                    }
                    else
                    {
                        // Field is not required, but data length must not exceed the permitted value
                        if( $table_meta[ $field ]['max_length'] && $this->table_config[$key]['sf_type'] !== 'many_related' )
                        {
                            $max_length = ( $this->table_config[$key]['sf_type'] == 'date' ) ? 16 : $table_meta[ $field ]['max_length'];
    				        $set_rules	.= IN . "\$this->form_validation->set_rules( '$field', lang('$field'), '$max_length' );\n";
                        }
                    }
                }

				$set_value	 		.= 	"\$data['values']['$field'] = set_value( '$field' );\n";

				$validation_false	.= 	"\$data['values']['$field'] = set_value( '$field' );\n";

                /**
                 *  Data validation exceptions
                 *  This block is created for the controller, $data_post will be passed to the insert / update model method 
                 */                 
                switch( $this->table_config[$key]['sf_type'] )
                {
                    case 'checkbox':
    				    $validation_true .=	IN . "\$data_post['$field'] = ( \$this->input->post( '$field' ) == FALSE ) ? $this->checkbox_default : \$this->input->post( '$field' );\n";
                    break;

                    case 'date':
    				    $validation_true .=	IN . "\$data_post['$field'] = ( \$this->input->post( '$field' ) == '' ) ? time() : strtotime( \$this->input->post( '$field' ) );\n";
                    break;

                    case 'file':
    				    $validation_true .=	IN . "\$data_post['$field'] = ( empty( \$_FILES['$field']['name'] ) ) ? \$this->input->post( '$field-original-name' ) : \$this->uploader->upload( '$field' );\n";
    				break;

                    case 'many_related':
    				    // Skip this, posted values are going into the switch table
    				break;

                    default:
    				    $validation_true .=	IN . "\$data_post['$field'] = \$this->input->post( '$field' );\n";
    				break;
                }

                /**
                 *  CREATE MODEL code
                 *  This block creates the related methods for the model file
                 *  Relation type: one <--> one                 
                 */
                if( $this->table_config[$key]['sf_type'] == 'related' )
                {
                    $rel_data = explode( '|', $this->table_config[$key]['sf_related'] );

                    /**
                        Prevent same function occuring again, table config may contain multiple related field from the same table
                    **/                                                                                 
                    if( !in_array( $rel_data[0], $field_register ) )
                    {
                        /**
                         *  Detect self joins and skip joining table. 
                         *  The related filed is queried by a subquery at line 109
                         */
                        if( $rel_data[0] !== $name_table )
                        {
                            $model_joins    .= "\$this->db->join( '$rel_data[0]', '$field = $rel_data[1]', 'left' );\n";
                        }

                        /**
                            Create the %RELATED_TABLES% replacements
                            This should be moved to a model, maybe: form_factory
                        **/
                        $model_related  .= "\tfunction related_$rel_data[0]()
    {
        \$this->db->select( '$rel_data[1] AS $rel_data[0]_id, $rel_data[3] AS $rel_data[0]_name' );
        \$rel_data = \$this->db->get( '$rel_data[0]' );
        return \$rel_data->result_array();
    }\n\n\n\n";
                        /**
                            %RELATED_TABLE_MODELS% & %RELATED_TABLE_ASSIGNS% replacements
                            They should be located in the controllers
                        **/
                        $ctrl_related_calls   .= "\$$rel_data[0]_set = $model_call".'->'."related_$rel_data[0]();\n";
                        $ctrl_related_assigns .= "\$this->template->assign( 'related_$rel_data[0]', \$$rel_data[0]_set );\n";
                    }
                    $field_register[] = $rel_data[0];
                }
                
                /**
                 *  CREATE MODEL and CONTROLLER code for ONE:MANY relations
                 *  This block creates the related methods for the model file
                 *  Relation type: one <--> many
                 *  PLEASE NOTE: iScaffold assumes, that a "table1_table2" switch table exists with the ID names of the two table                 
                 */
                if( $this->table_config[$key]['sf_type'] == 'many_related' )
                {
                    $rel_data = explode( '|', $this->table_config[$key]['sf_related'] );

                    /**
                     *  Code for MODEL files
                     */                                                             
                    $many_functions = "\tfunction insert_relations( \$target_table, \$items, \$insert_id )
    {
        foreach( \$items as \$i )
        {
            // Code assumes that the switch table is named: $name_table\_[ table2 name ]
            \$tables = explode( '_', \$target_table );
            \$data = array(
                    \$tables[0].'_id' => \$insert_id,
                    \$tables[1].'_id' => \$i,
            );
       		\$this->db->insert( \$target_table, \$data );
        }
    }


    function update_relations( \$target_table, \$items, \$reference_id )
    {
        \$tables = explode( '_', \$target_table );
        // STEP #1: Delete
        \$this->db->where( \$tables[0].'_id', \$reference_id );
        \$this->db->delete( \$target_table );
        
        // STEP #2: Insert
        \$this->insert_relations( \$target_table, \$items, \$reference_id );
    }


    function get_relations( \$switch_table, \$reference_id )
    {
        \$tables = explode( '_', \$switch_table );
        \$this->db->select( \$tables[1].'_id' );
        \$this->db->where( \$tables[0].'_id', \$reference_id );
        \$rel_data = \$this->db->get( \$switch_table );
        \$return_arr = array();
        foreach( \$rel_data->result_array() as \$r )
        {
            \$return_arr[] = \$r[ \$tables[1].'_id' ];
        }
        return \$return_arr;  
    }\n\n\n\n";
                    /**
                     *  The same code for the one:one code generation is needed here too
                     */                                         
                    $ctrl_related_calls   .= "\$$rel_data[0]_set = $model_call".'->'."related_$rel_data[0]();\n";
                    $ctrl_related_assigns .= "\$this->template->assign( 'related_$rel_data[0]', \$$rel_data[0]_set );\n";

                    /**
                        Prevent same function occuring again, table config may contain multiple related field from the same table
                    **/
                    if( !in_array( $rel_data[0], $field_register ) )
                    {

                        $model_related  .= "\tfunction related_$rel_data[0]()
    {
        \$this->db->select( '$rel_data[1] AS $rel_data[0]_id, $rel_data[3] AS $rel_data[0]_name' );
        \$rel_data = \$this->db->get( '$rel_data[0]' );
        return \$rel_data->result_array();
    }\n\n\n\n";
                    }
                    /**
                     *  Code for CONTROLLER files
                     *  Many:many specific variables                     
                     */
                    $many_relation_insert .= $model_call . "->insert_relations( '$name_table".'_'."$rel_data[0]', \$this->input->post( '$field' ), \$insert_id );\n";                                 
                    $many_relation_update .= $model_call . "->update_relations( '$name_table".'_'."$rel_data[0]', \$this->input->post( '$field' ), \$id );\n";
                    $many_related_calls   .= "$" . $name_table . "_" . $rel_data[0] . "_data = $model_call"."->get_relations( '$name_table".'_'."$rel_data[0]', \$id );\n";
                    $many_related_data_assigns .= "\$this->template->assign( '$name_table".'_'."$rel_data[0]_data', \$$name_table".'_'."$rel_data[0]_data );\n"; 
                    $many_related_empty_assigns .= "\$this->template->assign( '$name_table".'_'."$rel_data[0]_data', array() );\n"; 
                    $many_related_post_assigns .= "\$this->template->assign( '$name_table".'_'."$rel_data[0]_data', \$this->input->post( '$field' ) );\n"; 
                }

			} /* End id skipping if branch */ 
			
		} /* End $fields foreach */
		
		
        $model_row_array .= implode( "", $model_row_coll ); 
        $model_row_array .= " );"; 

		/** 
			Begin Code generation into template files
            
            Generate CONTROLLER templates 
		**/
        $has_file_field = $this->has_field( $name_table, 'file' );
         
        $file_upload_validation = ( !$has_file_field ) ? '' : '|| $this->uploader->success == FALSE || $this->uploader->required_empty !== FALSE';
        $load_upload_model      = ( !$has_file_field ) ? '' : "\$this->load->model( 'uploader' );";
        $file_upload_errors     = ( !$has_file_field ) ? '' : "\$errors .= ( \$this->uploader->success ) ? '' : \$this->uploader->response;\n";
        $file_upload_errors    .= ( !$has_file_field ) ? '' : IN5 . "\$errors .= \$this->uploader->required_empty;";

		$code_controller = read_file( $data_path['input_controller'] . 'controller.php' );                                                               
		
		$code_controller = str_replace( "%MODEL_CALL%", 		$model_call, 		$code_controller );

		$code_controller = str_replace( "%VALUES_INIT%", 		$values_init, 	    $code_controller );
		$code_controller = str_replace( "%VALUES_INIT_EDIT%", 	$values_init_edit,  $code_controller );

		$code_controller = str_replace( "%NAME_TABLE%", 		$name_table, 		$code_controller );  
		$code_controller = str_replace( "%NAME_CONTROLLER%", 	$name_controller,   $code_controller );
		$code_controller = str_replace( "%NAME_MODEL_LOWER%", 	$name_model_lower,  $code_controller );
		
		$code_controller = str_replace( "%SET_RULES%", 			$set_rules, 	    $code_controller );
		$code_controller = str_replace( "%SET_VALUE%", 			$set_value, 		$code_controller );
		$code_controller = str_replace( "%VALIDATION_FALSE%", 	$validation_false,  $code_controller );
		$code_controller = str_replace( "%VALIDATION_TRUE%", 	$validation_true,   $code_controller );
		
		$code_controller = str_replace( "%RELATED_TABLE_MODELS%",  $ctrl_related_calls,   $code_controller );
		$code_controller = str_replace( "%RELATED_TABLE_ASSIGNS%", $ctrl_related_assigns, $code_controller );
		
		$code_controller = str_replace( "%MANY_RELATION_INSERT%", $many_relation_insert, $code_controller );
		$code_controller = str_replace( "%MANY_RELATION_UPDATE%", $many_relation_update, $code_controller );

		$code_controller = str_replace( "%MANY_RELATION_MODELS%",        $many_related_calls,         $code_controller );
		$code_controller = str_replace( "%MANY_RELATION_DATA_ASSIGNS%",  $many_related_data_assigns,  $code_controller );
		$code_controller = str_replace( "%MANY_RELATION_EMPTY_ASSIGNS%", $many_related_empty_assigns, $code_controller );
		$code_controller = str_replace( "%MANY_RELATION_POST_ASSIGNS%",  $many_related_post_assigns,  $code_controller );

		$code_controller = str_replace( "%FILE_UPLOAD_VALIDATION%", $file_upload_validation, $code_controller );
		$code_controller = str_replace( "%LOAD_UPLOAD_MODEL%",      $load_upload_model,      $code_controller );
		$code_controller = str_replace( "%FILE_UPLOAD_ERRORS%",     $file_upload_errors,     $code_controller );

		$file_controller = $data_path['output_controller'] . $name_table . '.php';
		write_file( $file_controller, $code_controller );


		


        /**
            Generate MODEL templates
        **/                 		
		$code_model = read_file( $data_path['input_model'] . 'model.php' );

		$code_model = str_replace( "%NAME_MODEL%", 		     $name_model, 		   $code_model );
		$code_model = str_replace( "%FIELDS_STRING%", 	     $fields_string, 	   $code_model );  
		$code_model = str_replace( "%FIELDS_ARRAY%", 	     $fields_array, 	   $code_model );  
		$code_model = str_replace( "%RAW_FIELDS_STRING%",    $raw_fields_string,   $code_model );
		$code_model = str_replace( "%FIELDS_ID%", 		     $fields_id, 		   $code_model );
		$code_model = str_replace( "%NAME_TABLE%", 		     $name_table, 		   $code_model );
		$code_model = str_replace( "%MODELL_CALL_METADATA%", $model_call_metadata, $code_model );
		$code_model = str_replace( "%MODEL_ROW_ARRAY%",      $model_row_array,     $code_model );
		$code_model = str_replace( "%NAME_CONTROLLER%",      $name_controller,     $code_model );
		$code_model = str_replace( "%MODEL_JOINS%",          $model_joins,         $code_model );
		$code_model = str_replace( "%RELATED_TABLES%",       $model_related,       $code_model );
		$code_model = str_replace( "%MANY_FUNCTIONS%",       $many_functions,      $code_model );

		$file_model = $data_path['output_model'] . 'model_' . $name_table . '.php';
		write_file( $file_model, $code_model );

        
        /**
            Generate Uploader MODELS
            Right now it is a static file, nothing to configure
        **/
        
        $uploader_model_path = $data_path['output_model'] . 'uploader.php';
                          		
        if( !file_exists( $uploader_model_path ) )
        {
            $uploader_model = read_file( $data_path['input_model'] . 'uploader.php' );
            file_put_contents( $uploader_model_path, $uploader_model ); // Create file
        }

    


        /**
            Generate Frame_admin.tpl VIEW templates
            Right now it is a static file, nothing to configure
        **/

        $frame_view_path = $data_path['output_view'] . 'frame_admin.tpl';

        if( !file_exists( $frame_view_path ) )
        {
            $list_of_tables = '<ul id="top_menu">';

            foreach( $this->db->list_tables() as $t )
            {
                if( $t !== 'sf_config' ) $list_of_tables .= IN6 . "<li{if isset(\$table_name)}{if \$table_name == '".ucfirst($t)."'} class='active'{/if}{/if}><a href='$t'>".ucfirst($t)."</a></li>\n";
            }
            $list_of_tables .= IN5 . '</ul>';

            $frame_view_content = read_file( $data_path['input_view'] . 'frame_admin.tpl' );

            // Replacements
		    $frame_view_content = str_replace( "%LIST_OF_TABLES%", $list_of_tables, $frame_view_content );
		    $frame_view_content = str_replace( "%DATABASE_NAME%", ucfirst( $this->db->database ), $frame_view_content );

            file_put_contents( $frame_view_path, $frame_view_content ); // Create file
        }




        /**
            Generate 'show' VIEW templates
        **/
		$code_view_show = read_file( $data_path['input_view'] . 'show.tpl' );
	
		$code_view_show = str_replace( "%NAME_VIEW_FORM%", $name_view_show, $code_view_show );
        $code_view_show = str_replace( "%NAME_TABLE%",     $name_table,     $code_view_show );
    
        // FIELD LOOP REPLACEMENT BLOCK
        
        $exp = '|%FIELD_LOOP%(.*)%/FIELD_LOOP%|is';
        $to_replace = '';
        
        preg_match( $exp, $code_view_show, $matches );

		foreach ( $fields as $key => $field )
		{
		    $tmp = str_replace( '%FIELD_COUNT%', $key, $matches[1] ); 
    		$to_replace .= str_replace( '%FIELD_ID%', $field, $tmp );
		}
    
    	$code_view_show = preg_replace( $exp, $to_replace, $code_view_show );

        // WRITE TO FILE
		$file_view_show = $data_path['output_view'] . $name_view_show;
		write_file( $file_view_show, $code_view_show );
		
		


        
        /**
            Generate 'lister' VIEW templates
        **/                 		
		$code_view_list = read_file( $data_path['input_view'] . 'list.tpl' );

        /**
         *  Generate table header and contents HTML template
         */
        $table_header = '';
        $table_contents = '';

        foreach ( $fields as $key => $field )
        {
            $hidden_field = $this->table_config[$key]['sf_hidden'];

            // If the user clicked 'hidden' in the config, then iScaffold doesn't list it
            if( $hidden_field == 0 )
            {
                $table_header .= IN3 . '<th>{$' . $name_table . "_fields.". $field ."}</th>\n";
                $table_contents .= IN . '<td>{$row.'. $field ."}</td>\n";
            }
        }

        $code_view_list = str_replace( "%TABLE_HEADER%",   $table_header,   $code_view_list );
		$code_view_list = str_replace( "%TABLE_CONTENTS%", $table_contents,   $code_view_list );
        $code_view_list = str_replace( "%NAME_VIEW_FORM%", $name_view_list, $code_view_list );
        $code_view_list = str_replace( "%NAME_TABLE%",     $name_table,     $code_view_list );
        $code_view_list = str_replace( "%FIELD_ID%",       $fields_id,      $code_view_list );

		$file_view_list = $data_path['output_view'] . $name_view_list;
		write_file( $file_view_list, $code_view_list );




        /**
			Generate 'form' VIEW templates
        **/
        $code_form = '';
		$code_view_form                 = read_file( $data_path['input_view'] . 'form.tpl' );
        $this->form_factory->form_base  = read_file( $data_path['input_view'] . 'form_base.tpl' ); 
        $this->form_factory->name_table = $name_table; 

        // Build form elements
        $c = 0;
		foreach ( $fields as $key => $field )
		{
			if ( $key > 0 )
			{
                $this->form_factory->field_required = ( $table_meta[ $field ]['null'] == 'no' ) ? TRUE : FALSE;
				$code_form .= $this->form_factory->fetch_block( $field, $this->table_config[$key], $c );
			}
			$c++;
		}
		$code_view_form = str_replace( "%NAME_VIEW_FORM%", $name_view_form, $code_view_form );
        $code_view_form = str_replace( "%NAME_TABLE%",     $name_table,     $code_view_form );
        $code_view_form = str_replace( "%FORM_FIELDS%",    $code_form,      $code_view_form );

		$file_form = $data_path['output_view'] . $name_view_form;
		write_file( $file_form, $code_view_form );





        /**
			Generate Language file                                                                                           
        **/
        $file_lang = $data_path['output_lang'] . 'db_fields_lang.php';

        if( !file_exists( $file_lang ) )
        {
            /**
             *  Create table names list first
             */
    		$code_lang = "<?php\n\n";
    		$code_lang .= "/*************************\n";
            $code_lang .= "\t Table names\n";
		    $code_lang .= "*************************/\n";
		    
            foreach ( $this->db->list_tables() as $tbl_name )
    		{
                if( $tbl_name !== 'sf_config' ) $code_lang .= "\$lang['$tbl_name'] = '".ucfirst( $tbl_name )."';\n";
    		}
            file_put_contents( $file_lang, $code_lang ); // Create file
        }

		$code_lang = read_file( $file_lang );
		
		$code_lang .= "\n\n/*************************\n";
		$code_lang .= "\t Table: $name_controller\n";
		$code_lang .= "*************************/\n";

		foreach ( $fields as $key => $field ) 
		{
    		$code_lang .= "\$lang['$field'] = '" . $this->table_config[$key]['sf_label'] . "';\n";

    		// Add enum values too
    		if( !empty( $table_meta[ $field ]['enum_values'] ) )
    		{
                $code_lang .= "\t// '$field' has some enum values, you can name them\n";
                foreach ( $table_meta[ $field ]['enum_values'] as $tmd )
                {
                    $code_lang .= "\t\$lang['$tmd'] = '$tmd';\n";
                }
            }
		}
        write_file( $file_lang, $code_lang );

	} /* End of Process_Table function */
	
	/**
	 *     This method supposed to decide wether the table has a specific configuration type
	 *     eg: has_field('file') will return true if sf_table has a "file" type field
	 *     returns: bool	 
	 */     	
	function has_field( $name_table, $type )
	{
	    $answer = FALSE;

        foreach( $this->table_config as $tc )
        {
            if( $tc['sf_type'] == $type && $tc['sf_table'] == $name_table )
            {
                $answer = TRUE;
            }
        }
        return $answer;
    }
}
