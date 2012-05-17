<div>
<label for="model">Model: </label>
<select name="model" id="model">
{foreach item=model from=$models}
<option value="{$model.id}">{$model.mfr} - {$model.model} - {$model.model_num}</option>
{/foreach}
</select>
</div>

<div>
<label for="serial">Serial Number: </label><input type="text" name="serial"
id="serial" size="30" />
</div>

<div>
<label for="signoutTo">Signed out to: </label>
<select name="signoutTo" id="signoutTo">
{foreach item=EMT from=$EMTs}
<option value="-1">BACK TO STOCK</option>
<option value="{$EMT.EMTid}">{$EMT.LastName}, {$EMT.FirstName} ({$EMT.EMTid})</option>
{/foreach}
</select>
</div>