<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * REST SERVER CONTROLLER for %NAME_TABLE%
 * ------------------------------------------------
 * This file is geneated by iScaffold
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class %NAME_CONTROLLER% extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model( '%NAME_MODEL_LOWER%' );
        %LOAD_UPLOAD_MODEL%
    }


    /**
     *  List a record of the table
     *  ----------------------------------------------------------------------------
     *  api.example.com/%NAME_TABLE%/item/21 => lists the record with the id 21
     */
	function item_get()
    {
        if( $this->get('id') )
        {
            $%NAME_TABLE%_item = %MODEL_CALL%->get( $this->get('id') );

            $response_code = ( $%NAME_TABLE%_item ) ? 200 : 404;
        	$this->response( $%NAME_TABLE%_item, $response_code );
        }
        else
        {
            $message = array(
                    'request' => 'failed',
                    'error' => 'You must provide an ID! eg. /%NAME_TABLE%/item/21',
                );
            $this->response( $message, 400 );
        }
    }


    /**
     *  List a record of the table
     *  ----------------------------------------------------------------------------
     *  api.example.com/playlists/list/             => lists the first
     *  api.example.com/playlists/list/offset/10    => shifts the resutls by 10
     */
    function list_get()
    {
        %MODEL_CALL%->pagination_enabled = TRUE;
        $offset = ( $this->get('offset') ) ? $this->get('offset') : FALSE;

        $%NAME_TABLE%_collection = %MODEL_CALL%->lister( $offset );
        $this->response( $%NAME_TABLE%_collection, 200 );
    }


    /**
     *  Here the client can create a record in the database, by posting all the required fields
     */
    function %NAME_TABLE%_post()
    {
%SET_RULES%
%VALIDATION_TRUE%
        if ( $this->form_validation->run() == FALSE %FILE_UPLOAD_VALIDATION% )
        {
            $errors = validation_errors();
            %FILE_UPLOAD_ERRORS%
            %RELATED_TABLE_MODELS%
            %RELATED_TABLE_ASSIGNS%
            %MANY_RELATION_POST_ASSIGNS%

            $message = array(
                    'request' => 'failed',
                    'error' => 'A required field was not posted!',
                    'error_messages' => $errors
                );
            $this->response( $message, 400 );
        }
        elseif ( $this->form_validation->run() == TRUE )
        {
            $insert_id = %MODEL_CALL%->insert( $data_post );
            %MANY_RELATION_INSERT%

            $message = array(
                    'request' => 'successful'
                );
            $this->response( $message, 200 );
        }
    }


    function %NAME_TABLE%_delete()
    {
    	if( $this->get('id') )
        {
            %MODEL_CALL%->delete( $this->input->post('delete_ids') );

            $message = array(
                    'request' => 'successful'
                );
            $this->response( $message, 200 );
        }
        else
        {
            $message = array(
                    'request' => 'failed',
                    'error' => 'You must provide an ID to be deleted!'
                );
            $this->response( $message, 400 );
        }
    }
}