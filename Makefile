# makefile to update CVS and do anything else needed
# Time-stamp: "2008-11-02 23:48:30 jantman"
# $Id$

ifdef LOGSTR
        LOG = $(LOGSTR)
else
        LOG = just working on things (issue: 161)
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
	cvs import -m "$(LOG)" php-ems-tools-trunk jantman "$(TAGSTR)"
