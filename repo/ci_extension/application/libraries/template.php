<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require "smarty/Smarty.class.php";

class Template extends Smarty
{
    function __construct()
    {
        parent::__construct();

        $this->compile_dir = APPPATH . "compiled/";
        $this->template_dir = APPPATH . "views/";

        $this->assign( 'APPPATH', APPPATH );
        $this->assign( 'BASEPATH', BASEPATH );

        // Say hello to CI super class
        $CI =& get_instance();
        
        // Default assigns
        $this->assign( 'config', $CI->config->config );
        $this->assign( 'domain', $_SERVER['HTTP_HOST'] );
    }
}
