# barryvdh - Laravel IDE Helper Generator --dev
    1. composer require --dev barryvdh/laravel-ide-helper 
    2. php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config

# SPATIE - Ray --dev
    1. composer require spatie/laravel-ray --dev
    2. php artisan ray:publish-config

# SPATIE - Laravel Backup
    1. composer require spatie/laravel-backup
    2. php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

# Fortify
    1. composer require laravel/fortify
    2. php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

# Artisan View --dev
    1. composer require sven/artisan-view --dev

# Yajra Datatable
    1. composer require yajra/laravel-datatables-oracle
    'providers' => [
        ....
        ....
        Yajra\DataTables\DataTablesServiceProvider::class,
    ]
    'aliases' => [
        ....
        ....
        'DataTables' => Yajra\DataTables\Facades\DataTables::class,
    ]
    2. php artisan vendor:publish --provider="Yajra\DataTables\DataTablesServiceProvider"

# laravolt - avatar
    1. composer require laravolt/avatar
    2. php artisan vendor:publish --provider="Laravolt\Avatar\ServiceProvider"

# laravel-translatable-string-exporter --dev
    1. composer require kkomelin/laravel-translatable-string-exporter --dev

# SPATIE - flash
    1.composer require spatie/laravel-flash

# iSeed --dev
    1.composer require orangehill/iseed --dev

# SPATIE - laravel-collection-macros
    1.composer require spatie/laravel-collection-macros

# Torann - laravel-currency
    1.composer require torann/currency
    2.php artisan vendor:publish --provider="Torann\Currency\CurrencyServiceProvider" --tag=config
    3.php artisan vendor:publish --provider="Torann\Currency\CurrencyServiceProvider" --tag=migrations

# pricecurrent - laravel-eloquent-filters 
    1. composer require pricecurrent/laravel-eloquent-filters
