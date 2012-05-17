{if $msg != ""}
<div class="error">
<h2>Error</h2>
{$msg}
</div>
{/if}

<br />

<table class="minorTable" style="width: 100%;">
<tr><th>download</th><th>date</th><th>name</th><th>type</th><th>size (b)</th><th>comment</th><th>eid</th><th>equipment</th><th>serial/size</th><th>history</th><tr>
{foreach item=file from=$files}
<tr>
<td><a href="viewFile.php?euid={$file.eu_id}">{$file.eu_id}</a></td>
<th>{$file.eu_ts|date_format:$config.datetime}</td>
<td>{$file.eu_name}</td>
<td>{$file.eu_type}</td>
<td>{$file.eu_size_b}</td>
<td>{$file.eu_comment}</td>
<td>{$file.eu_eid}</td>
<td>{$file.et_name} - {$file.em_name} {$file.emod_name}</td>
{if $file.e_size != ""}
<td>{$file.e_size}</td>
{else}
<td>{$file.e_serial}</td>
{/if}

<td><a href="history.php?eid={$file.e_id}">history</a></td>

</tr>
{/foreach}
</table>

