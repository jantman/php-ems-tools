<p>Enter service history for an item.</p>

<p style="border: 1px solid black;">{$msg}</p>

{if $eid == ""}
<form name="hx" method="GET">
<strong>Enter History for Item:</strong>
<select name="eid" id="eid">
{foreach item=item from=$items}
<option value="{$item.eid}">{$item.str}</option>
{/foreach}
</select>
<input type="submit" value="Select" /></form>
{else}
<p><strong>Equipment ID {$eid}:</strong> {$itemDesc}</p>
<div style="border-top: 1px solid black;">
<form name="comment" method="POST">
<input type="hidden" name="action" value="comment" />
<input type="hidden" name="eid" value="{$eid}" />
<p><strong>Add Equipment Service History:</strong></p>
<p><strong>Comment:</strong><textarea rows="5" cols="50" name="comment" id="comment"></textarea></p>
<p><input type="submit" value="Add Comment" /></p>
</form>
</div>

<div style="border-top: 1px solid black;">
<form name="file" method="POST" enctype="multipart/form-data">
<input type="hidden" name="action" value="file" />
<input type="hidden" name="eid" value="{$eid}" />
<p><strong>Upload File:</strong></p>
<p><strong>Type:</strong>
<select name="fileType" id="fileType">
<option value="CPS">PPS/CPS</option>
<option value="PDF">PDF - PPS/CPS Printout</option>
</select>
</p>
<p><strong>Comment:</strong><input type="text" size="30" name="comment" id="comment" /></p>
<p><input type="file" name="uploadFile" id="uploadFile" /></p>
<p><input type="submit" value="Upload File" /></p>
</form>
</div>

<div style="border-top: 1px solid black;">
<form name="status" method="POST">
<input type="hidden" name="action" value="status" />
<input type="hidden" name="eid" value="{$eid}" />
<p><strong>Update Equipment Status:</strong></p>
<p><strong>New Status:</strong>
<select name="statusID" id="statusID">
<option value="4">Out for Service</option>
<option value="6">Broken</option>
<option value="7">Possible Problem</option>
<option value="5">In Stock</option>
<option value="8">Unknown/Lost (Missing)</option>
<option value="11">Decommissioned</option>
</select>
</p>
<p><input type="submit" value="Update Status" /></p>
</form>
</div>
{/if}
