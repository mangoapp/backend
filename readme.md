# Mango Backend

Mango Backend is the repository that powers our frontend application. Setting up the backend is fairly simple. This guide assumes you have a standard LAMP install working. If you do not have a LAMP stack installed, check out [XAMPP](https://www.apachefriends.org/index.html). Once installed, be sure to create a database for Mango.

**Installation**

1. Clone github repo: `git clone https://github.com/mangoapp/backend.git`

2. Install [Composer](https://getcomposer.org), and run `composer install` in your cloned directory. You may need to run `php composer.phar install` if you have not added composer to your PATH.
 
3. Copy `.env.example` to `.env` and fill in your database connection information

4. Run `php artisan migrate`, followed by `php artisan db:seed`

5. Generate a JWT secret: `php artisan jwt:generate`

6. Generate an app secret: `php artisan key:generate`

7. If you are running Apache, copy the following into your config file:
```
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```

You can now navigate to http://localhost/backend/public and begin making API requests. To view available API requests, please refer to the Wiki.
