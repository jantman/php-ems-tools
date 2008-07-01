# makefile to update CVS and do anything else needed
# Time-stamp: "2008-07-01 16:54:43 jantman"
# $Id$

ifdef LOG
        LOGSTR = $(LOG)
else
        LOGSTR = just working on things
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
	cvs import -m "$(LOGSTR)" php-ems-tools-trunk jantman "$(TAGSTR)"
