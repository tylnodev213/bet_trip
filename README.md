## Yêu cầu
```
- Laravel 10.x
- Apache 2.4.x 
- PHP >= 8.1
- MySQL >= 8.0
- Composer 2.5.x
```

## Cài đặt
```
1. run: composer install 
2. run: cp .env.example .env (change setting APP_URL, DB, MAIL ...)
3. run: php artisan key:generate
4. run: php artisan optimize:clear
5. run: php artisan storage:link
6. run migrate: php artisan migrate
7. run seeder
```
