<?php
//PCRpro Stats Manager for MPAC
//(c) 2006 Jason Antman. 
require_once 'HTML/QuickForm.php';

$form = new HTML_QuickForm('firstForm');


$form->addElement('radio','action','Action:','Monthly Member Stats','monthMemb', array('id' => 'action'));
$form->addElement('radio','action',null,'Yearly Member Stats','yearMemb', array('id' => 'action'));
$form->addElement('radio','action',null,'Monthly Call Stats (Simple)','monthCall', array('id' => 'action'));
$form->addElement('radio','action',null,'Yearly Call Stats (Simple)','yearCall', array('id' => 'action'));
$form->addElement('radio','action',null,'Calls Summary by Member','membCall', array('id' => 'action'));

//$options = array('language' => 'en','format' => 'F');//options for the date objects 
//$form->addElement('date', 'month', 'Time Period: ', $options, array('id' => 'month'));

$s =& $form->createElement('select','month','Month: ');
$opts = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
$s->loadArray($opts,date("n"));
$form->addElement($s);


$options = array('language' => 'en','format' => 'Y','minYear' => 2006,'maxYear' => 2030);//options for the date objects 
$foo =& $form->createElement('select', 'year', 'Year: ');
$opts = array();
for($i = 2006; $i < 2030; $i++)
{
    $opts[$i] = $i;
}
$foo->loadArray($opts, date("Y"));
$form->addElement($foo);

$form->addElement('text', 'emtid', 'ID#', array('id'=>'emtid'));

//$form->addElement('static', 'Populate', 'Populate', '<a href="javascript:redirect()">Populate</a>');

$form->addElement('submit', 'btnSubmit', 'GO!');
$form->addElement('reset', 'btnReset', 'Reset');

$defaults['year'] = date('Y'); 
$defaults['month'] = date('n');
$form->setDefaults($defaults);  

if ($form->validate()) 
{
  	 $form->process('processForm', false);   
}

function processForm($formItems)
{
	$loc = '';
   $action = $formItems['action'];
   $month = $formItems['month'];
   $year = $formItems['year'];
   $emtid = $formItems['emtid'];
   if ($action == 'monthMemb')
	{
		$loc = "membStats.php?month=".$month."&year=".$year;
	}
	else if ($action == 'yearMemb')
	{
		$loc = "membStats.php?year=".$year;
	}
	else if ($action == 'monthCall')
	{
		$loc = "simpleMonthly.php?month=".$month."&year=".$year;
	}
	else if ($action == 'yearCall')
	{
		$loc = "simpleMonthly.php?year=".$year;
	}
	else if ($action == 'membCall')
	{
		$loc = "membCalls.php?EMTid=".$emtid;
	} 
	header('location: '.$loc);  
}
// Output the form






?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
  <title>PCRpro Stats for MPAC</title>
</head><body>
<script type="javascript" src="index.js"> </script>
<h1>PCRpro Stats for MPAC</h1>

<?php
$form->display();
?>
</body>
</html>
