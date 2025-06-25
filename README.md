# contas-a-pagar
A side project to understand more about web development.

## Manual setup for local development (Windows)
### Download
- Apache 2.4.63-250207 Win64 (from ApacheLounge)
- MySQL Community Server 8.4.5 LTS
- PHP 8.4.8 (VS17 x64 Thread Safe (2025-Jun-03 17:40:02))
- Composer-Setup.exe
### Configuration
#### Apache
- conf/httpd.conf
    - Set ServerName to localhost
    - Add:
        ```conf
        LoadModule php_module "C:/php/php8apache2_4.dll"
        <FilesMatch \.php$>
            SetHandler application/x-httpd-php
        </FilesMatch>

        PHPIniDir "C:/php/"
        DirectoryIndex index.php
        ```
    - AllowOveride All (<Directory "${SRVROOT}/htdocs">)
- To install as a service, run: 
    ```
    bin/httpd.exe -k install
    ```
#### MySQL
- Create file my.ini
    ```ini
    [mysqld]
    basedir=C:/mysql
    datadir=C:/mysql/data
    ```
- Run
    ```
    mysqld --initialize-insecure
    ```
    ```
    mysqld --install MySQL80 --defaults-file="C:/mysql/my.ini"
    ```
#### PHP
- Add root directory to Path (environment variables)
- Copy php.ini-development and rename it to php.ini
- Set `date.timezone = "America/Sao_Paulo"` in php.ini
- Uncomment `;extension=pdo_mysql` in php.ini
#### Composer
```
composer init
```
```
composer require vlucas/phpdotenv
```
```
composer require monolog/monolog
```
#### Ready to start
- Start Apache2.4
- Start MySQL80
- Make sure project files are inside Apache24/htdocs