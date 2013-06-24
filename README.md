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
git pull -s recursive -Xtheirs --no-edit upstream master
```

Then push the repo upstream

```
git push
```
To get a full list of environment variables, simply add a line in your
.openshift/action_hooks/build script that says "export" and push.


