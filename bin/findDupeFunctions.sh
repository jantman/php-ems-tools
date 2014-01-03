#!/bin/bash

grep -E "^[:space:]*function [A-Za-z0-9_-]+[:space:]*\(" js/*.js inc/*.php inc/*.js *.php *.js bin/*.php | awk -F : '{print $NF}' | sort | uniq -cd
