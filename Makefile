# makefile to update SVN and do anything else needed
#
# +--------------------------------------------------------------------------------------------------------+
# | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
# +--------------------------------------------------------------------------------------------------------+
# | Time-stamp: "2010-08-26 08:42:54 jantman"                                                              |
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
# | $LastChangedRevision:: 68                                                                            $ |
# | $HeadURL:: http://svn.jasonantman.com/newcall/Makefile                                               $ |
# +--------------------------------------------------------------------------------------------------------+
#

.PHONY: commit docs

docs:
	rm -Rf ~/rmt/central-home/temp/rm_me/jsdoc/*
	mkdir -p temp/js
	bin/js2phpdoc.php js/ temp/js/
	cp -r inc temp/
	cp *.php temp/
	phpdoc -c docs/default.ini
	cp -R docs/* ~/rmt/central-home/temp/rm_me/jsdoc/
	rm -Rf temp

commit:
	-rm *~
	-cd bin/ && rm *~
	-cd config/ && rm *~
	-cd css/ && rm *~
	-cd inc/ && rm *~
	-cd js/ && rm *~
	svn commit

