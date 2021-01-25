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

- [x] update private config database in deployrep

sb-udb3
- [x] sudo mysqldump v6jusprod > v6jusprod.sql

sb-udb3 as root
- [x] mysql v7jusprod < v6jusprod.sql
- [x] mysql v7jusprod < vf7sb_mariadb_migrate_6.0_to_7.0.sql

x2go4
- [x] deploy

sb-udb3
- [x] REVOKE ALL ON `v6jusprod`.* FROM  'vfuser_jus_prod'@'%'

GREEN

- [x] update private config database in deployrep

sb-udb3
- [x] mysqldump v6greenprod > v6greenprod.sql


sb-udb3 as root
- [x] mysql v7greenprod < v6greenprod.sql
- [x] mysql v7greenprod < vf7sb_mariadb_migrate_6.0_to_7.0.sql

- [x] update rights pura db (with pura.sql)

x2go4
- [x] vufind deploy

sb-udb3
- [x] REVOKE ALL ON `v6greenprod`.* FROM  'vfuser_green_prod'@'%'
SHOW GRANTS FOR 'vfuser_green_prod'@'%';

PURA 

x2go4
- [x] update db for pura prod
- [x] pura deploy

sb-udb3
- [x] REVOKE ALL ON `v6greenprod`.`pura_user` FROM  'pura-back-end-u18'@'%';
- [x] REVOKE ALL ON `v6greenprod`.`user` FROM  'pura-back-end-u18'@'%';
SHOW GRANTS FOR 'pura-back-end-u18'@'%';


ORANGE


- [x] update private config database in deployrep

sb-udb8
- [x] sudo mysqldump v6orangeprod > v6orangeprod.sql

sb-udb8 as root
- [x] mysql v7orangeprod < v6orangeprod.sql
- [x] mysql v7orangeprod < vf7sb_mariadb_migrate_6.0_to_7.0.sql

x2go2
- [x] vufind deploy

sb-udb8

- [x] REVOKE ALL ON `v6orangeprod`.* FROM  'vfuser_orange_prod'@'%'
SHOW GRANTS FOR 'vfuser_orange_prod'@'%';



CLEAN-UP

- [x] UPDATE DB backup scripts in deploy_rep (udb3, udb8, x2go4)

- [x] REMOVE v6 databases

