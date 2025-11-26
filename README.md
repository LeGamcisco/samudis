# eGateway v3 - [23-12-2023 version]
 <p>
    <strong>eGateway</strong> adalah sistem pengelola data CEMS <i>(Continue Emision Monitoring System)</i>. eGateway memiliki fitur yang mudah digunakan dan terintegrasi dengan <strong>SISPEK</strong>
    (Sistem Informasi Pemantauan Emisi Industri Kontinyu)
</p>

## Requirement
1. `php8.2` or greater
2. `PostgreSQL`
3. Enable PHP Extention like : `php-intl`,`php-odbc`,`php-mbstring`,`php-curl`,`php-pgsql`
4. Allow Firewall to `https://ditppu.menlhk.go.id`, `https://api.trusur.tech`, `https://api-cems.trusur.tech` on port `443` (https)

## Recomendation Tools in Server
### Mandatory:
1. XAMPP (>php8.2)
2. PostgreSQL
3. MsEdge (for browser)
### Optional
1. Notepad++ (for text editor)
2. TablePlus (for checking Database)
3. Driver ODBC, SQL Server (depens on requirement)
4. Winrar (optional)
5. Python (optional)

## Instalation
1. Clone repository
```bash
git clone https://github.com/trusur/egateway-laravel.git
cd egateway-laravel
cp .env.example .env
composer install
php artisan key:generate
```
2. Configuration database & other in `.env` file
3. Extract `Task Scheduler.zip` file
4. Open Task Scheduler in Windows Server
5. Create Folder `eGateway V3`
6. Import all task from folder `Task Scheduler`
