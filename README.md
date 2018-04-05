## Installation

You need to have Apache 2.4 HTTP server, PHP v.5.6 or later with `gd` and `intl` extensions, and MySQL 5.6 or later.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
composer install
```

The command above will install the dependencies (Zend Framework and Doctrine).

Enable development mode:

```
composer development-enable
```

Create the `data/cache` directory:

```
mkdir data/cache
```

Adjust permissions for `data` directory:

```
sudo chown -R www-data:www-data data
sudo chmod -R 775 data
```

Create `public/img/captcha` directory:

```
mkdir public/img/captcha
```

Adjust permissions for `public/img/captcha` directory:

```
sudo chown -R www-data:www-data public/img/captcha
sudo chmod -R 775 public/img/captcha 
```

Create `config/autoload/local.php` config file by copying its distrib version:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

Edit `config/autoload/local.php` and set database password parameter.

Login to MySQL client:

```
mysql -u root -p
```

Create database:

```
CREATE DATABASE config_management;
quit
```

Run database migrations to initialize database schema:

```
./vendor/bin/doctrine-module migrations:migrate
```

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/config-management/public
    
    <Directory /path/to/config-management/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

Now you should be able to see the Role Demo website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 
