#!/bin/bash

# Author: Sylvain SAUSSE
# Date: 5 September 2023
# License: GNU LGPL 3 license <http://www.gnu.org/licenses/>
# This script is meant to install dependencies for equal / simbiose app developpement and building within a docker container (for which equal image lacks some tools).

# (This script has been written for linux but can be adapted for Windows or MacOS easily.
# You will need a recent version of nodejs to compile the project and its dependencies.
# You can install nodejs 18.17 (LTS version at the time this script was written)
# with these commands
# -------------------------------------------------------------------------------------
# wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.5/install.sh | bash
# nvm install 18.17
# -------------------------------------------------------------------------------------


# zip and git are needed to compile both dependencies and application
apt update
apt install zip
apt install git

# Creating a folder to contain source code of the dependencies
rm -r dep-files
mkdir dep-files
cd dep-files

# Cloning dependencies from github
git clone https://github.com/yesbabylon/symbiose-ui.git
cd symbiose-ui
git clone https://github.com/cedricfrancoys/equal-ui.git

# Installing nodejs modules needed to compile symbiose-ui
npm install
# Compiling symbiose-ui
cd sb-shared-lib
sh build.sh
cd ..
cd equal-ui
# Installing nodejs modules needed to compile equal-ui
npm install
# Building equal-ui
sh build.sh
sh export.sh

# You should good to go ! you can compile apps with the build.sh file in the app directory
# You can remove the dep-files folder at any time

# REMINDER : To deploy the app in a Unix environment, you need to change the `base-href` argument to "/nameoftheapp/" in the build file of the app.