
    %default%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
    		<div>
    	       	<input class="text_field" type="text" maxlength="255" value="{if isset($%NAME_TABLE%_data)}{$%NAME_TABLE%_data.%FIELD_NAME%}{/if}" name="%FIELD_NAME%" />
    		</div>
    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
    	</div>
    %/default%

    %textarea%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
    		<div>
        		<textarea rows="10" cols="50" class="text_area" name="%FIELD_NAME%">{if isset($%NAME_TABLE%_data)}{$%NAME_TABLE%_data.%FIELD_NAME%}{/if}</textarea>
    		</div>
    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
    	</div>
    %/textarea%

    %WYSIWYG%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
    		<div>
        		<textarea rows="10" cols="50" class="text_area wysiwyg" name="%FIELD_NAME%" >{if isset($%NAME_TABLE%_data)}{$%NAME_TABLE%_data.%FIELD_NAME%}{/if}</textarea>
    		</div>
    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
    	</div>
    %/WYSIWYG%

    %checkbox%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
            <input class="field checkbox" type="checkbox" value="1" name="%FIELD_NAME%"{if isset($%NAME_TABLE%_data)}{if $%NAME_TABLE%_data.%FIELD_NAME% == 1} checked="checked"{/if}{/if} />

    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
    	</div>
    %/checkbox%

    %file%
    	<div class="group">
        	<fieldset>
                <legend class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</legend>
                <input type="hidden" value="{if isset($%NAME_TABLE%_data)}{$%NAME_TABLE%_data.%FIELD_NAME%}{/if}" name="%FIELD_NAME%-original-name" />
                {if isset($%NAME_TABLE%_data.%FIELD_NAME%)}
                    {if !$%NAME_TABLE%_data.%FIELD_NAME%}
                        <p>No file uploaded</p>
                    {else}
                        <p>File uploaded: <a href="uploads/{$%NAME_TABLE%_data.%FIELD_NAME%}">{$%NAME_TABLE%_data.%FIELD_NAME%}</a></p>
                    {/if}
                {/if}
                <input class="field file" type="file" name="%FIELD_NAME%" />
        		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
        	</fieldset>
    	</div>
    %/file%

    %date%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
    		<span>
    		      <input class="text_field datepicker short" name="%FIELD_NAME%" size="16" type="text" maxlength="16" value="{if isset($%NAME_TABLE%_data)}{$%NAME_TABLE%_data.%FIELD_NAME%|date_format:"Y-m-d H:i"}{/if}"/>
    		      <label>YYYY-MM-DD HH:MM</label>
    		</span>
    		<span>
    		      <img src="iscaffold/images/calendar.png" class="icon" alt="Pick date." />
    		</span>
    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
    	</div>
    %/date%

    %related%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
    		<select class="field select addr" name="%FIELD_NAME%" >
                <option value="0"></option>
                {foreach $related_%RELATED_TABLE% as $rel}
                    <option value="{$rel.%RELATED_TABLE%_id}"{if isset($%NAME_TABLE%_data)}{if $%NAME_TABLE%_data.%FIELD_NAME% == $rel.%RELATED_TABLE%_id} selected="selected"{/if}{/if}>{$rel.%RELATED_TABLE%_name}</option>
                {/foreach}
        	</select>
    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
        </div>
    %/related%

    
    %many_related%
    	<div class="group">
        	<fieldset>
                <legend class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}</legend>
                        {foreach $related_%RELATED_TABLE% as $rel}
                		    <span>
                                <input id="chk-{$rel.%RELATED_TABLE%_id}" class="field checkbox" type="checkbox" value="{$rel.%RELATED_TABLE%_id}" name="%FIELD_NAME%[]" {if isset($%NAME_TABLE%_%RELATED_TABLE%_data)}{if in_array( $rel.%RELATED_TABLE%_id, $%NAME_TABLE%_%RELATED_TABLE%_data )}checked="checked" {/if}{/if}/>
                                <label for="chk-{$rel.%RELATED_TABLE%_id}" class="choice">{$rel.%RELATED_TABLE%_name}</label>
                            </span>
                        {/foreach}
                <br clear="left" />
                <br />

                <button class="button" type="button" onclick="chk_selector( 'all', this )">
                    Select all
                </button>
                <button class="button" type="button" onclick="chk_selector( 'none', this )">
                    Select none
                </button>
                %IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
            </fieldset>
       	</div>
    %/many_related%


    %enum_values%
    	<div class="group">
            <label class="label">{$%NAME_TABLE%_fields.%FIELD_NAME%}%IF_REQUIRED%<span class="error">*</span>%/IF_REQUIRED%</label>
            %IF_REQUIRED%<span class="error">can't be blank</span>%/IF_REQUIRED%
        	<div class="block">
        	<span class="left">
        		<select class="field select addr" name="%FIELD_NAME%" >
                    <option value="0"></option>
                    {foreach $metadata.%FIELD_NAME%.enum_values as $k => $e}
                        <option value="{$e}"{if isset($%NAME_TABLE%_data.%FIELD_NAME%)}{if $%NAME_TABLE%_data == $metadata.%FIELD_NAME%.enum_names[$k]} selected="selected"{/if}{/if}>{$metadata.%FIELD_NAME%.enum_names[$k]}</option>
                    {/foreach}
            	</select>
            </span>
            </div>
    		%IF_FIELD_DESC%<p class="instruct">%FIELD_DESC%</p>%/IF_FIELD_DESC%
        </div>
    %/enum_values%

