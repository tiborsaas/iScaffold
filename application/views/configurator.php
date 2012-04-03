<?php

/****************************************************************************
 *  configurator.php
 *  The template loaded in the iframe
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <base href="<?php echo base_url(); ?>">

		<link rel="stylesheet" href="repo/generator/css/bootstrap.css" type="text/css" />
		<link rel="stylesheet" href="repo/generator/css/configurator.css" type="text/css" />

        <script type="text/javascript">
            var base_url = '<?php echo base_url(); ?>';
            var db_name = '<?=$db_name?>';
        </script>
    	<script src="repo/generator/js/mootools.js"></script>
		<script src="repo/generator/js/mootools-more.js"></script>
		<script src="repo/generator/js/configurator.js"></script>
		<title><?php echo "$app_name $app_version"; ?></title>
	</head>
	<body>
		<!-- The main container -->
		<div id="container">

			<div id="content">
				<div id="welcome">

                    <?php if( !empty( $config ) ): ?>
                        <div class="alert alert-success">
                            <a class="close" id="alert_close" data-dismiss="alert">×</a>
                            <p>You are ready to configure your database schema. Use the 'Save changes' button at the bottom of the page.</p>
    					</div>
                    <?php else: ?>

                        <div class="alert alert-block">
                            <p>Database table "sf_config" does not exists, <a href="<?=base_url()?>index.php/configurator/create_table/<?=$db_name?>" class="link-icon">click here to create it.</a></p>
    					</div>

                        <div id="conf-menu"  class="alert alert-info">
                            This action will create an "sf_config" table in your database named <em><?=$db_name?></em> to store the settings you made.
                            <br />
                            <br />
                            You can delete it after the code is generated, but I suggest you to keep it until your project is ready, you might want to
                            regenerate the source codes more than one time.
                        </div>
                        <br />
                        <br />
                    <?php endif; ?>

                    <div class="tabbable tabs-left" id="table_selector">
                        <ul class="nav nav-tabs">
                            <?php foreach( $config as $table => $tdata ): ?>
                                <li><a><?=$table?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div id="configurator">
                    <?php if( !empty( $config ) ): ?>
                        <?php foreach( $config as $table => $tdata ): ?>
                           <div class="table" rel="<?=$table?>">
                                <ul id="<?=$table?>">
                                    <?php foreach( $tdata['fields'] as $key => $field ): ?>
                                      <li>
                                        <h4><?=$field?></h4>
                                        <div class="field">

                    					   <label for="d_<?=$table?>-<?=$field?>">Label</label>
                    					   <input type="text" name="label" id="d_<?=$table?>-<?=$field?>" value="<?=$tdata['data'][$key]['sf_label']?>" /><br />

                    					   <label for="t_<?=$table?>-<?=$field?>">Field type</label>
                    					   <select name="type" id="t_<?=$table?>-<?=$field?>" onchange="conf.fieldState(this)" <?php if( $tdata['data'][$key]['sf_type'] == 'many_related' ): ?>disabled="disabled"<?php endif ?>>
                    					       <option value="default" <?php if( $tdata['data'][$key]['sf_type'] == 'default' ): ?>selected="selected"<?php endif ?>>Default input</option>
                    					       <option value="checkbox" <?php if( $tdata['data'][$key]['sf_type'] == 'checkbox' ): ?>selected="selected"<?php endif ?>>Checkbox</option>
                    					       <option value="textarea" <?php if( $tdata['data'][$key]['sf_type'] == 'textarea' ): ?>selected="selected"<?php endif ?>>Textarea</option>
                    					       <option value="wysiwyg" <?php if( $tdata['data'][$key]['sf_type'] == 'wysiwyg' ): ?>selected="selected"<?php endif ?>>WYSIWYG</option>
                    					       <option value="file" <?php if( $tdata['data'][$key]['sf_type'] == 'file' ): ?>selected="selected"<?php endif ?>>File</option>
                    					       <option value="date" <?php if( $tdata['data'][$key]['sf_type'] == 'date' ): ?>selected="selected"<?php endif ?>>Date</option>
                    					       <option value="enum_values" <?php if( $tdata['data'][$key]['sf_type'] == 'enum_values' ): ?>selected="selected"<?php endif ?>>Enum data set</option>
                    					       <option value="related" <?php if( $tdata['data'][$key]['sf_type'] == 'related' ): ?>selected="selected"<?php endif ?>>Related table: one => one</option>
                    					       <option value="many_related" <?php if( $tdata['data'][$key]['sf_type'] == 'many_related' ): ?>selected="selected"<?php endif ?> disabled="disabled">Related table: one => many</option>
                                           </select><br />

                                           <div class="related<?php if( $tdata['data'][$key]['sf_type'] == 'related' || $tdata['data'][$key]['sf_type'] == 'many_related' ): ?> show<?php endif ?>">
                        					   <label for="de_<?=$table?>-<?=$field?>">Related ID & field</label>
                        					   <div class="select_holder">
                                                <?php if( $tdata['data'][$key]['sf_type'] == 'related' || $tdata['data'][$key]['sf_type'] == 'many_related' ): ?>
                                                    <select name="related_id" onchange="conf.disableInvalids( this )">
                                                    <?php foreach( $schema as $tbl => $td ): ?>
                                                        <?php foreach( $td as $fl ): ?>
                                                    	   <option value="<?=$tbl?>|<?=$fl?>"<?php if( $tdata['data'][$key]['sf_related'][1] == $fl ): ?> selected="selected"<?php endif ?>><?=$tbl?> -> <?=$fl?></option>
                                                    	<?php endforeach ?>
                                                    <?php endforeach ?>
                                                    </select>
                                                    <select name="related_name">
                                                    <?php foreach( $schema as $tbl => $td ): ?>
                                                        <?php foreach( $td as $fl ): ?>
                                                    	   <option value="<?=$tbl?>|<?=$fl?>"<?php if( $tdata['data'][$key]['sf_related'][3] == $fl ): ?> selected="selected"<?php endif ?>><?=$tbl?> -> <?=$fl?></option>
                                                    	<?php endforeach ?>
                                                    <?php endforeach ?>
                                                    </select>
                                                <?php endif ?>
                                               </div>
                                               <?php if( $tdata['data'][$key]['sf_type'] == 'many_related' ): ?>
                                    			   <div class="related_desc"><img src="repo/generator/img/info.gif" alt="Info" />First, select the ID to store in the switch table, then the field you wish to be displayed.</div>
                                    			   <div class="many_related_desc"><img src="repo/generator/img/red.gif" alt="Info" />Be sure, that your database has a 'switch' table, named <strong><?=$table?>_<span></span></strong></div>
                                               <?php else: ?>
                            					   <div class="related_desc"><img src="repo/generator/img/info.gif" alt="Info" />First, select the ID to join, then the field you wish to be listed.</div>
                                               <?php endif ?>
                                           </div>

                    					   <label for="de_<?=$table?>-<?=$field?>">Description</label>
                    					   <input type="text" name="desc" id="de_<?=$table?>-<?=$field?>" size="80" value="<?=$tdata['data'][$key]['sf_desc']?>"  /><br />

                    					   <label for="h_<?=$table?>-<?=$field?>">Hide in listing</label>
                    					   <input type="checkbox" name="hidden" id="h_<?=$table?>-<?=$field?>" <?php if( $tdata['data'][$key]['sf_hidden'] == 1 ): ?>checked="checked"<?php endif ?>/><br />
                                        </div>
                                      </li>
                                    <?php endforeach; ?>
                                </ul>
					           <a href="javascript:conf.addRelation('<?=$table?>')" class="button">Add many relation</a>
    					   </div>
                        <?php endforeach; ?>
					<?php endif; ?>
                    </div>
				</div>
			</div>


			<!-- The footer -->
            <?php if( !empty( $config ) ): ?>
                <div id="danger_actions" class="alert alert-error">
                    <h4>Danger zone!</h4>
                    <p id="conf-menu">
                        <a href="javascript:conf.check( '<?=base_url()?>index.php/configurator/modify/reset/<?=$db_name?>', 'Are you sure? This action will erase all the settings you made.' )" id="" class="link-icon">Reset config table</a> delete all settings you made, but keep the config table's fields.<br />
                        <a href="javascript:conf.check( '<?=base_url()?>index.php/configurator/modify/generate/<?=$db_name?>', 'Are you sure? This action will drop the config table.')" id="" class="link-icon">Regenerate config table</a> truncates config table and refills it from your database schema.<br />
                        <a href="javascript:conf.check( '<?=base_url()?>index.php/configurator/modify/update/<?=$db_name?>', 'Are you sure? This action will modify the settings you made.')" id="" class="link-icon">Update config table</a> scans your schema, if you made changes, like a new table or field, it will be added to the config table.<br />
                    </p>
                </div>
            <?php endif; ?>


			<div id="templates">

                <!-- SELECT TEMPLATE FOR RELATIONS -->
                <select id="schema_list">
                <?php foreach( $schema as $table => $tdata ): ?>
                    <?php foreach( $tdata as $field ): ?>
                	   <option value="<?=$table?>|<?=$field?>"><?=$table?> -> <?=$field?></option>
                	<?php endforeach ?>
                <?php endforeach ?>
                </select>

			    <!-- LIST TEMPLATE FOR MANY RELATIONS -->
                <ul>
                  <li id="field_template">
                    <h4>Many realtion #</h4>
                    <div class="field">
            
            		   <label for="">Field id</label>
            		   <input type="text" name="field_id" id="" value="" /> Give it a name like a table field<br />

            		   <label for="">Type</label>
            		   <select name="type" id="" disabled="disabled" />
            		      <option value="many_related">Many related</option>
                       </select>
                       <br />

            		   <label for="">Label</label>
            		   <input type="text" name="label" id="" value="" /><br />
    
                       <div class="related">
            			   <label for="">Related ID & field</label>
            			   <div class="select_holder"></div>
            			   <div class="related_desc"><img src="repo/generator/img/info.gif" alt="Info" />First, select the ID to store in the switch table, then the field you wish to be displayed.</div>
            			   <div class="many_related_desc"><img src="repo/generator/img/red.gif" alt="Info" />Be sure, that your database has a 'switch' table, named <strong><?=$table?>_<span></span></strong></div>
                       </div>

            		   <label for="">Description</label>
            		   <input type="text" name="desc" id="" size="80" value=""  /><br />
            
            		   <label for="">Hidden</label>
            		   <input type="checkbox" name="hidden" id="" disabled="disabled" /><br />
					   <a href="javascript:conf.removeRelation()" class="button negative" id="save_button">Delete this relation</a>
                    </div>
                  </li>
                </ul>

		    </div>
		</div>
	</body>
</html>