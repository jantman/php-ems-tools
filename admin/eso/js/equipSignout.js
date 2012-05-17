//
// SCHEDULE-SPECIFIC FUNCTIONS
//

function updateType()
{
  doHTTPrequest(('inc/equipSignoutFormPart.php?type='+document.getElementById('type').value), handleNewTypeFormRequest);
}

function handleNewTypeFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('byType').innerHTML = response;
  }
}

function updateEquipSelect()
{
  
}

// Add Equipment Form

function updateAddEquipForm()
{
  var url = "";
  url = "type=" + document.getElementById("type_id").value;
  if(document.getElementById("mfr_id") != null)
    {
      url = url + "&mfr_id=" + document.getElementById("mfr_id").value;
    }
  if(document.getElementById("emod_id") != null)
    {
      url = url + "&emod_id=" + document.getElementById("emod_id").value;
    }
  url = "ajax-php/getAddEquipForm.php?" + url;
  doHTTPrequest(url, handleAddEquipRequest);
}

function handleAddEquipRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('addEquipForm').innerHTML = response;
  }
}

// Return Equipment Form

function updateReturnEquipForm()
{
  var url = "";
  url = "ajax-php/getReturnEquipForm.php?EMTid=" + document.getElementById("EMTid").value;
  doHTTPrequest(url, handleReturnEquipRequest);
}

function handleReturnEquipRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('returnEquipForm').innerHTML = response;
  }
}