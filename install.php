<?php
// install.php
//
// Command-Line installation script. Handles all database stuff and
//   checks for dependencies. 
// FOR UNIX OR LINUX ONLY!
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
//      $Id$

// this file will import the user's customization
require_once('./config/config.php');

// import ALL of the config files for checking
require_once('./config/rigCheckData.php');
require_once('./config/rosterConfig.php');
require_once('./config/scheduleConfig.php');

require_once('./admin/checkCustomConfig.php');

$dontMakeDB = false; // whether or not to make the DB.

// clear screen
for($temp = 0; $temp < 101; $temp++)
{
    fwrite(STDOUT, "\n");
}

// make SURE this script is running from CLI
if(php_sapi_name() != 'cli')
{
    die("You are using something other than the command-line interface. Exiting...");
}

fwrite(STDOUT, "PHP EMS Tools \n");
fwrite(STDOUT, "INSTALLATION SCRIPT \n");
fwrite(STDOUT, "------------------------------------------------------------- \n");
fwrite(STDOUT, "This script is for Linux/Unix/BSD systems ONLY! \n");
fwrite(STDOUT, "------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Please be WARNED that PHP EMS Tools is not designed to be used on the internet. To do so, you must be sure to configure additional security. At the minimum, use .htaccess files!\n");
fwrite(STDOUT, "Until we are done with configuration, this directory (and the MySQL database) should NOT be visible from the Internet.\n");

