{if $msg != ""}
<div class="error">
<h2>Error</h2>
{$msg}
</div>
{/if}

<form name="hx" method="POST"><strong>Search History by Serial Number:</strong>
<input type="text" size="15" name="serno" id="serno" /><input type="submit" value="Search" /></form>

<form name="hx" method="POST">
<strong>Search History for Item:</strong>
<select name="eid" id="eid">
{foreach item=item from=$items}
<option value="{$item.eid}">{$item.str}</option>
{/foreach}
</select>
<input type="submit" value="Search" /></form>

{if $hx != null}
<hr />
<p><strong>History for Equipment ID {$itemID}:</strong> {$itemDesc}</p>
<table class="minorTable" style="width: 100%;">
<tr><th>date</th><th>entry</th><tr>
{foreach item=foo from=$hx}
<tr>
<td>{$foo.date}</td>
<td>{$foo.text}</td>
</tr>
{/foreach}
</table>
<p><a href="service.php?eid={$itemID}">Enter service history for this item</a></p>

<br />
<p><strong>Files:</strong></p>
<table class="minorTable" style="width: 100%;">
<tr><th>id</th><th>download</th><th>date</th><th>name</th><th>type</th><th>size (b)</th><th>comment</th><tr>
{foreach item=file from=$files}
<tr>
<td>{$file.eu_id}</td>
<td><a href="viewFile.php?euid={$file.eu_id}">download</a></td>
<th>{$file.eu_ts|date_format:$config.datetime}</td>
<td>{$file.eu_name}</td>
<td>{$file.eu_type}</td>
<td>{$file.eu_size_b}</td>
<td>{$file.eu_comment}</td>
</tr>
{/foreach}
</table>

{/if}