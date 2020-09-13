#!/bin/bash
now=$(date +%d-%b-%H-%Y) 
echo $now
tar -czf ./bak2/$now.tar.gz *.php

