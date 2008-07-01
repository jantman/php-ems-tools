# makefile to update CVS and do anything else needed
# Time-stamp: "2008-07-01 16:51:34 jantman"
# $Id$

ifdef LOG
        LOGSTR = $(LOG)
else
        LOGSTR = just working on things
endif

cvsupdate:
	-rm *~
	-cd inc/ && rm *~
	-cd config/ && rm *~
	cvs import -m "$(LOGSTR)" php-ems-tools-trunk jantman r_3_1_dev
