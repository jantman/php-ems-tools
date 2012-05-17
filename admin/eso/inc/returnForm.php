<?php

function genReturnForm($EMTid = null, $eid = null)
{
    // type
    $res = "<table>";
    $res .= "<tr><td><strong>Member: </strong></td><td>\n";
    $res .= genReturnFormMember($EMTid, $eid);
    $res .= '</td></tr>'."\n";

    if($_GET['EMTid'] == 0){ return $res;}

    $res .= "<tr><td><strong>Item: </strong></td><td>\n";
    $res .= genReturnFormEquip($EMTid, $eid);
    $res .= '</td></tr>'."\n";

    $res .= "<tr><td><strong>Reason for Return: </strong></td><td>\n";
    $res .= '<select name="reason" id="reason">'."\n";
    $res .= '<option value="0">Select a Reason</option>'."\n";
    $res .= '<option value="Resignation">Resignation</option>'."\n";
    $res .= '<option value="Broken">Broken</option>'."\n";
    $res .= '<option value="Equip Problems">Equip Problems</option>'."\n";
    $res .= '<option value="Replacement/Loaner">Replacement/Loaner</option>'."\n";
    $res .= '<option value="Upgrade">Upgrade</option>'."\n";
    //$res .= '<option value=""></option>'."\n";
    $res .= '<option value="Other">Other - Enter reason:</option>'."\n";
    $res .= '</select>'."\n";
    $res .= '<input type="text" size="20" name="returnReason" id="returnReason" />'."\n";
    $res .= '</td></tr>'."\n";

    $res .= '<tr><td colspan="2"><input type="submit" value="Return Item" /></td></tr>'."\n";
    $res .= '</table>'."\n";
    return $res;
}

function genReturnFormEquip($EMTid, $eid)
{
    $res = "";
    if($eid == null)
    {
	$query = "SELECT eq.e_serial,eq.e_id,et.et_name,emod.emod_name,emfr.em_name FROM eso_equipment AS eq LEFT JOIN eso_events AS evt ON eq.e_id=evt.evt_equip_id LEFT JOIN eso_opt_equipTypes AS et ON eq.e_et_id=et.et_id LEFT JOIN eso_opt_model AS emod ON eq.e_emod_id=emod.emod_id LEFT JOIN eso_opt_mfr AS emfr ON emfr.em_id=emod.emod_mfr_id WHERE evt.evt_status_id=2 AND evt.evt_EMTid IS NOT NULL AND evt.evt_is_deprecated=0 AND evt.evt_EMTid='".mysql_real_escape_string($EMTid)."';";
	$result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
	$res .= '<select name="eq_id" id="eq_id">'."\n";
	$res .= '<option value="0">Select Equipment to Return</option>'."\n";
	while($row = mysql_fetch_assoc($result))
	{
	    $res .= '<option value="'.$row['e_id'].'" ';
	    $res .= '>'.$row['et_name'].' - '.$row['em_name'].' '.$row['emod_name'].' '.$row['e_serial'].' ('.$row['e_id'].')</option>'."\n";
	}
	$res .= '</select>'."\n";
	return $res;
    }

    // else
    $query = "SELECT eq.e_serial,eq.e_id,et.et_name,emod.emod_name,emfr.em_name FROM eso_equipment AS eq LEFT JOIN eso_events AS evt ON eq.e_id=evt.evt_equip_id LEFT JOIN eso_opt_equipTypes AS et ON eq.e_et_id=et.et_id LEFT JOIN eso_opt_model AS emod ON eq.e_emod_id=emod.emod_id LEFT JOIN eso_opt_mfr AS emfr ON emfr.em_id=emod.emod_mfr_id WHERE evt.evt_status_id=2 AND evt.evt_EMTid IS NOT NULL AND evt.evt_is_deprecated=0 AND evt.evt_equip_id=".((int)$eid).";";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    $row = mysql_fetch_assoc($result);
    $res = $row['et_name'].' - '.$row['em_name'].' '.$row['emod_name'].' '.$row['e_serial'].' ('.$row['e_id'].')'."\n";
    $res .= '<input type="hidden" name="eq_id" id="eq_id" value="'.((int)$eid).'" />'."\n";
    return $res;


}

function genReturnFormMember($member, $eid)
{
    $res = "";
    if($eid == null)
    {
	$query = "SELECT DISTINCT e.evt_EMTid,r.FirstName,r.LastName FROM eso_events AS e LEFT JOIN roster AS r ON e.evt_EMTid=r.EMTid WHERE e.evt_EMTid IS NOT NULL;";
	$result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
	$res .= '<select name="EMTid" id="EMTid" onChange="updateReturnEquipForm()">'."\n";
	$res .= '<option value="0">Select A Member</option>';
	while($row = mysql_fetch_assoc($result))
	{
	    $res .= '<option value="'.$row['evt_EMTid'].'"';
	    if($row['evt_EMTid'] == $_GET['EMTid']){ $res .= ' selected="selected"';}
	    $res .= '>'.$row['evt_EMTid']." - ".$row['LastName'].", ".$row['FirstName'].'</option>'."\n";
	}
	$res .= '</select>'."\n";
	return $res;
    }
    
    // else...
    $query = "SELECT DISTINCT e.evt_EMTid,r.FirstName,r.LastName FROM eso_events AS e LEFT JOIN roster AS r ON e.evt_EMTid=r.EMTid WHERE e.evt_EMTid='".mysql_real_escape_string($member)."';";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    $row = mysql_fetch_assoc($result);
    $res = $member." - ".$row['FirstName'].", ".$row['LastName'];
    $res .= '<input type="hidden" name="EMTid" id="EMTid" value="'.$member.'" />'."\n";
    return $res;
}

?>