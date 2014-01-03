#!/usr/bin/env python
# Python script to set SVN keyword substitution properties on files

# +--------------------------------------------------------------------------------------------------------+
# | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
# +--------------------------------------------------------------------------------------------------------+
# | Time-stamp: "2009-12-21 11:16:48 jantman"                                                              |
# +--------------------------------------------------------------------------------------------------------+
# | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
# |                                                                                                        |
# | This program is free software; you can redistribute it and/or modify                                   |
# | it under the terms of the GNU General Public License as published by                                   |
# | the Free Software Foundation; either version 3 of the License, or                                      |
# | (at your option) any later version.                                                                    |
# |                                                                                                        |
# | This program is distributed in the hope that it will be useful,                                        |
# | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
# | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
# | GNU General Public License for more details.                                                           |
# |                                                                                                        |
# | You should have received a copy of the GNU General Public License                                      |
# | along with this program; if not, write to:                                                             |
# |                                                                                                        |
# | Free Software Foundation, Inc.                                                                         |
# | 59 Temple Place - Suite 330                                                                            |
# | Boston, MA 02111-1307, USA.                                                                            |
# +--------------------------------------------------------------------------------------------------------+
# |Please use the above URL for bug reports and feature/support requests.                                  |
# +--------------------------------------------------------------------------------------------------------+
# | Authors: Jason Antman <jason@jasonantman.com>                                                          |
# +--------------------------------------------------------------------------------------------------------+
# | $LastChangedRevision:: 12                                                                            $ |
# | $HeadURL:: http://svn.jasonantman.com/newcall/makeprops.py                                           $ |
# +--------------------------------------------------------------------------------------------------------+
#

# bottom line - this runs a given command on every regular file (and not in .svn/) under a path

import os, os.path, sys

CMD = 'svn propset svn:keywords "Date Revision Author HeadURL Id" ' # will be postfixed by file name

# propset all files in path, recurse
def doDir(path):
    # list all files in directory
    for f in os.listdir(path):
        # skip over '.', '..', and '.svn'
        if f == "." or f == ".." or f == ".svn":
            continue
        # either do the file or recurse
        if os.path.isfile(os.path.join(path, f)):
            sys.stderr.write("Setting props on " + os.path.join(path, f) + "... ")
            os.system(CMD + os.path.join(path, f))
            sys.stderr.write(" OK.\n")
        elif os.path.isdir(os.path.join(path, f)):
            # recurse
            doDir(os.path.join(path, f))
# RUN THE FUNCTION:
doDir("./")
