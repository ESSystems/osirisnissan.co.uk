ll
hg clone https://bitbucket.org/stewartc/cm2
ls cm2/app/webroot
ll cm2/app/webroot
service pwd
pwd
ls -a
cd cm2
hg checkout recall
vi app/config/core.php 
ls -l app
ls -l app/tmp
ls app/config
cp app/config/database.php.default app/config/database.php
vi app/config/database.php
hg
hg status
hg revert
hg status
hg revert app/config/core.php
hg status
rm app/config/core.php.orig
hg status
ls app
ll app
ll app/downloads/
ll app
exit
ls
cd cm2
ls
hg pull -u
