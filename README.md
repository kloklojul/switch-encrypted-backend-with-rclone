# Switch Encrypted File Downloader For Tinfoil

A simple proof-of-concept for a custom back end to use with tinfoil.
It utilizes rclone to download from a google drive that has encrypted files
safed on it.

## Requirements

* php
* composer
* a configured rclone (it is strongly recommended to encrypt your files on the google drive with rclone for security reasons)
* a webserver

## Install the Application

For deployment use your favorite web server.

* Use composer to install all necessary dependencies.
```
cd /path/to/project
composer install
```
* Next create a mysql/mariadb DB and load the <b>game_list.sql</b> into the freshly created database.

* Everything you need to configure for the project to work can be found in <b>app/config.php</b>.

* to reduce the number of requests to the google drive the database needs to be filled with directory structure of the drive. You can try to use the <b>rcloneToSQLMigrator</b>. To use the migrator send a post request to domain.com with
```
'key':'the_key_set_in_the_config'
```
This should fill the database with the necessary information. If not you will need to write a custom script to migrate your drive to the Database.
<br>
<br>
In Tinfoil add **domain.com/all** as your host
## Common Errors
> rclone failes to read or write to its config file

Make sure to set all necessary permissions for your webserver user

<br>
<br>
<br>
<br>

## To-Do

```
- currently doesn't work with japanese/chinese character
```

# DO NOT USE THIS FOR PRODUCTION this is just a POC
