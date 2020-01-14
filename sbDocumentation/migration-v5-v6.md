JUS

sb-udb1
sudo mysqldump v5jusprod > v5jusprod.sql
scp v5jusprod.sql walterl@sb-udb3.swissbib.unibas.ch:/users/staff/ub/walterl

sb-udb3 as root
mysql v6jusprod < v5jusprod.sql
mysql v6jusprod < migrationLio.sql

x2go2
deploy

GREEN

sb-udb1
sudo mysqldump v5greenprod > v5greenprod.sql
scp v5greenprod.sql walterl@sb-udb3.swissbib.unibas.ch:/users/staff/ub/walterl

sb-udb3 as root
mysql v6greenprod < v5greenprod.sql
mysql v6greenprod < migrationLio.sql

x2go2
vufind deploy

x2go4
pura deploy

ORANGE

sb-udb2
sudo mysqldump v5orangeprod > v5orangeprod.sql
scp v5orangeprod.sql walterl@sb-udb8.swissbib.unibas.ch:/users/staff/ub/walterl

sb-udb8 as root
mysql v6orangeprod < v5orangeprod.sql
mysql v6orangeprod < migrationLio.sql

x2go2
vufind deploy







