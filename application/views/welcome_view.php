<?php

/****************************************************************************
 *  welcome_view.php
 *  The default view
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
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>iScaffold <?php echo $app_version; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This is iScaffold!!!">
    <meta name="author" content="Tibor Szász">
    <base href="<?php echo base_url(); ?>">
    <link href="repo/generator/css/bootstrap.css" rel="stylesheet">
    <link href="repo/generator/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="repo/generator/css/iscaffold.css" rel="stylesheet">
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="repo/generator/ico/favicon.ico">
  </head>
<body>
  	<!-- Navbar -->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="./index.html">iScaffold <span><?php echo $app_version; ?>, Codename <?php echo $app_codename; ?></span></a> 
          <div class="nav-collapse">
            <ul class="nav pull-right">
              <li><a href="http://iscaffold.skyweb.hu" target="_blank">Project site</a></li>
              <li><a href="https://github.com/kowdermeister/iScaffold" target="_blank">Github page</a></li>
              <li><a href="https://github.com/kowdermeister/iScaffold/wiki" target="_blank">Github Wiki</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="app container">
    		<div class="row-fluid">

    		  <div class="span6">

    		  	<h1>Welcome</h1>

    		  	<p class="top-message">Welcome to <acronym class="yellow" title="This application">iScaffold</acronym>. This is a <acronym class="red" title="A tool which is meant to result in high-quality, defect-free, and maintainable software products.">CASE</acronym> application built upon <acronym class="green" title="An Open Source PHP framework">CodeIgniter</acronym> that lets you generate your basic <acronym class="blue" title="Files that handle the incoming and outgoing data">Models</acronym>, <acronym class="brown" title="These files show the actual data or output.">Views</acronym> and <acronym class="pink" title="Controllers handle the incoming requests, call models etc">Controllers</acronym> for each table in your database.</p>

    		  	<h1 class="m10">Start here</h1>

      			<div class="btn-group">
      			  <a class="btn dropdown-toggle btn-large btn-primary" data-toggle="dropdown" id="database_picker_button" href="#">
      			    Select database
      			    <span class="caret"></span>
      			  </a>
      			  <ul class="dropdown-menu" id="database_picker">
                  <?php foreach( $databases as $db ): ?>
                    <?php if( $db !== 'information_schema' && $db !== 'mysql' ): ?><li><a rel="<?=$db?>"><?=$db?></a></li><?php endif; ?>
                  <?php endforeach; ?>
      			  </ul>
      			</div>

            <div id="configurator_wrapper">
        			<p>Cool, now click the button below, or if you are happy with your settings, generate the application on the right side.</p>
        			<a class="btn dropdown-toggle btn-large btn-inverse" data-toggle="modal" href="#configurator" id="configure_button" href="#">Open configurator</a>
            </div>

    		  </div>


    		  <!-- Generate code panel -->


    		  <div class="span6" id="code_generator_block">

	            <div id="generate_wrapper"><!-- hidden by default -->

	      		  	<h1>Generate code</h1>

	      		  	<p>To generate your application first pick a code template to work with.</p>

	        			<div class="btn-group">
	        				<a class="btn dropdown-toggle" data-toggle="dropdown" id="code_template_button" href="#">
	        					iScaffold core
	        					<span class="caret"></span>
	        				</a>
	        				<ul class="dropdown-menu" id="code_template_picker">
                    <?php foreach( $code_templates as $ct ): ?>
  	        					<li><a rel="<?=$ct['directory']?>" data-desc="<?=$ct['manifest']['description']?>"><?=$ct['manifest']['name']?></a></li>
                    <?php endforeach; ?>
	        				</ul>
	        			</div>

	        			<div id="template_desc">
	        				<div class="alert alert-info"><?php foreach( $code_templates as $ct ){ if( $ct['directory'] == 'iscaffold_core' ) echo $ct['manifest']['description']; } ?></div>
	        			</div>

	        			<p>The final step comes, click generate to finish your application.</p>

	        			<a class="btn dropdown-toggle btn-large btn-danger" id="generate_button" href="#">Generate application</a>
  
                <div class="spinner">
                  <p>Coding hard...</p>
                  <div class="bar1"></div>
                  <div class="bar2"></div>
                  <div class="bar3"></div>
                  <div class="bar4"></div>
                  <div class="bar5"></div>
                  <div class="bar6"></div>
                  <div class="bar7"></div>
                  <div class="bar8"></div>
                  <div class="bar9"></div>
                  <div class="bar10"></div>
                  <div class="bar11"></div>
                  <div class="bar12"></div>
                </div>

                <div class="alert alert-success hide" id="generate_success">
                  <p>Your application is generated.</p>
                </div>

                <div class="alert alert-error hide" id="generate_fail">
                  <p>There was a problem generating your application. It can be server code error. Check your console.</p>
                </div>
	          </div>
    		</div>
	  </div>


    <!-- Footer -->
    <footer class="footer">
      <p>©2009-<?php echo date('Y'); ?> - Tibor Szász, All rights reserved. This application is powered by CodeIgniter.</p>
      <p>This generator GUI is built with <a href="https://github.com/twitter/bootstrap/">Twitter Bootstrap</a>, licensed under <a href="http://www.apache.org/licenses/LICENSE-2.0/">Apache License v2.0</a>.</p>
    </footer>

  </div><!-- /container -->


	<div class="modal fade" id="configurator">
	  <div class="modal-header">
	    <h3>Configurator</h3>
	  </div>
	  <div class="modal-body">
	    <iframe src="" frameborder="0" width="100%" id="configurator_frame" name="configurator_frame"></iframe>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn" data-dismiss="modal">Close without saving</a>
	    <a href="#" class="btn btn-danger" id="save_config">Save changes</a>
	  </div>
	</div>

  <!-- javascript includes -->
  <script src="repo/generator/js/jquery.js"></script>
  <script src="repo/generator/js/bootstrap-transition.js"></script>
  <script src="repo/generator/js/bootstrap-alert.js"></script>
  <script src="repo/generator/js/bootstrap-modal.js"></script>
  <script src="repo/generator/js/bootstrap-dropdown.js"></script>
  <script src="repo/generator/js/bootstrap-scrollspy.js"></script>
  <script src="repo/generator/js/bootstrap-tab.js"></script>
  <script src="repo/generator/js/bootstrap-tooltip.js"></script>
  <script src="repo/generator/js/bootstrap-popover.js"></script>
  <script src="repo/generator/js/bootstrap-button.js"></script>
  <script src="repo/generator/js/bootstrap-collapse.js"></script>
  <script src="repo/generator/js/bootstrap-carousel.js"></script>
  <script src="repo/generator/js/bootstrap-typeahead.js"></script>
  <script src="repo/generator/js/prefixfree.min.js"></script>
  <!-- and the winner is -->
  <script src="repo/generator/js/iscaffold.js"></script>

  </body>
</html>
