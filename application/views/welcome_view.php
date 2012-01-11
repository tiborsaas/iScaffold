<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>repo/generator/styles/reset.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>repo/generator/styles/style.css" type="text/css" />
		<link href='http://fonts.googleapis.com/css?family=Gloria+Hallelujah' rel='stylesheet' type='text/css'>

		<!-- jQuery -->
		<script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
		<script type="text/javascript">

			$( function() {
				$('.message').hide();
				$('.message').fadeIn(1000);
			});

			function database_switch( db )
			{
				if( db == 'no' )
				{
					$('#hidden_steps').animate({height:'0'});
				}
				else
				{
					$('#hidden_steps').animate({height:'200'});
					$('#configure').attr( 'href', '<?=base_url()?>index.php/configurator/index/' + db );
					$('#generate').attr( 'href', '<?=base_url()?>index.php/generate/create/' + db );
				}
			}
		</script>		
		<title><?php echo "$app_name $app_version"; ?></title>
	</head>
	<body>
		<!-- The main container -->
		<div id="container">
			<!-- The header -->
			<div id="header">
				<h1 id="title"><?php echo "$app_name $app_version"; ?></h1>
				<p id="version-codename">Codename: <?php echo $app_codename; ?></p>
				<p class="top-message">Welcome to <acronym class="yellow" title="This application"><?php echo $app_name; ?></acronym>. This is a <acronym class="red" title="A tool which is meant to result in high-quality, defect-free, and maintainable software products.">CASE</acronym> application built upon <acronym class="green" title="An Open Source PHP framework">CodeIgniter</acronym> that lets you generate your basic <acronym class="blue" title="Files that handle the incoming and outgoing data">Models</acronym>, <acronym class="brown" title="These files show the actual data or output.">Views</acronym> and <acronym class="pink" title="Controllers handle the incoming requests, call models etc">Controllers</acronym> for each table in your database.</p>
			</div>
			<!-- The Container -->
			<div id="content">
				<div id="welcome">

					<?php if ( $info_message == '' ): ?>
						<div id="<?php echo $message_id; ?>" class="message">
							<p><?php echo $dir_message; ?></p>	
						</div>
					<?php endif ?>


					<?php if ( $info_message == 'success' ): ?>
						<div id="<?php echo $message_id; ?>" class="message">
							<p><?php echo "<b>Hoooray!</b> The CRUD-Application has been succesfully generated. <br /><br />
                                           Please take the code from the <b>/output/$database</b> directory and copy it to the desired location."; ?></p>
						</div>						
					<?php endif ?>

					<h2<?php if ( $info_message == 'step2' ): ?> class="lined"<?php endif ?>><?php echo ( $info_message == 'success' ) ? 'Start over ':'Step 1'; ?>: Pick a database</h2>

					<p>iScaffold will create the CRUD application for this database:</p>

					<!-- Pick database -->

					<select id="database" onchange="database_switch(this.value)">
						<option value="no">Please select a database</option>
						<?php foreach( $databases as $db ): ?>
							<?php if( $db !== 'information_schema' && $db !== 'mysql' ): ?><option value="<?=$db?>"<?php echo ( $db == $database )?' selected="selected"':''?>><?=$db?></option><?php endif; ?>
						<?php endforeach; ?>
					</select>

					<?php
						if ( $info_message == 'success' )
						{
							echo 'or ';
							echo anchor('generate/create/'.$database, 'Generate again',array('title' => 'Generate again','class' => 'generate-link', 'id' => 'generate' ));
						}				
					 ?>

					<?php if ( $info_message != 'step2' ): ?>
					<div id="hidden_steps">
					<?php endif; ?>

						<!-- Configuration step -->

						<h2<?php if ( $info_message == 'step2' || $info_message == 'success' ): ?> class="lined"<?php endif ?>>Step 2: Configuration</h2>
						
						<p>Before <acronym class="yellow" title="This application">iScaffold</acronym> creates your code <?php echo anchor('configurator/index/'.$database, 'Configure table data',array('title' => 'Generate the data','class' => 'generate-link', 'id' => 'configure' )) ?>.</p>
						<p>You can set up diffrent types of inputs for each table fileds.</p>

						<!-- Generate code, the config table must exists -->

						<?php if( $is_config ): ?>
						
							<h2<?php if ( $info_message == 'success' ): ?> class="lined"<?php endif ?>>Step 3: Generate source code</h2>
						   <p>Using <acronym class="yellow" title="This application">iScaffold</acronym> is very simple, simple click <?php echo anchor('generate/create/'.$database, 'Generate',array('title' => 'Generate the data','class' => 'generate-link', 'id' => 'generate' )) ?>.</p>

	                    <?php endif; ?>

					<?php if ( $info_message != 'step2' ): ?>
					</div>					
					<?php endif; ?>
			
				</div>
			</div>
			<!-- The footer -->
			<div id="footer">
				<p>&#169;2009-<?php echo date('Y'); ?> - Tibor Szász, &#214;m&#252;r Yolcu &#73;skender,  All rights reserved<br />This application is powered by CodeIgniter</p>
			</div>
		</div>
	</body>
</html>