#!/bin/bash

# This is the PHP EMS Tools installation script
# http://www.php-ems-tools.com

# Time-stamp: "2006-11-15 01:10:37 jantman"
# Version 1

# NOTICE:
# This assumes that you have the BASH shell installed
# and that it resides at /bin/bash
# if not, please modify the path in line 1 to that of BASH

# THIS SCRIPT MUST BE RUN AS ROOT.

# VARIABLES TO CUSTOMIZE:

#  defines the name of the database used by PHP EMS Tools
# there should be no reason to change this
dbName="php-ems-tools"

# these specify the member information for the administrator (you).
# please see the roster documentation for more information
# you MUST change these values!!!!
id="EDITME"
LastName="YourLastName"
FirstName="YourFirstName"
password="YourPassword" # this is a cleartext password. be sure to obscure this after running this file.
#this next line should be the name of one of the member types you define in custom.php
status="Senior"
shownAs="YourName" #how you will be shown on the schedule, is this option enabled in custom.php

#
# DO NOT CHANGE ANYTHING BELOW THIS LINE
#

# BEGIN INSTALL SCRIPT

# Check if the script is being run as root exit if it is not.
if [ "$UID" -ne "0" ]
then
  echo "[ERROR] This script must be run as root"
  exit 1
fi

if [ -d HTML ]

then

    echo "HTML Directory exists. (That's good.)"

else

    echo "ERROR: The HTML directory or link does not exist. You MUST create it before using php-ems-tools. Please refer to the documentation."

fi



# create the database, create the tables, set permissions
mysqladmin CREATE $dbName
mysql $dbName < php-ems-tools.sql

echo " FLUSH PRIVILEGES; USE $dbName; GRANT ALL PRIVILEGES ON * TO ''@'localhost'; FLUSH PRIVILEGES;" | mysql --user=root

#add administrative user

rightsLevel=2

hash=`./makeMD5.php $id`

echo 'USE '$dbName'; INSERT INTO roster SET EMTid="'$id'",LastName="'$LastName'",FirstName="'$FirstName'",password="'$password'",rightsLevel="'$rightsLevel'",status="'$status'",shownAs="'$shownAs'",pwdMD5="'$hash'";' | mysql

# END INSTALL SCRIPT