{if $msg != ""}
<div class="error">
<h2>Error</h2>
{$msg}
</div>
{/if}

<div style="float: left; width: 40%; margin-right: 5%; margin-left: 5%;">
<p style="text-align: center;"><strong>Equipment Types</strong></p>
<table class="minorTable" style="width: 100%;">
<tr><th>id</th><th>name</th></tr>
{foreach item=type from=$types}
<tr><td>{$type.et_id}</td><td>{$type.et_name}</td></tr>
{/foreach}
</table>

<div>
<form name="typeAdd" method="POST">
<input type="hidden" name="action" value="addType" />
<p><strong>Add Equipment Type:</strong></p>
<table>
<tr><td><strong>Name: </strong></td><td><input type="text" size="30" id="name" name="name" /></td></tr>
<tr><td colspan="2"><input type="submit" value="Add Type" /></td></tr>
</table>
</form>
</div>

</div> <!-- end float div -->

<div style="float: right; width: 40%; margin-right: 5%; margin-left: 5%;">
<p style="text-align: center;"><strong>Manufacturers</strong></p>
<table class="minorTable" style="width: 100%;">
<tr><th>id</th><th>type</th><th>name</th></tr>
{foreach item=mfr from=$mfrs}
<tr><td>{$mfr.em_id}</td><td>{$mfr.type}</td><td>{$mfr.name}</td></tr>
{/foreach}
</table>

<div>
<form name="typeAdd" method="POST">
<input type="hidden" name="action" value="addMfr" />
<p><strong>Add Equipment Manufacturer:</strong></p>
<table>
<tr><td><strong>Type: </strong></td>
<td>
<select name="type_id" id="type_id">
{foreach item=type from=$types}
<option value="{$type.et_id}">{$type.et_name}</option>
{/foreach}
</select>
</td></tr>
<tr><td><strong>Name: </strong></td><td><input type="text" size="30" id="name" name="name" /></td></tr>
<tr><td colspan="2"><input type="submit" value="Add Manufacturer" /></td></tr>
</table>
</form>
</div>

</div> <!-- end float div -->

<div class="clearing">&nbsp;</div>

<div style="float: left; width: 40%; margin-right: 5%; margin-left: 5%;">
<p style="text-align: center;"><strong>Models</strong></p>
<table class="minorTable" style="width: 100%;">
<tr><th>type</th><th>manufacturer</th><th>model</th><th>model &#35;</th></tr>
{foreach item=model from=$models}
<tr><td>{$model.type}</td><td>{$model.mfr}</td><td>{$model.name}</td><td>{$model.modelnum}</td></tr>
{/foreach}
</table>

<div>
<form name="typeAdd" method="POST">
<input type="hidden" name="action" value="addModel" />
<p><strong>Add Equipment Model:</strong></p>
<table>
<tr><td><strong>Type/Manufacturer: </strong></td>
<td>
<select name="mfr_id" id="mfr_id">
{foreach item=mfr from=$mfrs}
<option value="{$mfr.em_id}">{$mfr.type} / {$mfr.name}</option>
{/foreach}
</select>
</td></tr>
<tr><td><strong>Model: </strong></td><td><input type="text" size="30" id="model" name="model" /></td></tr>
<tr><td><strong>Model &#35;: </strong></td><td><input type="text" size="30" id="modelnum" name="modelnum" /></td></tr>
<tr><td><strong>Has Sizes</strong></td><td><input type="checkbox" id="sizes" name="sizes" /></td></tr>
<tr><td colspan="2"><input type="submit" value="Add Model" /></td></tr>
</table>
</form>
</div>

</div> <!-- end float div -->

<div class="clearing">&nbsp;</div>

<div style="float: left; width: 40%; margin-right: 5%; margin-left: 5%;">
&nbsp;
</div>

</div>

<div style="float: right; width: 40%; margin-right: 5%; margin-left: 5%;">
&nbsp;
</div>
<div class="clearing">&nbsp;</div>
