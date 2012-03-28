<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class %NAME_CONTROLLER% extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();

		$this->load->library( 'template' ); 
		$this->load->model( '%NAME_MODEL_LOWER%' ); 
		%LOAD_UPLOAD_MODEL%
		$this->load->helper( 'form' );
		$this->load->helper( 'language' ); 
		$this->load->helper( 'url' );
        $this->load->model( 'model_auth' );

        $this->logged_in = $this->model_auth->check( TRUE );
        $this->template->assign( 'logged_in', $this->logged_in );

		$this->lang->load( 'db_fields', 'english' ); // This is the language file
	}



    /**
     *  LISTS MODEL DATA INTO A TABLE
     */         
    function index( $page = 0 )
    {
        %MODEL_CALL%->pagination( TRUE );
		$data_info = %MODEL_CALL%->lister( $page );
        $fields = %MODEL_CALL%->fields( TRUE );
        %MANY_RELATION_RELATED_FIELDS%

        $this->template->assign( 'pager', %MODEL_CALL%->pager );
		$this->template->assign( '%NAME_TABLE%_fields', $fields );
		$this->template->assign( '%NAME_TABLE%_data', $data_info );
        $this->template->assign( 'table_name', '%NAME_CONTROLLER%' );
        $this->template->assign( 'template', 'list_%NAME_TABLE%' );
        %MANY_RELATION_FIELD_ASSIGNS%
		$this->template->display( 'frame_admin.tpl' );
    }



    /**
     *  SHOWS A RECORD VIEW
     */
    function show( $id )
    {
		$data = %MODEL_CALL%->get( $id );
        $fields = %MODEL_CALL%->fields( TRUE );
        %MANY_RELATION_RELATED_FIELD%

        %MANY_RELATION_DATA_ASSIGNS%
        $this->template->assign( 'id', $id );
		$this->template->assign( '%NAME_TABLE%_fields', $fields );
		$this->template->assign( '%NAME_TABLE%_data', $data );
		$this->template->assign( 'table_name', '%NAME_CONTROLLER%' );
		$this->template->assign( 'template', 'show_%NAME_TABLE%' );
		$this->template->display( 'frame_admin.tpl' );
    }



    /**
     *  SHOWS A FROM, AND HANDLES SAVING IT
     */         
    function create( $id = false )
    {
		$this->load->library('form_validation');
        
		switch ( $_SERVER ['REQUEST_METHOD'] )
        {
            case 'GET':
                $fields = %MODEL_CALL%->fields();
                %RELATED_TABLE_MODELS%
                %RELATED_TABLE_ASSIGNS%
                %MANY_RELATION_EMPTY_ASSIGNS%
                $this->template->assign( 'action_mode', 'create' );
        		$this->template->assign( '%NAME_TABLE%_fields', $fields );
                $this->template->assign( 'metadata', %MODEL_CALL%->metadata() );
        		$this->template->assign( 'table_name', '%NAME_CONTROLLER%' );
        		$this->template->assign( 'template', 'form_%NAME_TABLE%' );
        		$this->template->display( 'frame_admin.tpl' );
            break;

            /**
             *  Insert data TO %NAME_TABLE% table
             */
            case 'POST':
                $fields = %MODEL_CALL%->fields();

                /* we set the rules */
                /* don't forget to edit these */
%SET_RULES%
%VALIDATION_TRUE%
                if ( $this->form_validation->run() == FALSE %FILE_UPLOAD_VALIDATION%)
                {
                    $errors = validation_errors();
                    %FILE_UPLOAD_ERRORS%
                    %RELATED_TABLE_MODELS%
                    %RELATED_TABLE_ASSIGNS%
                    %MANY_RELATION_POST_ASSIGNS%
              		$this->template->assign( 'errors', $errors );
              		$this->template->assign( 'action_mode', 'create' );
            		$this->template->assign( '%NAME_TABLE%_data', $data_post );
            		$this->template->assign( '%NAME_TABLE%_fields', $fields );
                    $this->template->assign( 'metadata', %MODEL_CALL%->metadata() );
            		$this->template->assign( 'table_name', '%NAME_CONTROLLER%' );
            		$this->template->assign( 'template', 'form_%NAME_TABLE%' );
            		$this->template->display( 'frame_admin.tpl' );
                }
                elseif ( $this->form_validation->run() == TRUE )
                {
                    $insert_id = %MODEL_CALL%->insert( $data_post );
                    %MANY_RELATION_INSERT%
					redirect( '%NAME_TABLE%' );
                }
            break;
        }
    }



    /**
     *  DISPLAYS THE POPULATED FORM OF THE RECORD
     *  This method uses the same template as the create method
     */
    function edit( $id = false )
    {
        $this->load->library('form_validation');

        switch ( $_SERVER ['REQUEST_METHOD'] )
        {
            case 'GET':
                %MODEL_CALL%->raw_data = TRUE;
        		$data = %MODEL_CALL%->get( $id );
                $fields = %MODEL_CALL%->fields();
                %RELATED_TABLE_MODELS%
                %MANY_RELATION_MODELS%
                %RELATED_TABLE_ASSIGNS%
                %MANY_RELATION_DATA_ASSIGNS%
          		$this->template->assign( 'action_mode', 'edit' );
        		$this->template->assign( '%NAME_TABLE%_data', $data );
        		$this->template->assign( '%NAME_TABLE%_fields', $fields );
                $this->template->assign( 'metadata', %MODEL_CALL%->metadata() );
        		$this->template->assign( 'table_name', '%NAME_CONTROLLER%' );
        		$this->template->assign( 'template', 'form_%NAME_TABLE%' );
        		$this->template->assign( 'record_id', $id );
        		$this->template->display( 'frame_admin.tpl' );
            break;
    
            case 'POST':
    
                $fields = %MODEL_CALL%->fields();
                /* we set the rules */
                /* don't forget to edit these */
%SET_RULES%
%VALIDATION_TRUE%
                if ( $this->form_validation->run() == FALSE %FILE_UPLOAD_VALIDATION%)
                {
                    $errors = validation_errors();
                    %FILE_UPLOAD_ERRORS%
                    %RELATED_TABLE_MODELS%
                    %RELATED_TABLE_ASSIGNS%
                    %MANY_RELATION_POST_ASSIGNS%
              		$this->template->assign( 'action_mode', 'edit' );
              		$this->template->assign( 'errors', $errors );
            		$this->template->assign( '%NAME_TABLE%_data', $data_post );
            		$this->template->assign( '%NAME_TABLE%_fields', $fields );
                    $this->template->assign( 'metadata', %MODEL_CALL%->metadata() );
            		$this->template->assign( 'table_name', '%NAME_CONTROLLER%' );
            		$this->template->assign( 'template', 'form_%NAME_TABLE%' );
        		    $this->template->assign( 'record_id', $id );
            		$this->template->display( 'frame_admin.tpl' );
                }
                elseif ( $this->form_validation->run() == TRUE )
                {
				    %MODEL_CALL%->update( $id, $data_post );
				    %MANY_RELATION_UPDATE%
					redirect( '%NAME_TABLE%/show/' . $id );   
                }
            break;
        }
    }



    /**
     *  DELETE RECORD(S)
     *  The 'delete' method of the model accepts int and array  
     */
    function delete( $id = FALSE )
    {
        switch ( $_SERVER ['REQUEST_METHOD'] )
        {
            case 'GET':
                %MODEL_CALL%->delete( $id );
                redirect( $_SERVER['HTTP_REFERER'] );
            break;

            case 'POST':
                %MODEL_CALL%->delete( $this->input->post('delete_ids') );
                redirect( $_SERVER['HTTP_REFERER'] );
            break;
        }
    }
}
