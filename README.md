<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Requirements

- **PHP**: ^8.2 (Laravel 12 requirement)
- **Laravel**: ^12.0
- **Livewire**: ^3.0 (stable)
- **Node.js**: Latest LTS version
- **Composer**: Latest version

## Installation

Installation Steps:

- git clone https://github.com/creativagr/kara.git
- composer install
- npm install
- copy .env.examples .env
- create DB
- php artisan key:generate
- php artisan storage:link
- php artisan migrate:fresh --seed
- mkdir storage/app/public/avatars
- php artisan config:cache
- npm run build (for production) or npm run dev (for development)

## Upgrade Notes (Laravel 12 & Livewire 3 Stable)

This project has been upgraded from Laravel 10 to Laravel 12 and Livewire 3 beta to stable. Key changes:

- **PHP Requirement**: Upgraded from PHP 8.1 to PHP 8.2+
- **Laravel Framework**: Upgraded to Laravel 12
- **Livewire**: Migrated from beta to stable version 3.0
- **Vite**: Upgraded from v3 to v5
- **Component Updates**: Migrated deprecated `$listeners` to `#[On()]` attributes

### Breaking Changes

- PHP 8.2+ is now required
- Some third-party packages may need updates - verify compatibility during `composer update`
- Axios upgraded from 0.27 to 1.7 (major version change)

### Testing

After upgrading dependencies, run:
- `php artisan test` - Run automated test suite
- Manual testing of all Livewire components (Dashboard, Goals, Tasks, Notifications)
- Verify Hubspot and Google Calendar integrations

## SSL

SSL is required for hubspot login.
Steps to install in XAMPP:
- 




## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
