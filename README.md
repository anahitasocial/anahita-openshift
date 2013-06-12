Read First - Known Issue
============
The openshift PHP cartirage enales the xdebug with maximum call stack of 100. This casues a crash in Anahita since in some places the call stack can grow more than 100.

Unfortunately openshift does not provide a way to disable xdebug so we are working on to see if Zend server cartirage can be used instead.

Install
============
Create an openshift App

```
rhc app create -a anahita -t php-5.3
```

Add MySQL service

```
rhc cartridge add -a anahita -c mysql-5.1
```

Add this upstream Anahita repo

```
cd anahita
git remote add upstream -m master git://github.com/anahitasocial/anahita-openshift.git
git pull -s recursive -Xtheirs --no-edid upstream master
```

Then push the repo upstream

```
git push
```

Repo layout
===========
php/ - Externally exposed php code goes here
libs/ - Additional libraries
misc/ - For not-externally exposed php code
../data - For persistent data (full path in environment var: OPENSHIFT_DATA_DIR)
deplist.txt - list of pears to install
.openshift/action_hooks/pre_build - Script that gets run every git push before the build
.openshift/action_hooks/build - Script that gets run every git push as part of the build process (on the CI system if available)
.openshift/action_hooks/deploy - Script that gets run every git push after build but before the app is restarted
.openshift/action_hooks/post_deploy - Script that gets run every git push after the app is restarted


Notes about layout
==================
Please leave php, libs and data directories but feel free to create additional
directories if needed.

Note: Every time you push, everything in your remote repo dir gets recreated
please store long term items (like an sqlite database) in ../data which will
persist between pushes of your repo.


Environment Variables
=====================
OpenShift provides several environment variables to reference for ease
of use.  The following list are some common variables but far from exhaustive:

    $_ENV['OPENSHIFT_APP_NAME']   - Application name
    $_ENV['OPENSHIFT_DATA_DIR']   - For persistent storage (between pushes)
    $_ENV['OPENSHIFT_TMP_DIR']    - Temp storage (unmodified files deleted after 10 days)

When embedding a database using 'rhc cartridge add', you can reference environment
variables for username, host and password:

    $_ENV['OPENSHIFT_MYSQL_DB_HOST']      - DB host
    $_ENV['OPENSHIFT_MYSQL_DB_PORT']      - DB Port
    $_ENV['OPENSHIFT_MYSQL_DB_USERNAME']  - DB Username
    $_ENV['OPENSHIFT_MYSQL_DB_PASSWORD']  - DB Password

To get a full list of environment variables, simply add a line in your
.openshift/action_hooks/build script that says "export" and push.


