# urc-sn-gen

Serial Number Generator

Requirements:

- Apache 2 Web Server
- Apache module mod_rewrite.c
- PHP 5.6
- MySQL 5

Installation:
- Create database and provide it's name and credentials to the nette-app/app/config/config.local.neon configuration file.
- Install the database structure using nette-app/sql/install.sql SQL script.
- Make sure nette-app/temp and nette-app/log directories are writable by the web-server.
- Create a virtual host pointing to nette-app/www directory.
- Allow apache config override using .htaccess file on the nette-app/www directory and all subdirectories.
- Make sure mod_rewrite is enabled.
