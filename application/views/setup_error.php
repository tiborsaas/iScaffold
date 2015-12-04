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
    <title>iScaffold error</title>
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
          <div class="nav-collapse">
            <ul class="nav pull-right">
              <li><a href="http://tiborsaas.github.io/iScaffold" target="_blank">Project site</a></li>
              <li><a href="https://github.com/tiborsaas/iScaffold" target="_blank">Github page</a></li>
              <li><a href="https://github.com/tiborsaas/iScaffold/wiki" target="_blank">Github Wiki</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="app container">
    		<div class="row-fluid">

    		  <div class="span6">

    		  	<h1>Database connection error</h1>

    		  	<p class="top-message">Please edit <acronym class="green">application/config/database.php</acronym> to setup your connection.</p>

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