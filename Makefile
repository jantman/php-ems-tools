# makefile to update CVS and do anything else needed
# Time-stamp: "2008-07-01 20:46:50 jantman"
# $Id$

ifdef LOG
        LOGSTR = $(LOG)
else
        LOGSTR = just working on things (issue: 148)
endif

ifdef TAG
	TAGSTR = $(TAG)
else
	TAGSTR = main
endif

cvsupdate:
	-rm *~
	-cd inc/ && rm *~
	-cd config/ && rm *~
	cvs import -m "$(LOGSTR) (issue: 148)" php-ems-tools-trunk jantman "$(TAGSTR)"
