> [!WARNING]
> **This repository is archived and no longer maintained.** It holds the PHP version of Anahita, which has been retired.
>
> Anahita is now **Anahita Services**: social software in Go microservices, federated over ActivityPub and the AT Protocol. Its web app is [anahitasocial/anahita-web](https://github.com/anahitasocial/anahita-web), and [anahitasocial/anahita-services](https://github.com/anahitasocial/anahita-services) is to be published shortly. Read more at [www.anahita.io](https://www.anahita.io).
>
> Thank you to everyone who built, ran and used Anahita in PHP since 2010.

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


