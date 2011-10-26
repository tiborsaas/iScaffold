
    /**
     *  Selects, deselects all the checkboxes inside a given DOM reference
     *  Used in the admin edit/create view      
     */         
	function chk_selector( way, obj )
	{
        var block = obj.parentNode;
        var chks = block.getElementsByTagName('input');
        
        for ( i=0; i<chks.length; i++ )
        {
            if( chks[i].type == 'checkbox' )
            {
                if( way == 'all' )
                {
                    chks[i].checked = true;
                }
                else if( way == 'none' )
                {
                    chks[i].checked = false;
                }
            }
        }		
	}

    /**
     *  General confirm message used to check wether the user really wants to delete a row
     *  Used in the listings view
     */
    function chk( url )
    {
        if( confirm('Are you sure you want to delete this row?') )
        {
            document.location = url;
        }
    }

    /**
     *  Initialize widgets on page load
     */
    $( document ).ready( function()
    {
        /**
         *  WYSIWYG EDITOR
         */
        $.cleditor.buttons.image.uploadUrl = 'uploads';
        $('.wysiwyg').cleditor(
        {
            width:      "100%",
            controls:   "bold italic underline strikethrough | style | " +
                        "bullets numbering | outdent " +
                        "indent | alignleft center alignright | undo redo removeformat | " +
                        "rule image link unlink | pastetext | source"

            // more options at: 
            // http://premiumsoftware.net/cleditor/docs/GettingStarted.html#optionalParameters
        });

        /**
         *  DATEPICKER
         */
        $('.datepicker').datepicker();

        /**
         *  Add security confirm for the multi delete button
         */
        $('.actions button').click( function( event )
        {
            if( !confirm('Are you sure you want to delete the selected rows?') )
            {
                event.preventDefault();
            }       
        });
    });
