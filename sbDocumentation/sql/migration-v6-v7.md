create databases first

udb3
- [x] CREATE DATABASE v7greenprod;
- [x] GRANT ALL PRIVILEGES ON `v7greenprod`.* TO 'vfuser_green_prod'@'%' WITH GRANT OPTION;
- [x] CREATE DATABASE v7jusprod;
- [x] GRANT ALL PRIVILEGES ON `v7jusprod`.* TO 'vfuser_jus_prod'@'%' WITH GRANT OPTION;
- [x] CREATE DATABASE v7justest;
- [x] GRANT ALL PRIVILEGES ON `v7justest`.* TO 'vfuser_jus_test'@'%' WITH GRANT OPTION;

for backups :
- [x] CREATE DATABASE v7orangeprod;

udb8
- [x] CREATE DATABASE v7orangeprod;
- [x] GRANT ALL PRIVILEGES ON `v7orangeprod`.* TO 'vfuser_orange_prod'@'%' WITH GRANT OPTION;

for backups : 
- [x] CREATE DATABASE v7greenprod;


avec les bon users et grant
- [x] copy migration script vf7sb_mariadb_migrate_6.0_to_7.0.sql on udb3 and udb8


JUSTEST

- [x] update private config database in deployrep

justest uses v7greentest

JUS

update private config database in deployrep

sb-udb3
sudo mysqldump v6jusprod > v6jusprod.sql

sb-udb3 as root
mysql v7jusprod < v6jusprod.sql
mysql v7jusprod < vf7sb_mariadb_migrate_6.0_to_7.0.sql

x2go4
deploy

GREEN

update private config database in deployrep

sb-udb3
sudo mysqldump v6greenprod > v6greenprod.sql


sb-udb3 as root
mysql v7greenprod < v6greenprod.sql
mysql v7greenprod < vf7sb_mariadb_migrate_6.0_to_7.0.sql

update pura db (with pura.sql)

x2go4
vufind deploy

x2go4
pura deploy

ORANGE


update private config database in deployrep

sb-udb8
sudo mysqldump v6orangeprod > v6orangeprod.sql

sb-udb8 as root
mysql v7orangeprod < v6orangeprod.sql
mysql v7orangeprod < vf7sb_mariadb_migrate_6.0_to_7.0.sql

x2go2
vufind deploy



- [ ] UPDATE DB backup scripts in deploy_rep (udb3, udb8, x2go4)

- [ ] REMOVE v6 databases