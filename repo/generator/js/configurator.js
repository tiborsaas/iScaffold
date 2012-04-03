/****************************************************************************
 *  configurator.js
 *  JavaScript front end code
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

var Configurator = new Class({

    initialize: function()
    {
        // Data to post to server
        this.confData = {};
        this.tables = $$('.table');
        
        // Loading ready, show the tables
        $('configurator').setStyle('display', 'block');

        // Boot        
        this.initAccordion();        
        this.initSorting();

        if( $('alert_close') )
        {
            $('alert_close').addEvent('click',function (e) {
                this.getParent().destroy();
            })
        }
    },

    /**
     *  Initialize configurator's accordion
     */         
    initAccordion: function()
    {
        new Fx.Accordion( $$('#table_selector a'), this.tables, {
            display: 0,
       		onActive: function(toggler){
    			toggler.getParent().addClass('active');
    		},
    		onBackground: function(toggler){
    			toggler.getParent().removeClass('active');
    		}
        });
    },

    /**
     *  Make table fields reorderable
     */         
    initSorting: function()
    {
        var pass = this;
        this.lists = {};
        this.tables.each( function( t )
        {
            var tableName = t.get('rel');
            pass.lists[ tableName ] = new Sortables( t.getElement( 'ul' ), {
                clone: true,
                handle: 'h4',
                revert: true,
                onStart: function( obj, clone ){
                    clone.set('opacity', 0.7)
                }
            });
        });    
    },

    /**
     *  Function to save the configuration table's datas via AJAX
     */
    saveConfig: function()
    {
        var pass = this;
        
        // Make the button load
        $('save_button').addClass( 'loading' );
        $('save_button').innerHTML = '&nbsp;';
        $('save_button').href = 'javascript:void(0)';
        
        this.tables.each( function( t )
        {
            // Get table name
            var tableName = t.get('rel');
    
            // Loop trough fields and collect data
            t.getElements('.field').each( function( f )
            {
                var fieldName = f.previousElementSibling.innerHTML;
                var many_field_id = f.getElement('input[name=field_id]')

                if( !pass.confData[tableName] ) pass.confData[tableName] = {};
                if( !pass.confData[tableName][fieldName] ) pass.confData[tableName][fieldName] = {};
                
                pass.confData[tableName][fieldName]['label']  = f.getElement('input[name=label]').value;
                pass.confData[tableName][fieldName]['type']   = f.getElement('select[name=type]').value;
                pass.confData[tableName][fieldName]['desc']   = f.getElement('input[name=desc]').value;
                pass.confData[tableName][fieldName]['hidden'] = f.getElement('input[name=hidden]').checked;

                // Relations
                pass.confData[tableName][fieldName]['field_id'] = ( many_field_id ) ? many_field_id.value : false;

                if( pass.confData[tableName][fieldName]['type'] == 'related' || pass.confData[tableName][fieldName]['type'] == 'many_related' )
                {
                    pass.confData[tableName][fieldName]['related_id']   = f.getElement('select[name=related_id]').value;
                    pass.confData[tableName][fieldName]['related_name'] = f.getElement('select[name=related_name]').value;
                }
                else
                {
                    pass.confData[tableName][fieldName]['related_id']   = false;
                    pass.confData[tableName][fieldName]['related_name'] = false;
                }
            });
        });


        // db_name,base_url variables are generated in the 'views/configurator.php' template's header
        new Request.JSON({ url: "index.php/configurator/save/" + db_name, onSuccess: function( rsp, txt ){

            if( rsp !== null )
            {
                if( rsp.success == 'yes' )
                {
                    $('save_button').removeClass( 'loading' );
                    
                    // Call parent window's 
                    parent.app.closeConfigurator();
                }
            }
            else
            {
                $('save_button').removeClass( 'loading' );
                $('save_button').innerHTML = 'Try again';
                $('save_button').href = 'javascript:conf.saveConfig()';
                $('debug_rsp').height = 250;
                $('debug_rsp').contentWindow.document.write( txt );
            }
        }}).post( { 'json_data': JSON.encode( this.confData ) } );        
    },


    /**
     *  Called by 'field type select'
     *  Toggles sf_related      
     */             
    fieldState: function( obj )
    {
        var pass = this;
        if( obj.value == 'related' || obj.value == 'many_related' )
        {
            obj.parentNode.getElement('.related').setStyle( 'display', 'block' );
            var dest = obj.parentNode.getElement('.select_holder');

            if( dest.getElements( 'select' ).length == 0 )
            {
                // First select
                var related_id = $('schema_list').clone();
                related_id.addEvent( 'change', function(){ pass.disableInvalids( this ); } );
                related_id.name = 'related_id';
                related_id.inject( dest );

                // Second select
                var related_name = $('schema_list').clone();
                related_name.name = 'related_name';                 
                related_name.inject( dest );
            }
        }
        else
        {
            obj.parentNode.getElement('.related').setStyle( 'display', 'none' );
        }    
    },


    /**
     *  Creates an extra field for the fields list
     */         
    addRelation: function( table )
    {
        var pass = this;

        // Count many relations
        var mc = $( table ).getElements('input[name=field_id]').length + 1;
        
        // Field list item clone
        var li_clone = $('field_template').clone();
        var dest = li_clone.getElement('.select_holder');
            li_clone.getElement('h4').innerHTML = li_clone.getElement('h4').innerHTML + mc;
            li_clone.getElement('a').href = "javascript:void(0)";
            li_clone.getElement('a').href = "javascript:void(0)";
            li_clone.getElement('.many_related_desc strong').innerHTML = table + "_<span></span>";
            li_clone.getElement('.related').setStyle( 'display', 'block' );

            if( dest.getElements( 'select' ).length == 0 )
            {
                // First select
                var related_id = $('schema_list').clone();
                related_id.addEvent( 'change', function(){ pass.disableInvalids( this ); } );
                related_id.name = 'related_id';
                related_id.inject( dest );

                // Second select
                var related_name = $('schema_list').clone();
                related_name.name = 'related_name';                 
                related_name.inject( dest );
            }

            li_clone.getElement('a').addEvent( 'click', function()
            { 
                if( confirm( 'Do you really want to delete this relation?' ) )
                {
                    pass.removeRelation( li_clone )
                }
            });

        // This is the UL element
        li_clone.inject( $( table ) );
        
        console.log( this.lists[ table ] );
        this.lists[ table ].addItems( li_clone );
    },


    /**
     *  Creates an extra field for the fields list
     */         
    removeRelation: function( item )
    {
        item.destroy();
    },


    /**
     *  Disables fields for related table selects, that make no sense to select. 
     */         
    disableInvalids: function( obj )
    {
        // Get selected table
        var table = obj.value.split('|');
        var name_select = obj.nextElementSibling; 
        var found = false;
        table = table[0]; 

        // user friendy feature to tell switch table name
        var trg = obj.parentNode.parentNode.getElement( '.many_related_desc span' );
        if( trg )
        {
            trg.innerHTML = table;
        }
                
        name_select.getElements( 'option' ).each( function( op )
        {
            op.disabled = false;
            if( op.value.split('|')[0] !== table )
            {
                op.disabled = 'disabled';
            }
            // Make the first match selected
            if( found == false && op.value.split('|')[0] == table )
            {
                name_select.value = op.value;
                found = true;
            }
        });
    },
    
    check: function( url, message )
    {
        if( confirm( message ) )
        {
            document.location = url;
        }        
    }
});

var conf = {};
window.addEvent( 'load', function()
{
    conf = new Configurator;
});
