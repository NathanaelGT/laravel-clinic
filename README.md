# Persyaratan
- [PHP 8.0+](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download)
- [NodeJS & NPM](https://nodejs.org/en/download)

# Instalasi
```bash
cp .env.example .env
composer install --optimize-autoloader --no-dev
npm install
npm run prod
php artisan key:generate
php artisan migrate
php artisan optimize
php artisan view:cache
php artisan serve
```

# Mendaftarkan Akun Admin
1. Daftar pada halaman [register](http://127.0.0.1:8000/register)
2. ```bash
   php artisan user:verify {email}
   ```
3. Login menggunakan akun yang telah diverifikasi
