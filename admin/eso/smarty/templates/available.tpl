{if $msg != ""}
<div class="error">
<h2>Error</h2>
{$msg}
</div>
{/if}

{if $eid != null}
<div>
<form name="assign" method="POST">
<input type="hidden" name="eid" value="{$eid}" />
<p><strong>Assign Equipment:</strong><br />
{$myitem.et_name} - {$myitem.em_name} {$myitem.emod_name}
({$myitem.emod_model_num}) Serial {$myitem.e_serial}</p>
<p><strong>To: </strong>
<select name="EMTid" id="EMTid">
<option value="0">--SELECT--</option>
{foreach item=member from=$members}
<option value="{$member.EMTid}">{$member.LastName}, {$member.FirstName} ({$member.EMTid})</option>
{/foreach}
</select>
</p>
<p><input type="submit" value="Assign Item" /></p>
</form>
</div>
{/if}

<table class="minorTable" style="width: 100%;">
<tr><th>issue</th><th>id</th><th>type</th><th>mfr</th><th>model</th><th>serial/size</th><th>status</th><th>history</th><tr>
{foreach item=item from=$items}
<tr>
<td><a href="available.php?eid={$item.e_id}">issue</a></td>
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
<td><a href="history.php?eid={$item.e_id}">history</a></td>
</tr>
{/foreach}

</table>

