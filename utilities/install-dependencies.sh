#!/bin/bash
source ./utilities/progressbar.sh || exit 1

dependencies=(libfreetype6-dev libpng-dev libjpeg-dev libcurl4-gnutls-dev libyaml-dev libicu-dev libzip-dev unzip)


### START DO NOT EDIT AREA ###
echo "Installing dependencies..."
dependencies_count=${#dependencies[@]}
i=0
draw_progress_bar $i ${dependencies_count} "dependencies"
for dependency in ${dependencies[*]}; do
  apt-get install -y --no-install-recommends ${dependency} > /dev/null
  i=$((i+1))
  draw_progress_bar $i ${dependencies_count} "dependencies"
done
echo