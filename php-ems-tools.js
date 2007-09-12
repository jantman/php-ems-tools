// JavaScript include file for PHP EMS Tools
//
// Time-stamp: "2006-11-24 20:22:57 jantman"
/*
function signOn(int year, int month, int date, String shift, int slot)
{
    URL = "signOn.php?year="+year+"&month="+month+"&date="+date+"&shift="+shift+"&slot="+slot;
    eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=1,width=450,height=400,left = 312,top = 84');");
}
function dailyMessage(int year, int month, int date, String shift)
{
    URL = "signOn.php?year="+year+"&month="+month+"&date="+date+"&shift="+shift;
    eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=1,width=450,height=400,left = 312,top = 84');");
}
*/

function popUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=400,height=400');");
}

function rosterPopUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=0,width=430,height=600');");
}

function helpPopUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=500,height=600');");
}