fwrite(STDOUT, "If nothing is visible to the Internet, or you understand and choose to disregard this warning, please type yes. Otherwise, type no.\n");
fwrite(STDOUT, "yes or no?\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Ok, good. We can continue.\n");
}
else
{
    fwrite(STDOUT, "Security is a major concern, especially with a database program such as this.\n");
    fwrite(STDOUT, "Please think about the possible risks, and develop a solution.\n");
    fwrite(STDOUT, "I'm exiting now...\n");
    die();
}
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "PHP EMS Tools (php-ems-tools) is licensed under the GNU General Public License (GPL). A copy of the license is included as the LICENSE.txt file in this directory.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "This is *free* software, both in terms of price and in terms of *freedom*. You have the freedom to redistribute, modify, and use the code in any way that you choose.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "That being said, I do have the following requests (and only requests):\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "1) If you are intending to use PHP EMS Tools for your EMS organization, please fill out the user survey at http://www.php-ems-tools.com This information is for my research purposes only, as I would like to keep the package up-to-date for the needs of the users. Your information will be kept completely private. I will only use it for planning future features, and you will not be contacted via email more than once unless you subscribe to a mailing list.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "2) You are free to modify the code however you choose, but please include attribution to the original project (PHP EMS Tools available at http://www.php-ems-tools.com).\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "3) If you choose to modify the code, I ask that you e-mail me with your additions, or a brief synopsis of what they do, so that I can incorporate the features into the next release.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "4) I REQUEST that you do NOT redistribute this code unless it can not be downloaded from php-ems-tools.com or SourceForge. This is an effort to be sure that everyone gets the most recent version.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "5) If you feel that the software has helped your organization, recognize the savings that you have gained by using free software. I mean this both in terms of cost savings from cost-free software, and the merits of Free/Open Source Software - specifically, the ability to modify the software to your liking, and the avoidance of vendor lock-in with proprietary software. If you would like to help my development efforts, I accept donations to support the costs of running my web sites and development machines. Please contact me at jason@php-ems-tools.com or visit www.php-ems-tools.com\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "6) I highly encourage all users to consider making a donation in return for the software, in any amount, to:\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "   Midland Park Volunteer Ambulance Corps\n");
fwrite(STDOUT, "   PO Box 38\n");
fwrite(STDOUT, "   Midland Park, NJ\n");
fwrite(STDOUT, "   07432\n");
fwrite(STDOUT, "   http://www.midlandparkambulance.com\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "   Checks Payable To: Midland Park Ambulance Corps\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "   This donation is tax deductable (MPAC is a 501(c)(3) Non-Profit).\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "\n");

fwrite(STDOUT, "Do you accept this license? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Thank You.\n");
}
else
{
    fwrite(STDOUT, "You may NOT use this software until you accept the license terms!\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "NOTICE: \n");
fwrite(STDOUT, "This piece of free, open-source software relies on user feedback for development. It is highly important that if you have ANY problems or suggestions, you report them using the Bug Report link at www.php-ems-tools.com\n");
fwrite(STDOUT, "\n\n");

fwrite(STDOUT, "Please press enter to continue with the installation.\n");
$command = fgets(STDIN);
if($command != "")
{
    fwrite(STDOUT, "Moving on...\n");
}
else
{
    fwrite(STDOUT, "Sorry!\n");
    die();
}


fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Before we do anything, let's check to see that config.php is configured correctly.\n");
fwrite(STDOUT, "You can run the config.php check from the command line or via the web, using admin/checkCustomConfig.php\n");
fwrite(STDOUT, "It is recommended that you do this every time you change config.php\n");
fwrite(STDOUT, "This script only checks for MAJOR errors that will being everything to a grinding halt. Minor errors may effect functionality but slip by me right now...\n\n\n\n");
fwrite(STDOUT, "Checking config.php...\n");

$checked = checkCustom(); // run checkCustom from ./admin/checkCustomConfig.php

if($checked == true)
{
    fwrite(STDOUT, "Successful. I was unable to find any MAJOR errors.\n");
}
else
{
    fwrite(STDOUT, "I', sorry, but the above errors are fatal, and I cannot continue.\n Please corrent then and run me again.\n");
    die();
}
fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
// check which OS we are running
$uname = posix_uname();
$sysname = $uname['sysname'];
if((stristr($sysname, "win") == false) && (stristr($sysname, "mac") == false) && (stristr($sysname, "darwin") == false))
{
    fwrite(STDOUT, "Are you running this on a Unix, Linux, BSD, or orher *nix operating system? yes or no. \n");
    $command = trim(fgets(STDIN));
    if($command != "yes")
    {
	die("This script is only for *nix machines. Sorry. Please contact the developer for further info.\n");
    }   
}
else
{
    // Windoze or Mac. die.
    fwrite(STDOUT, "This script is not for Windows or Mac. Sorry. \n Please contact the developer for more information.\n");
    fwrite(STDOUT, "The installation procedure is actually quite simple. Please read through the documentation (either included or online at our web site). It generally just consists of setting up a database in MySQL and setting up the tables, which can be done easily.\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

// are we running as root?
fwrite(STDOUT, "This script is about to see if you are running it as root. \n");
fwrite(STDOUT, "If you have already setup the database, you can simply type YES when prompted. \n");
fwrite(STDOUT, "Root permissions are only needed to initially setup the database.\n \n");

$euid = posix_geteuid();
if($euid != 0)
{
    fwrite(STDOUT, "Your user id (euid) does not appear to be 0, that of root. This script must be run as root. \n");
    fwrite(STDOUT, "If you are ABSOLUTELY sure that you ARE root (or you have root set to a uid other than 0???) or have already intialized the database and granted privileges to the user that you're running as, \n");
    fwrite(STDOUT, "Type YES to continue, otherwise press any key. \n");
    $command = trim(fgets(STDIN));
    if($command != "YES")
    {
	die("Please su to root and run this script as root.");
    }
}
else
{
    fwrite(STDOUT, "Done. \n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

// has config.php been edited?
fwrite(STDOUT, "Before we go any farther, please be sure that you have edited config.php to the correct values, especially the dbName value and your server information. \n\n");
fwrite(STDOUT, "Have you done so? yes or no.\n");
$command = trim(fgets(STDIN));
if($command != "yes")
{
    die("Please edit config.php and then re-run this script.");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Good. \n\n");
fwrite(STDOUT, "Now let's check your system for some requirements... \n\n");
fwrite(STDOUT, "Obviously, you have PHP installed. The other requirements are: \n");
fwrite(STDOUT, "Web Server (Apache recommended). \n");
fwrite(STDOUT, "MySQL (with command-line client) and anonymous access for 'localhost'\n");
fwrite(STDOUT, "PEAR modules for PHP \n");
fwrite(STDOUT, "The HTML_QuickForm module for PEAR \n\n\n\n");

fwrite(STDOUT, "Do you have a web server installed? yes or no.\n");
$command = trim(fgets(STDIN));
if($command != "yes")
{
    die("Please install a web server and all other requirements in the documentation and then re-run this script. Apache is recommended.");
}

// ask if it's apache
fwrite(STDOUT, "Is your web server Apache? yes or no. \n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Good choice. \n");
    fwrite(STDOUT, "Let's see if it's running... (we'll do ps -A | grep httpd)\n");
    // a bit of a kludge...
    $httpd = shell_exec("ps -A | grep httpd");
    if(stristr($httpd, "httpd") != false)
    {
	fwrite(STDOUT, "Good, Apache is running. \n");
    }
    else
    {
	fwrite(STDOUT, "Apache is not running. Please correct this problem before continuing. \n");
	die();
    }
}
else
{
    fwrite(STDOUT, "I only know how to test for Apache.\n I will take your word that you have installed and properly configured your webserver. \n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

// is mysql installed?
fwrite(STDOUT, "Do you have MySQL installed and running? yes or no. \n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Let's see if it's running... \n");
    $mysqld = shell_exec("ps -A | grep mysqld");
    if(stristr($mysqld, "mysqld") != false)
    {
	fwrite(STDOUT, "Good, MySQL is running. \n");
    }
    else
    {
	fwrite(STDOUT, "MySQL. is not running. Please correct this problem before continuing. \n");
	die();
    }
    fwrite(STDOUT, "Now, let's see if we have a mysql client available... \n");
    $mysql = shell_exec("which mysql");
    if(stristr($mysql, "mysql") != false)
    {
	fwrite(STDOUT, "Good, the MySQL cilent is at ".$mysql.". \n");
    }
    else
    {
	fwrite(STDOUT, "The MySQL client is not available. Please correct this problem before continuing. \n");
	die();
    }
    fwrite(STDOUT, "Let's see if the mysqladmin program is available... \n");
    $mysqladmin = shell_exec("which mysqladmin");
    if(stristr($mysqladmin, "mysqladmin") != false)
    {
	fwrite(STDOUT, "Good, the MySQL Admin program is at ".$mysqladmin.". \n");
    }
    else
    {
	fwrite(STDOUT, "The mysqladmin program is not available.\n Have you already initialized the database? YES or no. \n");
	$command = trim(fgets(STDIN));
	if($command != "YES")
	{
	    $dontMakeDB = true;
	    fwrite(STDOUT, "Ok, we will proceed but not initialize the database. \n");
	}
	else
	{
	    fwrite(STDOUT, "To initialize the database, I need access to the mysqladmin program. Please correct the problem and then start this script again. \n");
	    die();
	}
    }
}
else
{
    fwrite(STDOUT, "Please install MySQL and have it running before continuing. \n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

global $dbName;
// initialize database
fwrite(STDOUT, "Shall we create the database ('".$dbName."') now? \n");
fwrite(STDOUT, "Only type no if you have already done so, and have setup permissions for anonymous at localhost.\n");
fwrite(STDOUT, "yes or no. \n");
$command = trim(fgets(STDIN));
if($command == "yes" || ! $dontMakeDB)
{
    // initialize db as in install-db.sh
    // but don't put anything into it yet
    setupDatabase();
}
else
{
    fwrite(STDOUT, "We will assume you have already created the database called '".$dbName."'.");
}

// try to connect - anonymous at localhost
fwrite(STDOUT, "Trying to connect to MySQL as anonymous at localhost... \n");
$conn = mysql_connect();
if(! $conn)
{
    fwrite(STDOUT, "I'm sorry, but I can't connect to the MySQL server. Perhaps you haven't enabled anonymous permissions for localhost. Please correct this problem and then run this script again... \n");
    die();
}
fwrite(STDOUT, "Successful.\n");

fwrite(STDOUT, "I will now try and select the database called '".$dbName."'.\n");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database! Perhaps you haven't setup permissions.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Great! I was able to select the database.\n");

fwrite(STDOUT, "Now, let's see if there's anything in it...\n");
$query = "SHOW TABLES LIKE 'schedule\_%';";
$failed = false;
$result = mysql_query($query) or ($failed = true);
$num = mysql_num_rows();

if($failed || ($num < 1))
{
    fwrite(STDOUT, "I could not find a table called 'schedule_template', which would be installed if you had setup the database.\n");
    fwrite(STDOUT, "This means that you have not setup the tables yet.\n");
    $setupTables = true;
}
elseif($num == 1)
{
    fwrite(STDOUT, "It looks to me like you have setup the tables already, but never viewed the schedule.\n");
    fwrite(STDOUT, "You should point a web browser to the schedule and view it, to populate a table for this month.\n");
    $setupTables = false;
}
else // we have month tables
{
    fwrite(STDOUT, "I found schedule tables setup for at least one month.\n");
    fwrite(STDOUT, "Since you have a current installation, you should not be running this script.\n");
    fwrite(STDOUT, "I'll exit now...\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

if($setupTables)
{
    fwrite(STDOUT, "\n");
    fwrite(STDOUT, "Ok, now I'll setup the tables for PHP EMS Tools.\n");
    doTableSetup();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "\nOk. We're done with the database work.\n");

// ask for web root directory, or directory we're installing in

fwrite(STDOUT, "Let's see what the current directory is...\n");
$cwd = posix_getcwd();

if($cwd == "/srv/www/htdocs")
{
    fwrite(STDOUT, "Good. The current working directory is ".$cwd."\n");
    fwrite(STDOUT, "This is the default web root directory for Apache.\n");
    fwrite(STDOUT, "If this is not the directory you are installing into, please exit me and run me where you are installing php-ems-tools.\n");
}
else
{
    fwrite(STDOUT, "The current directory is not /srv/www/htdocs, which is the Apache default directory.\n");
    fwrite(STDOUT, "I'm seeing that the current directory is ".$cwd.".\n");
    fwrite(STDOUT, "This should be the directory that you are installing php-ems-tools into, and should be served by your web server.\n");
    fwrite(STDOUT, "Is this correct? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Ok, I will use this as the installation directory.\n");
    }
    else
    {
	fwrite(STDOUT, "Please run this program in the directory that you installed php-ems-tools in, and make sure that it is served by your webserver.\n");
	fwrite(STDOUT, "I am exiting now. Please run me again when you have completed the above instructions.\n");
	die();
    }
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Now we'll check for some requirements...\n");

fwrite(STDOUT, "Do you have PEAR Installed? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Ok. I don't know how to check for it, so I'll take your word.\n");
}
else
{
    fwrite(STDOUT, "Please install PEAR and HTML_QuickForm before running me.\n");
    fwrite(STDOUT, "I'm exiting now...\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "I'll check to see if the HTML QuickForm package is available.\n");
fwrite(STDOUT, "PHP EMS Tools is coded to require the HTML directory in the current working directory.\n");
fwrite(STDOUT, "This should work with either an actual directory or a link.\n");


fwrite(STDOUT, "Checking for PEAR directory...\n");
if(file_exists("PEAR"))
{
    fwrite(STDOUT, "Ok, the PEAR directory seems to be at ".$cwd."/PEAR\n");
}
else
{
    fwrite(STDOUT, "I cannot find a PEAR directory at ".$cwd."/PEAR\n");
    fwrite(STDOUT, "This may be a problem in the future, but I will assume that there is a reason why I can't find it there.\n");
}

fwrite(STDOUT, "Checking for HTML directory...\n");

if(file_exists("HTML"))
{
    fwrite(STDOUT, "Ok, the HTML directory seems to be at ".$cwd."/HTML\n");
}
else
{
    fwrite(STDOUT, "I cannot find a HTML directory at ".$cwd."/HTML\n");
    fwrite(STDOUT, "PHP EMS Tools must have this directory in order to work.\n");
    fwrite(STDOUT, "I'm exiting now. Please fix this problem and run me again.\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

if(is_dir("HTML"))
{
    fwrite(STDOUT, "It also seems to be an actual directory. Good.\n");
}
elseif(is_link("HTML"))
{
    fwrite(STDOUT, "It seems to be a link. This may or may not work. If you experience problems, please make a hard link.\n");
}
else
{
    fwrite(STDOUT, "It seems to be neither a directory nor a link. This may be a problem...\n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Now I'll check for the QuickForm.php file...");
if(file_exists('HTML/QuickForm.php'))
{
    fwrite(STDOUT, "Good, I found it at ".$cwd."/HTML/QuickForm.php\n");
    require_once 'HTML/QuickForm.php';
}
else
{
    fwrite(STDOUT, "I could not find the QuickForm file at ".$cwd."/HTML/QuickForm.php\n");
    fwrite(STDOUT, "This will prevent PHP EMS Tools from running, as this is the location which it is told to find the file.\n");
    fwrite(STDOUT, "I am exiting now...\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Checking for the file HTML/QuickForm/element.php...\n");
if(file_exists('HTML/QuickForm/element.php'))
{
    fwrite(STDOUT, "Good, I found it at ".$cwd."/HTML/QuickForm/element.php\n");
    require_once 'HTML/QuickForm/element.php';
}
else
{
    fwrite(STDOUT, "I could not find the element.php file at ".$cwd."/HTML/QuickForm/element.php\n");
    fwrite(STDOUT, "This will prevent PHP EMS Tools from running, as this is the location which it is told to find the file.\n");
    fwrite(STDOUT, "I am exiting now...\n");
    die();
}

fwrite(STDOUT, "\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "I'll try and create a form with QuickForm to make sure that it's working...\n");

$form = new HTML_QuickForm('firstForm') or die("I can't create a form. PHP EMS Tools will not run until this problem is corrected.");

if($form)
{
    fwrite(STDOUT, "Success!\n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Congratulations, configuration and installation seems to be complete!\n");
fwrite(STDOUT, "\n");

fwrite(STDOUT, "Now, I'll have you test the configration.\n");

fwrite(STDOUT, "You will need to know the URL or IP address to this server on your network.\n");
fwrite(STDOUT, "Unfortunately, I don't know how to detect that.\n");
fwrite(STDOUT, "What is the URL to your server on the local network (i.e. http://192.168.0.6 or http://hostname.domain.com) ?\n");
$URL = trim(fgets(STDIN));

// ask for the directory on the server - i.e. / or /php-ems-tools/
if($cwd == "/srv/www/htdocs")
{
    fwrite(STDOUT, "Since your working directory is ".$cwd.", I will assume that this is the top-level web directory.\n");
    fwrite(STDOUT, "Therefore, the URL for php-ems-tools should be ".$URL."/ \n");
    fwrite(STDOUT, "Is this correct? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Good.\n");
	$dir = "/";
    }
    else
    {
	fwrite(STDOUT, "Ok. That's a bit strange, but I should be able to handle it.\n");
	fwrite(STDOUT, "What comes after ".$URL." to view this directory from the web/LAN?\n");
	$dir = trim(fgets(STDIN));
	fwrite(STDOUT, "Ok, so the URL to PHP EMS Tools should be ".$URL.$dir."\n");
    }
}
else
{
    fwrite(STDOUT, "Ok, we need to know the full URL to view PHP EMS Tools from the web/LAN.");
    fwrite(STDOUT, "Is it ".$URL."/ ? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Good. That means the full URL should be ".$URL."/ \n");
	$dir = "/";
    }
    else
    {
	fwrite(STDOUT, "Please enter the part of the URL after ".$URL." \n");
	$dir = trim(fgets(STDIN));
	fwrite(STDOUT, "Ok, Our full URL should be ".$URL.$dir."\n");
    }
}

mysql_close($conn);

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Do you have a web browser (lynx is OK) on this machine? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Good. Now, you can confirm your installation by pointing your browser to:\n");
    fwrite(STDOUT, "http://127.0.0.1".$dir."\n");
    fwrite(STDOUT, "Please try this.\n");
    fwrite(STDOUT, "Are you able to see the PHP EMS Tools main page? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Good. Now we'll try it from another machine.\n");
	fwrite(STDOUT, "Please go to another machine on your network, and point your browser to:\n");
	fwrite(STDOUT, $URL.$dir."\n");
	fwrite(STDOUT, "Does it work? yes or no\n");
	$command2 = trim(fgets(STDIN));
	if($command2 == "yes")
	{
	    fwrite(STDOUT, "GREAT! We're done. Thanks for using PHP EMS Tools.\n");
	    fwrite(STDOUT, "\n");
	    finishText();
	    exit(0);
	}

    }
}
else
{
    fwrite(STDOUT, "That's ok. Please try using another machine that should be able to view documents served by the web server on this machine. You will want to point your browser to this directory (php-ems-tools). It seems from your configuration file that the full URL should be ".$URL.$dir." If this is not right, please correct your config.php file.\n\n\n");
    fwrite(STDOUT, "Please take a moment to try to view this URL. If it doesn't seem to load correctly, I will give you some simple troubleshooting hints. If none of them work, please visit the online documentation at http://www.php-ems-tools.com and also attempt troubleshooting specific to your web server and system.\n");
    fwrite(STDOUT, "Can you view this page? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command = "yes")
    {
	fwrite(STDOUT, "GREAT! Installation has finished successfully...");
	exit(0);
    }
    else
    {
	fwrite(STDOUT, "Ok. Well, troubleshooting time!\n");
    }
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, " \n");
fwrite(STDOUT, "I'm sorry that you are having some problems.\n");
fwrite(STDOUT, "First, please make sure that Apache (or whatever web server you're using) is properly configured and serving pages.\n");
fwrite(STDOUT, "If that was not the problem, you have a few choices for support:\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "You can look at our web page at\n");
fwrite(STDOUT, "http://www.php-ems-tools.com\n");
fwrite(STDOUT, "for documentation and also a list of previous bugs and support requests.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "As a last resort, feel free to e-mail the developer at:\n");
fwrite(STDOUT, "jason@php-ems-tools.com\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "These options are detailed in the documentation at www.php-ems-tools.com\n");
fwrite(STDOUT, "\n");

fwrite(STDOUT, "Please make note of these options, and press ENTER to continue...\n");
$command = trim(fgets(STDIN));
exit(0);

function doTableSetup()
{
    global $conn;
    require_once('./admin/dbTableSchema.php'); // db table schema
    // this file has $dbTableSchemaA which is the array that holds all queries to setup the table

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    // begin table creation loop
    foreach($dbTableSchemaA as $val)
    {
	$tblName = $val['name'];
	$tblDesc = $val['description'];
	$query = $val['query'];
	fwrite(STDOUT, "Creating the ".$tblName." table...\n");
	mysql_query($query) or die("I'm sorry, but there was an error in creating the ".$tblName." table: ".mysql_error());
	fwrite(STDOUT, "Done.\n");
	fwrite(STDOUT, "Checking that it worked...\n");
	$result = mysql_query("SHOW TABLES LIKE ".$tblName.";");
	if(mysql_num_rows($result) > 0)
	{
	    fwrite(STDOUT, "Ok.\n");
	}
	else
	{
	    fwrite(STDOUT, "I'm sorry, but the table isn't there. I'll continue, but you will not be able to access some of the features.\n");
	}
    }
    // end table creation loop

    fwrite(STDOUT, "Table creation complete.\n");
    fwrite(STDOUT, "\n");

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "Now, we have to setup an initial administrative user for you, so that you can make changes and modify other users.\n");
    fwrite(STDOUT, "We will give you a rightsLevel of 2 - the highest.\n");

    $rightsLevel = 2;

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "Now, I will ask you for some information to setup your master administrator user...\n");
    fwrite(STDOUT, "If you want to stop this program at any point, you can type EXIT when prompted for input.\n");
    fwrite(STDOUT, "\n\n");

    // EMTid
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your ID# for the system...\n");
	$EMTid = trim(fgets(STDIN));
	if($EMTid == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$EMTid."' as your ID. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    // LastName
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your Last Name for the system...\n");
	$LastName = trim(fgets(STDIN));
	if($LastName == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$LastName."' as your Last Name. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    // FirstName
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your First Name for the system...\n");
	$FirstName = trim(fgets(STDIN));
	if($FirstName == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$FirstName."' as your First Name. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    // password
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your password for the system...\n");
	fwrite(STDOUT, "NOTICE: this is stored in plain text in the database!\n");
	$password = trim(fgets(STDIN));
	if($password == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$password."' as your password. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }
    $pwdMD5 = md5($password);

    // status
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "You can now select your status/member type from the options set in config.php.\n");
	fwrite(STDOUT, "Please type one of the following numbers...\n");

	global $memberTypes;
	$types = array();
	for($i = 0; $i < count($memberTypes); $i++)
	{
	    $types[$i] = $memberTypes[$i]['name'];
	}

	for($i = 0; $i < count($types); $i++)
	{
	    fwrite(STDOUT, $i.")\t\t".$types[$i]."\n");
	}
	$status = trim(fgets(STDIN));
	if($status == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	$status = $types[$status];
	fwrite(STDOUT, "You entered '".$status."' as your status. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    fwrite(STDOUT, "Ok, we're done collecting your information. You can enter the rest with the web interface.\n");

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "Our MySQL query is as follows:\n");

    $userInfo = 'INSERT INTO roster SET EMTid="'.$EMTid.'",LastName="'.$LastName.'",FirstName="'.$FirstName.'",password="'.$password.'",rightsLevel=2,status="'.$status.'",pwdMD5="'.$pwdMD5.'";';

    fwrite(STDOUT, $userInfo."\n");
    fwrite(STDOUT, "If all of the information you entered is correct, we can continue.\n");
    fwrite(STDOUT, "Or we can exit...\n");
    fwrite(STDOUT, "Continue? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command != "yes")
    {
	die("Exiting...");
    }
    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "Ok, adding your user to the roster...\n");
    mysql_query($userInfo)or die("I'm sorry, but there was an error in creating the table schedule_change_template: ".mysql_error());
    fwrite(STDOUT, "Done.\n");

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "Confirming that it was input correctly...\n");
    $result = mysql_query('SELECT * FROM roster WHERE EMTid="'.$EMTid.'";');
    if(mysql_num_rows($result) < 1)
    {
	fwrite(STDOUT, "FAILED.\n");
	fwrite(STDOUT, "I don't know how to recover from this problem. Exiting...\n");
	die();
    }
    fwrite(STDOUT, "Successful.\n");

    fwrite(STDOUT, "Tables and user data successfully added.\n");

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");


}

function setupDatabase()
{
    global $conn;
    global $dbName;
    $grants = "CREATE, DELETE, INSERT, SHOW DATABASES, SELECT, UPDATE";
    $query = "FLUSH PRIVILEGES; USE \`".$dbName."\`; GRANT ".$grants." ON \`".$dbName."\`.* TO ''@'localhost'; FLUSH PRIVILEGES;";

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "Creating the database using the command-line utilities through a shell escape...\n");
    fwrite(STDOUT, "sending 'mysqladmin CREATE ".$dbName."'...\n");
    $res = shell_exec("mysqladmin CREATE ".$dbName);
    fwrite(STDOUT, "Done. \n");
    fwrite(STDOUT, "Testing...\n");

    mysql_select_db($dbName) or die("Database not created, or can't be selected. Exiting...");

    fwrite(STDOUT, "Ok, I was able to select the database.\n");

    fwrite(STDOUT, "PHP EMS Tools needs the following permissions for its' database: \n"); // finish this
    fwrite(STDOUT, $grants."\n");
    fwrite(STDOUT, "I will do this through the MySQL client program.\n");

    fwrite(STDOUT, "\n");
    fwrite(STDOUT, "Shall I set them up now? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Ok. Executing the program...\n");
	// PROBLEM WITH THIS
	$shellCmd = 'echo "'.$query.'" | mysql --user=root';
	$res = shell_exec($shellCmd);
	
	// DEBUG
	fwrite(STDOUT, "Shell Command:\n".$shellCmd."\n");
	fwrite(STDOUT, "Result:\n".$res."\n");
	// END DEBUG

	if(strstr($res, "ERROR"))
	{
	    fwrite(STDOUT, "I'm sorry, but for some reason, I could not set the permissions.\n The error message was:\n".$res);
	}
    }
    else
    {
	fwrite(STDOUT, "Ok, I won't do that.\n");
	fwrite(STDOUT, "Please be aware that PHP EMS Tools will NOT work until these permissions are set.\n");
    }

    fwrite(STDOUT, "Database creation complete.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
}

?>