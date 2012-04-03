/****************************************************************************
 *  iscaffold.js
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

var app = {};

app.database = false;
app.code_template = 'iscaffold_core';

$(function() 
{
	/**
	 *	Init configurator dialog
	 */
	$('#configurator').modal({
		backdrop: 'static',
		keyboard: false,
		show: false
	});

	app.resizeIframe();

	$( window ).resize( app.resizeIframe );

	/**
	 *	Add button handler events
	 */
	$('#database_picker a').click( function()
	{
		app.database = this.rel;

		$('#database_picker_button').html( this.innerHTML + ' <span class="caret"></span>' );
		$('#generate_wrapper').animate( { marginLeft: '0' } );
		$('#configurator_wrapper').animate( { height: '100' } );
		$('#template_desc').animate( { width: '99%' } );
		$('#configurator_frame').attr( 'src', 'index.php/configurator/index/' + this.rel );
	});

	$('#code_template_picker a').click( function()
	{
		app.code_template = this.rel;

		$('#template_desc .alert').html( $(this).data('desc') );
		$('#code_template_button').html( this.innerHTML + ' <span class="caret"></span>' );
	});

	$('#save_config').click( function () 
	{
		$(this).addClass('disabled');
		$(this).html( 'Working, please wait...');
		window.frames['configurator_frame'].conf.saveConfig();
	});

	$('#generate_button').click( function () 
	{
		$('#generate_button').addClass('disabled');
		$('.spinner').show();

		$.ajax({
			url: 'index.php/generate/index/' + app.database + '/' + app.code_template,
			dataType: 'json',
			success: function ( response ) 
			{
				$('#generate_button').removeClass('disabled');
				$('.spinner').hide();

				if( response )
				{
					if( response.result == 'success' )
					{
						$("#generate_success").removeClass('hide');
						$("#generate_fail").addClass('hide');
					}
					else
					{
						$("#generate_success").addClass('hide');
						$("#generate_fail").removeClass('hide');
						$("#generate_fail p").html( response.message );
					}
				}
			},
			error: function ( response ) 
			{
				console.log( "Error object", response );
				$('#generate_button').removeClass('disabled');
				$('.spinner').hide();
				$("#generate_success").addClass('hide');
				$("#generate_fail").removeClass('hide');
			}
		});
	});
});

app.resizeIframe = function()
{
	$('#configurator_frame').css( 'height', $(window).innerHeight() - 190 );	
}

app.closeConfigurator = function()
{
	$('#save_config').removeClass('disabled');
	$('#save_config').html( 'Save changes' );
	$('#configurator').modal('hide');
}
