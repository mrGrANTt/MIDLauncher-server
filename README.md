# Setup site
## Apache 
### Install apache2
```bash
sudo apt update
sudo apt install apache2  
```

### Enable apache2
```bash
sudo systemctl start apache2
sudo systemctl enable apache2
```

### Check status
```bash
sudo systemctl status apache2
```
Will output something like:
```
● apache2.service - The Apache HTTP Server
     Loaded: loaded (/usr/lib/systemd/system/apache2.service; enabled; preset: enabled)
     Active: active (running) since Sat 2025-07-05 15:16:09 +10; 18s ago
       Docs: https://httpd.apache.org/docs/2.4/
   Main PID: 36460 (apache2)
      Tasks: 6 (limit: 37656)
     Memory: 10.9M (peak: 11.9M)
        CPU: 32ms
     CGroup: /system.slice/apache2.service
             ├─36460 /usr/sbin/apache2 -k start
             ├─36465 /usr/sbin/apache2 -k start
             ├─36466 /usr/sbin/apache2 -k start
             ├─36467 /usr/sbin/apache2 -k start
             ├─36468 /usr/sbin/apache2 -k start
             └─36469 /usr/sbin/apache2 -k start
```
Before it you can check website `http://<ip-addres>`. You will see "Apache2 Default Page". If not - check logs in `/var/logapache2/error.log`.

<br />

## PHP
### Install php and moduls
```bash
sudo apt install php libapache2-mod-php php-mysql php-zip
sudo systemctl restart apache2
```

### Test your setup
1. Create test.php in DocumentRoot(By default: `/var/www/html/`)
2. Add simple code 
```php
<?php
	phpinfo(); 
?>
```
3. open in browser `http://<ip-addres>/text.php`
You will see php parametres

<br />

## MySQL
### Install MySQL
```bash
sudo apt install mysql-server
```

### Enable mysql-server
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

### You also can check status
```bash
sudo systemctl status mysql
```
Will output something like:
```
● mysql.service - MySQL Community Server
     Loaded: loaded (/usr/lib/systemd/system/mysql.service; enabled; preset: enabled)
     Active: active (running) since Sat 2025-07-05 17:04:05 +10; 2min 34s ago
   Main PID: 9826 (mysqld)
     Status: "Server is operational"
      Tasks: 37 (limit: 37656)
     Memory: 363.7M (peak: 379.2M)
        CPU: 2.173s
     CGroup: /system.slice/mysql.service
             └─9826 /usr/sbin/mysqld
```

### Run MySQL secure installation script
```bash
sudo mysql_secure_installation
```

Reply to the following MySQL database server options when prompted:
- `VALIDATE PASSWORD`: Enter `y` to enable password strength validation on the database server.
- `Password strength policy`: Enter `2` to enforce multi-character password requirements.
- `Remove anonymous users`: Enter `y` to delete anonymous users from the server.
- `Disallow root login remotely`: Enter `y` to block remote access for the root user.
- `Remove test database`: Enter `y` to remove the default MySQL test database.
- `Reload privilege tables now`: Enter `y` to apply the changes by reloading privilege tables.
Your output should look like the one below when successful:
```
Success.

All done! 
```

### Open sql console
```bash
sudo mysql
```

### Chenge root password
```sql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourGoodP@@ssw00rd';
FLUSH PRIVILEGES;
EXIT;
```
> `YourGoodP@@ssw00rd` - Your new password

### Log in again and enter new password
```bash
mysql -u root -p
```

### Create database and new user
> Don't forget to save new `database name`, `username` and `user's password`. 
```sql
CREATE database <database name>;
CREATE USER '<username>'@'localhost' IDENTIFIED BY '<user's password>';
GRANT SELECT, REFERENCES, CREATE, INSERT, UPDATE, DELETE ON <database name>.* TO '<username>'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

<br />

## GIT
### Install git
```bash
sudo apt install git
```

### Clone code to DocumentRoot(By default: /var/www/html/, but we will change it)
> DocumentRoot is `/var/www/html/` by default, but we will change it
```bash
cd /var/www/
sudo git clone -b main https://github.com/mrGrANTt/MIDLauncher-server midl
```

### Grant Apache access to files
```bash
sudo chown -R www-data:www-data /var/www/midl
sudo chmod -R 755 /var/www/midl
```

<br />

## Other Config
### Copy apache config
```bash
sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/midl.conf
```

### Edit Apache config
```bash
sudo nano /etc/apache2/sites-available/midl.conf
```
- `ServerAdmin` - email available to administrators
- `DocumentRoot` - path to site folder
- `ServerName` - base server domain(or IP)
- `ServerAlias` - additional server domain
For our example? it will be:
```conf
<VirtualHost *:80>
    ServerAdmin example@example.com
    ServerName midl.com
    DocumentRoot /var/www/midl
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### Enable site
```bash
sudo a2ensite midl.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```
If you have problems with the site, check `/var/log/apache2/error.log`.


### Configure database in php
1. Open `index.php`(use nano on other util)
```bash
sudo nano var/www/midl/page/includes/database.php
```
You will see code:
```php
<?php
    error_reporting(0);
    global $mainUrl;
    $mainUrl = "http://localhost/";
?>
<!DOCTYPE html>
<...>
```
2. Replace `mainUrl` with your site's url

### Configure database in php
1. Open `page/includes/database.php`(use nano on other util)
```bash
sudo nano var/www/midl/page/includes/database.php
```
You will see code:
```php
<?php

function connect(
    $host='localhost',
    $user='MIDLauncher',
    $pass='XbD%VO3NM#1a',
    $dbname='MIDLDatabase'
) {
    global $link;
    $link = mysqli_connect($host, $user, $pass);
<...>
```
2. If you remember, we save `database name`, `username` and `user's password`. use them to set `dbname`, `$user` and `$pass`.
Example:
```php
<?php

function connect(
    $host='localhost',
    $user='<username>',
    $pass='<user's password>',
    $dbname='<database name>'
) {
    global $link;
    $link = mysqli_connect($host, $user, $pass);
<...>
```
3. Save file(in Nano: `Ctrl + X`, `Y` and Enter)
4. Restart site
```bash
sudo systemctl restart apache2
```

### Configure `php.ini`
1. Find file:
```bash
php --ini | grep "php.ini"
```
You will see somethink like 
```
Configuration File (php.ini) Path: /etc/php/8.3/cli
Loaded Configuration File:         /etc/php/8.3/cli/php.ini
```

2. Open `php.ini` using "Loaded Configuration File" path
```bash
sudo nano /etc/php/8.3/cli/php.ini
```

3. In opened file edit these 3 parameters
```ini
upload_max_filesize=300M
post_max_size=310M
extension=zip
```
4. Save file(in Nano: `Ctrl + X`, `Y` and Enter)
5. Restart site
```bash
sudo systemctl restart apache2
```
