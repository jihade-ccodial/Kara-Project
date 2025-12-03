<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Installation

Installation Steps:

- git clone https://github.com/creativagr/kara.git
- composer install
- copy .env.examples .env
- create DB
- php artisan key:generate
- php artisan storage:link
- php artisan migrate:fresh --seed
- mkdir storage/app/public/avatars
- php artisan config:cache

## SSL

SSL is required for hubspot login.
Steps to install in XAMPP:
- 




## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
