<?php
                                                                                                                                                
class Configurator extends CI_Controller {

	function __construct()
	{
        parent::__construct();

		$this->load->helper('url');
        $this->load->model('conf_model');
        $this->load->model('idb');
        $this->load->library('explain_table');

        /**
         *  Target database connection
         */
        $this->conn = false;
    }


    /**
     *  Show the main view
     */
    function index( $database )
    {
		$this->idb->connect( $database );

	    // Check if 'sf_config' table exists
        if( !$this->conf_model->check_config_table() )
        {
    		// Array for system information
    		$data = array(
    			'app_name' 		=> $this->config->item('app_name'),
    			'app_codename' 	=> $this->config->item('app_codename'),
    			'app_version' 	=> $this->config->item('app_version'),
    			'app_website' 	=> $this->config->item('app_website'),
    			'config'        => array(),
    			'config_data'   => array(),
    		    'schema'        => array(),
    		    'db_name'       => $database,
    		);
        } 
        else 
        {
    	    // Merge schema and config data
    	    $conf = $this->conf_model->get_config();
    		$schema = $this->conf_model->get_schema();
    		$config = array();
    
    		foreach( $schema as $table=>$fields )
    		{
    		    // Store config datas
    		    $config[ $table ]['data'] = array();
                foreach( $conf as $c )
                {
                    // CONFIG DATA
                    if( $c['sf_table'] == $table )
                    {
                        $c['sf_related'] = ( $c['sf_type'] == 'related' || $c['sf_type'] == 'many_related' ) ? explode( '|', $c['sf_related'] ) : false;
                        $config[ $table ]['data'][] = $c;
                    } 
    
                    // FIELDS
                    if( $c['sf_table'] == $table ) $config[ $table ]['fields'][] = $c['sf_field'];
                }
                
                // Store fields
    		    //$config[ $table ]['fields'] = $fields;
            }

    		// Array for system information
    		$data = array(
    			'app_name' 		=> $this->config->item('app_name'),
    			'app_codename' 	=> $this->config->item('app_codename'),
    			'app_version' 	=> $this->config->item('app_version'),
    			'app_website' 	=> $this->config->item('app_website'),
    			'config'        => $config,
    			'config_data'   => array(),
    		    'schema'        => $this->conf_model->get_schema(),
                'db_name'       => $database,
    		);
    
        } // Endif

		// Load the view
		$this->load->view('configurator',$data);
	}




    /**
     *  Create sf_config table
     */         
    function create_table( $database )
    {
        $this->idb->connect( $database );
        // Create table
        $this->conf_model->create_config_table();

        // Populate it with data        
        $this->conf_model->generate();
    
        // Start again        
        redirect( 'configurator/index/'.$database );              
    }
    
    
    
    
	/**
	 *  This method saves the configuration table's data
	 */     	
	function save( $database )
	{
        $this->idb->connect( $database );

        $res = $this->conf_model->save( json_decode( $this->input->post('json_data') ) );

        if( $res === true )
        {
            echo '{ "success": "yes" }';
        }
        else
        {
            echo '{ "success": "no", "errors": '.$res.' }';
        }
    }




    /**
     *  Do some actions with the configuration table 
     */         
    function modify( $type, $database )
    {
        $this->idb->connect( $database );

        switch( $type )
        {
            // delete all settings you made, but keep the config table's fields.
            case 'reset':
                //$this->conf_model->setup_config_table();
                $this->conf_model->reset();
            break;

            // truncates config table and refills it from your database schema.
            case 'generate':
                $this->conf_model->generate();
            break;

            // scans your schema, if you made changes, like a new table or field,
            // it will be added to the config table.
            case 'update':
                $this->conf_model->update();
            break;
        }
        redirect( 'configurator/index/' . $database );
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */