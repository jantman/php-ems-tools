<table class="minorTable" style="width: 100%;">
<tr><th>EMTid</th><th>Name</th><th>type</th><th>mfr</th><th>model</th><th>serial/size</th><th>history</th><th>mark lost/stolen/broken</th><th>return</th><th>eid</th><tr>
{foreach item=item from=$items}
<tr>
<td>{$item.evt_EMTid}</td>
{if $item.evt_EMTid == "MPAC"}
<td>MPAC Building</td>
{else}
<td>{$item.LastName}, {$item.FirstName}</td>
{/if}
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

<td><a href="history.php?eid={$item.e_id}">history</a></td>
<td>mark lost/stolen/broken</td>
<td><a href="return.php?EMTid={$item.evt_EMTid}&eid={$item.e_id}">return</a></td>
<td>{$item.e_id}</td>

</tr>
{/foreach}
</table>
