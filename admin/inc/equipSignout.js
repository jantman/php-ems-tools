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

