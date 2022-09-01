#!/bin/bash

source test-inc.sh

tags=$(git tag)
for release in "${releases[@]}"; do
	latestrelease=$(git describe master --match "v$release.*" --abbrev=0)
	if [ $? == 0 ]; then
		echo $latestrelease
	fi
done