{if $msg != ""}
<div class="error">
<h2>Error</h2>
{$msg}
</div>
{/if}

<div>
<form name="equipAdd" method="POST">
<input type="hidden" name="action" value="addEquip" />
<p><strong>Add Equipment:</strong></p>
<div id="addEquipForm">
<table>
<tr><td><strong>Type: </strong></td>
<td>
<select name="type_id" id="type_id" onChange="updateAddEquipForm()">
{foreach item=type from=$types}
<option value="{$type.et_id}">{$type.et_name}</option>
{/foreach}
</select>
</td></tr>
<tr><td colspan="2"><input type="submit" value="Add Item" /></td></tr>
</table>
</div>
</form>
</div>

</form>

<br /><br />

<table class="minorTable" style="width: 100%;">
<tr><th>id</th><th>type</th><th>mfr</th><th>model</th><th>serial/size</th><th>status</th><th>issued
to</th><th>history</th><th>mark lost</th><tr>
{foreach item=item from=$items}
<tr>
<td>{$item.e_id}</td>
<td>{$item.et_name}</td>
<td>{$item.em_name}</td>
<td>{$item.emod_name}

{if $item.emod_model_num != ""}
<br />({$item.emod_model_num})
{/if}

</td>

{if $item.e_size != ""}
<td>{$item.e_size}</td>
{else}
<td>{$item.e_serial}</td>
{/if}

<td>{$item.es_name}</td>

{if $item.evt_EMTid != ""}
<td>{$item.evt_EMTid}</td>
{else}
<td>&nbsp;</td>
{/if}

<td><a href="history.php?eid={$item.e_id}">history</a></td>
<td>mark lost/stolen/broken</td>

</tr>
{/foreach}
</table>

