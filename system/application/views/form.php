

<?php
	// Build the controls.
	
	foreach ( $fields as $field ) 
	{
		echo '<? form_input( $field, $values[ '. $field .' ] ) ?>';
		echo "\n";
		echo '<br />';
		echo "\n";
		echo "\n";
	}

?>

<?php if ( $form_mode = 'create' ): ?>
	<?= form_open( ) ?>
<?php elseif ( $form_mode = 'edit' ): ?>
	
<?php endif ?>


