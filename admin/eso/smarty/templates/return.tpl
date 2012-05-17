{if $msg != ""}
<div class="error">
<h2>Error</h2>
{$msg}
</div>
{/if}




<div>
<form name="equipReturn" method="POST">
<input type="hidden" name="action" value="return" />
<div id="returnEquipForm">

{if $form != ""}
{$form}
{else}
<table>
<tr><td><strong>Member: </strong></td>
<td>
<select name="EMTid" id="EMTid" onChange="updateReturnEquipForm()">
<option value="0">Select A Member</option>
{foreach item=member from=$members}
<option value="{$member.evt_EMTid}">{$member.evt_EMTid} - {$member.LastName}, {$member.FirstName}</option>
{/foreach}
</select>
</td></tr>
<tr><td colspan="2"><input type="submit" value="Return Item" /></td></tr>
</table>
{/if}

</div>
</form>
</div>