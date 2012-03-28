<?php

/****************************************************************************
 *  form_factory.php
 *  This model is being used to genererate individual form elements 
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
class Form_factory extends CI_Model {
	
	function __construct()
	{
        parent::__construct();
		
		/**
            This variable will contain the 'form_base.tpl'		
            Variable is loaded from the 'model_iscaffold' model
		**/
		$this->form_base      = '';
		$this->name_table     = ''; // Populated the same way
        $this->field_required = FALSE;
	}

    /**	
        Create form elements 
    **/    
	function fetch_block( $field_name, $field_config, $count )
	{
	    $this->field_name   = $field_name;
	    $this->field_config = $field_config;
        $this->count        = $count;

        // Init relation array
	    if( $this->field_config['sf_related'] !== NULL )
        {
            $this->field_config['sf_related'] = explode( '|', $this->field_config['sf_related'] );
        } 
        $return_str = '';

        // Regular expression patten
        $exp = '|%'.$this->field_config['sf_type'].'%(.*)%/'.$this->field_config['sf_type'].'%|is';

        preg_match( $exp, $this->form_base, $matches );
        $return_str = $this->replace_variables( $matches[1] );

        return $return_str;
    }
    
    function replace_variables( $extractum )
    {
        $tpl = '';
		$tpl = str_replace( '%FIELD_COUNT%',   $this->count, $extractum );
		$tpl = str_replace( '%NAME_TABLE%',    $this->name_table, $tpl );
		$tpl = str_replace( '%FIELD_NAME%',    $this->field_name, $tpl );
		
		// Handle IF_FIELD_DESC block		
        if( $this->field_config['sf_desc'] )
		{
            $tpl = str_replace( '%FIELD_DESC%', $this->field_config['sf_desc'], $tpl );
            $tpl = str_replace( '%IF_FIELD_DESC%', '', $tpl );
            $tpl = str_replace( '%/IF_FIELD_DESC%', '', $tpl );
        }
        else // Wipe out IF block
        {
            $tpl = preg_replace( '|%IF_FIELD_DESC%(.*)%/IF_FIELD_DESC%|is',  '', $tpl );
        }

        // Handle IF_REQUIRED block
        if( $this->field_required )
        {
            $tpl = str_replace( '%IF_REQUIRED%', '', $tpl );
            $tpl = str_replace( '%/IF_REQUIRED%', '', $tpl );
        }
        else
        {
            $tpl = preg_replace( '|%IF_REQUIRED%(.*)%/IF_REQUIRED%|is',  '', $tpl );
        }

		if( isset( $this->field_config['sf_related'][0] ) )
        {
            $tpl = str_replace( '%RELATED_TABLE%', $this->field_config['sf_related'][0], $tpl );
        } 
		return $tpl;
    }
}
