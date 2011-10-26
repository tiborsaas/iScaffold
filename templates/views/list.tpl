<div class="block" id="block-tables">

                <div class="secondary-navigation">
                    <ul class="wat-cf">
                        <li class="first active"><a href="%NAME_TABLE%">Listing</a></li>
                        <li><a href="%NAME_TABLE%/create/">New record</a></li>
                    </ul>
                </div>

                <div class="content">
                    <div class="inner">
                        <h3>List of {$table_name}</h3>

                        {if !empty( $%NAME_TABLE%_data )}
                        <form action="%NAME_TABLE%/delete" method="post" id="listing_form">
                            <table class="table">
                            	<thead>
                                    <th width="20"> </th>
                                    %TABLE_HEADER%
                                    <th width="80">Actions</th>
                            	</thead>
                            	<tbody>
                                	{foreach $%NAME_TABLE%_data as $row}
                                        <tr class="{cycle values='odd,even'}">
                                            <td><input type="checkbox" class="checkbox" name="delete_ids[]" value="{$row.%FIELD_ID%}" /></td>
                                            %TABLE_CONTENTS%
                                            <td width="80">
                                                <a href="%NAME_TABLE%/show/{$row.%FIELD_ID%}"><img src="iscaffold/images/view.png" alt="Show record" /></a>
                                                <a href="%NAME_TABLE%/edit/{$row.%FIELD_ID%}"><img src="iscaffold/images/edit.png" alt="Edit record" /></a>
                                                <a href="javascript:chk('%NAME_TABLE%/delete/{$row.%FIELD_ID%}')"><img src="iscaffold/images/delete.png" alt="Delete record" /></a>
                                            </td>
                            		    </tr>
                                	{/foreach}
                            	</tbody>
                            </table>
                            <div class="actions-bar wat-cf">
                                <div class="actions">
                                    <button class="button" type="submit">
                                        <img src="iscaffold/backend_skins/images/icons/cross.png" alt="Delete"> Delete selected
                                    </button>
                                </div>
                                <div class="pagination">
                                    {$pager}
                                </div>
                            </div>
                        </form>
                        {else}
                            No records found.
                        {/if}

                    </div><!-- .inner -->
                </div><!-- .content -->
            </div><!-- .block -->
