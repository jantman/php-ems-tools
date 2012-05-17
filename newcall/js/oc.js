/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-04 13:00:57 jantman"                                                              |
 +--------------------------------------------------------------------------------------------------------+
 | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
 |                                                                                                        |
 | This program is free software; you can redistribute it and/or modify                                   |
 | it under the terms of the GNU General Public License as published by                                   |
 | the Free Software Foundation; either version 3 of the License, or                                      |
 | (at your option) any later version.                                                                    |
 |                                                                                                        |
 | This program is distributed in the hope that it will be useful,                                        |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
 | GNU General Public License for more details.                                                           |
 |                                                                                                        |
 | You should have received a copy of the GNU General Public License                                      |
 | along with this program; if not, write to:                                                             |
 |                                                                                                        |
 | Free Software Foundation, Inc.                                                                         |
 | 59 Temple Place - Suite 330                                                                            |
 | Boston, MA 02111-1307, USA.                                                                            |
 +--------------------------------------------------------------------------------------------------------+
 |Please use the above URL for bug reports and feature/support requests.                                  |
 +--------------------------------------------------------------------------------------------------------+
 | Authors: Jason Antman <jason@jasonantman.com>                                                          |
 +--------------------------------------------------------------------------------------------------------+
 | $LastChangedRevision:: 45                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/oc.js                                               $ |
 +--------------------------------------------------------------------------------------------------------+
*/

function oc_nocrew()
{
  var oc = new Array("unit_td", "ptof_td", "mileage_td", "loc_td", "injarea_td", "injured_area");
  hide_crew(1);
  hide_txrow();
  hide_vitals();
  hide_transto();
  hide_clinical();
  hide_patient();
  hide_rig();
}

function oc_undo()
{
  var oc = new Array("unit_td", "ptof_td", "mileage_td", "loc_td", "injarea_td", "injured_area");
  show_crew(0);
  show_txrow();
  show_vitals();
  show_transto();
  show_clinical();
  show_patient();
  show_rig();
}

function show_rig()
{
  document.getElementById("unit_td").style.display = "table-cell";
  document.getElementById("mileage_td").style.display = "table-cell";  
}

function hide_rig()
{
  document.getElementById("unit_td").style.display = "none";
  document.getElementById("mileage_td").style.display = "none";  
}

function hide_transto()
{
  document.getElementById("transto_td").style.display = "none";
}

function show_transto()
{
  document.getElementById("transto_td").style.display = "table-cell";
}

function hide_vitals()
{
  document.getElementById("vitals_tr").style.display = "none";
}

function show_vitals()
{
  document.getElementById("vitals_tr").style.display = "table-row";
}

function hide_txrow()
{
  document.getElementById("tx_inj_loc_tr").style.display = "none";
}

function show_txrow()
{
  document.getElementById("tx_inj_loc_tr").style.display = "table-row";
}

function hide_clinical()
{
  document.getElementById("clinical_tr").style.display = "none";
}

function show_clinical()
{
  document.getElementById("clinical_tr").style.display = "table-row";
}

function show_crew(leaveNum)
{
  var x = parseInt(document.getElementById("nextCrewRow").value);
  for(var i = leaveNum; i < x; i++)
  {
    document.getElementById("crew_tr_" + i).style.display = "table-row";
  }
  document.getElementById("addcrew_span").style.display = "inline";
}

function hide_crew(leaveNum)
{
  var x = parseInt(document.getElementById("nextCrewRow").value);
  for(var i = leaveNum; i < x; i++)
  {
    document.getElementById("crew_tr_" + i).style.display = "none";
  }
  document.getElementById("addcrew_span").style.display = "none";
}

function show_patient()
{
  document.getElementById("pt_physican_td").style.display = "table-cell";
  document.getElementById("pt_loc_scene_td").style.display = "table-cell";
  document.getElementById("pt_info_td").style.display = "table-cell";
  document.getElementById("pt_age_td").style.display = "table-cell";
  document.getElementById("pt_dob_td").style.display = "table-cell";
  document.getElementById("pt_sex_td").style.display = "table-cell";
  document.getElementById("ptof_td").style.display = "table-cell";
  document.getElementById("pt_name_tr").style.display = "table-row";
  document.getElementById("pt_address_tr").style.display = "table-row";
}

function hide_patient()
{
  document.getElementById("pt_physican_td").style.display = "none";
  document.getElementById("pt_loc_scene_td").style.display = "none";
  document.getElementById("pt_info_td").style.display = "none";
  document.getElementById("pt_age_td").style.display = "none";
  document.getElementById("pt_dob_td").style.display = "none";
  document.getElementById("pt_sex_td").style.display = "none";
  document.getElementById("ptof_td").style.display = "none";
  document.getElementById("pt_name_tr").style.display = "none";
  document.getElementById("pt_address_tr").style.display = "none";
}
