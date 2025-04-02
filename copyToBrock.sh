#!/usr/bin/env bash
scp -i /home/brock/brock__scp_key /var/www/html/tlapse/*.jpg brock@192.168.1.14:/archive/cams/sky/ 
