###  Setting up the National Licence
- Update the db by executing the script located in `/home/nicolas/PhpstormProjects/swissbib/source/sbDocumentation/vf3sb_mysql_migrate_swiss_national_licences.sql`
- Complete the missing information in the configuration according with the production options
(file `local/config/vufind/config.ini`) like in this exemple:
```
[SwitchApi]
auth_user = natlic
auth_password = xxxxxxx

[EmailService]
connection_config[username] = xxxxxxx@gmail.com
connection_config[password] = "xxxxxxxxxx"

[Test]
switchApi.auth_user = natlic
switchApi.auth_password = xxxxxxxx
```
- Run `cli/cssBuilder.sh` for compile the css style
- Create directory for exports in home directory : `mkdir export/nationalLicence`


### Console commands (called by cron job)
It is possible to run the following commands via a cron job.
##### Before run the script
- Run `cli/createClassMapFiles.sh` to create the classmap files used by the console command
- Remove the `cli` caches by running  `rm -rf local/cache/cli/`. Be careful to be in the root 
path before run this command.

##### Export of the National Licence users
Please make sure you have correctly setup the SMTP server config.
(`module/Swissbib/config/module.config.php on section` on `swisbib.national_licence.smtp_options`) to support the TLS encryption.
```bash
sudo cli/send-national-licence-user-export.sh 
```

##### Maintenence task
- For production environment: Set the environment variables `SWITCH_API_USER` and `SWITCH_API_PASSW` in the script file 
`update-national-licence-user-info.sh` or in your `~/.bashrc` or `~/.profile`. This is necessary because the apache environment variables
 are not available in the cli environment.
```bash
sudo cli/update-national-licence-user-info.sh
``` 


