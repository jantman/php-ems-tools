<form name="equipSignout" method="POST">

<p><strong>Pages:</strong></p>
<ul>
<li><strong>Available</strong> - List equipment in stock and available for
use. Sign out equipment to members.</li>
<li><strong>Return</strong> - Return to stock equipment that was issued to a
member.</li>
<li><strong>Members</strong> - Show equipment signed out to each
member. Search for detailed history for a specific member.</li>
<li><strong>History</strong> - Show assignment and service history for a piece
of equipment.</li>
<li><strong>Equipment</strong> - Add new equipment, mark equipment as lost,
stolen or broken.</li>
<li><strong>Service</strong> - Search for a piece of equipment by serial
number, view service history. Add service history to a piece of
equipment.</li>
<li><strong>Admin</strong> - Add equipment types, manufacturers or models.</li>
</ul>

</form>

<p><strong>Equipment with Problems:</strong></p>

<table class="minorTable" style="width: 100%;">
<tr><th>id</th><th>type</th><th>mfr</th><th>model</th><th>serial/size</th><th>status</th><th>history</th><tr>
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

<td><a href="history.php?eid={$item.e_id}">history</a></td>

</tr>
{/foreach}
</table>