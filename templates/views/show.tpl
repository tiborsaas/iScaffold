<div class="block" id="block-tables">

                <div class="secondary-navigation">
                    <ul class="wat-cf">
                        <li class="first"><a href="%NAME_TABLE%">Listing</a></li>
                        <li><a href="%NAME_TABLE%/create/">New record</a></li>
                    </ul>
                </div>

                <div class="content">
                    <div class="inner">
						<h3>Details of {$table_name}, record #{$id}</h3>

						<table class="table" width="50%">
                        	<thead>
                                <th width="20%">Field</th>
                                <th>Value</th>
                        	</thead>
						    %FIELD_LOOP%
						        <tr class="{cycle values='odd,even'}">
						            <td>{$%NAME_TABLE%_fields.%FIELD_ID%}:</td>
						            <td>{$%NAME_TABLE%_data.%FIELD_ID%}</td>
						        </tr>
						    %/FIELD_LOOP%
						</table>
                        <div class="actions-bar wat-cf">
                            <div class="actions">
                                <a class="button" href="%NAME_TABLE%/edit/{$id}">
                                    <img src="iscaffold/backend_skins/images/icons/application_edit.png" alt="Edit record"> Edit record
                                </a>
                            </div>
                        </div>
                    </div><!-- .inner -->
                </div><!-- .content -->
            </div><!-- .block -->
