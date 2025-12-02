# Filament Server Indicator

A Filament v4 plugin that displays server information, app version, and copyright in the panel footer. Perfect for load-balanced environments where you need to identify which server is handling requests.

## Requirements

- PHP 8.2+
- Laravel 10, 11 or 12
- Filament 4.x

## Installation

### Step 1: Install via Composer

```bash
composer require d3s-datasapiens/filament-server-indicator
```

### Step 2: Register the Plugin

Add the plugin to your Filament panel provider (e.g., `app/Providers/Filament/AdminPanelProvider.php`):

```php
use D3SDataSapiens\ServerIndicator\ServerIndicatorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            ServerIndicatorPlugin::make()
                ->version('1.0.0')
                ->copyright('© :year Your Company')
        );
}
```

### Step 3: Publish Assets (Optional)

#### Publish Configuration

To customize the default settings:

```bash
php artisan vendor:publish --tag=server-indicator-config
```

This publishes `config/server-indicator.php`.

#### Publish Views

To customize the footer layout:

```bash
php artisan vendor:publish --tag=server-indicator-views
```

This publishes views to `resources/views/vendor/server-indicator/`.

#### Publish Everything

To publish all assets at once:

```bash
php artisan vendor:publish --provider="D3SDataSapiens\ServerIndicator\ServerIndicatorServiceProvider"
```

## Usage

### Basic Usage

```php
ServerIndicatorPlugin::make()
    ->version('1.0.0')
    ->copyright('© :year D3S')
```

This displays: `© 2025 D3S | 1.0.0`

### With Server Name (Load Balancer)

When behind a load balancer that sets a cookie with the server name:

```php
ServerIndicatorPlugin::make()
    ->version('1.0.0')
    ->copyright('© :year D3S')
    ->cookieName('SRVNAME')
    ->stripPrefix('/^load-balanced-/')
```

This displays: `© 2025 D3S | 1.0.0 | web-002`

### Display Options

Control what is shown in the footer:

```php
ServerIndicatorPlugin::make()
    ->showVersion(true)
    ->showServer(true)
    ->showCopyright(true)
```

### Enable Logging

Log server information on each request:

```php
ServerIndicatorPlugin::make()
    ->logging(true, 'daily')
```

Logs include: server name, authenticated user, and tenant (if applicable).

### Custom Tenant Resolver

For multi-tenant applications:

```php
ServerIndicatorPlugin::make()
    ->tenantResolver(fn ($user) => $user->currentTenant?->name)
```

## Environment Variables

Add these to your `.env` file:

```env
# App version displayed in footer
APP_VERSION=1.0.0

# Cookie name containing server identifier
SERVER_INDICATOR_COOKIE=SRVNAME

# Copyright text (:year is replaced with current year)
SERVER_INDICATOR_COPYRIGHT="© :year Your Company"

# Enable logging
SERVER_INDICATOR_LOG=false
SERVER_INDICATOR_LOG_CHANNEL=stack
```

## Configuration File

After publishing, you can customize `config/server-indicator.php`:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Application Version
    |--------------------------------------------------------------------------
    */
    'version' => env('APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Cookie Name
    |--------------------------------------------------------------------------
    */
    'cookie_name' => env('SERVER_INDICATOR_COOKIE', 'SRVNAME'),

    /*
    |--------------------------------------------------------------------------
    | Server Name Prefix to Remove
    |--------------------------------------------------------------------------
    */
    'strip_prefix' => '/^load-balanced-/',

    /*
    |--------------------------------------------------------------------------
    | Copyright Text
    |--------------------------------------------------------------------------
    */
    'copyright' => env('SERVER_INDICATOR_COPYRIGHT', '© :year D3S'),

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('SERVER_INDICATOR_LOG', false),
        'channel' => env('SERVER_INDICATOR_LOG_CHANNEL', 'stack'),
        'include_user' => true,
        'include_tenant' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Display Options
    |--------------------------------------------------------------------------
    */
    'display' => [
        'show_version' => true,
        'show_server' => true,
        'show_copyright' => true,
    ],
];
```

## Customizing the Footer View

After publishing the views, you can customize `resources/views/vendor/server-indicator/footer.blade.php`:

```blade
<div class="flex items-center justify-center gap-x-1 text-sm text-gray-500 dark:text-gray-400">
    @if($showCopyright && $copyright)
        <span>{{ $copyright }}</span>
    @endif

    @if($showCopyright && $copyright && ($showVersion || ($showServer && $server)))
        <span>|</span>
    @endif

    @if($showVersion && $version)
        <span>{{ $version }}</span>
    @endif

    @if($showVersion && $version && $showServer && $server)
        <span>|</span>
    @endif

    @if($showServer && $server)
        <span>{{ $server }}</span>
    @endif
</div>
```

Available variables in the view:
- `$copyright` - The formatted copyright text
- `$version` - The version string
- `$server` - The server name (from cookie)
- `$showCopyright` - Whether to show copyright
- `$showVersion` - Whether to show version
- `$showServer` - Whether to show server name

## API Reference

| Method | Description |
|--------|-------------|
| `version(string $version)` | Set the version string |
| `copyright(string $text)` | Set copyright text (`:year` placeholder available) |
| `cookieName(string $name)` | Cookie name containing server identifier |
| `stripPrefix(?string $pattern)` | Regex pattern to clean server name |
| `logging(bool $enabled, ?string $channel)` | Enable/disable logging |
| `showVersion(bool $show)` | Show/hide version |
| `showServer(bool $show)` | Show/hide server name |
| `showCopyright(bool $show)` | Show/hide copyright |
| `tenantResolver(Closure $resolver)` | Custom tenant resolver for logging |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email security@d3s.com.br instead of using the issue tracker.

## Credits

- [Claudio Pereira](https://github.com/cpereiraweb)
- [All Contributors](../../contributors)

## License

MIT License. See [LICENSE](LICENSE) for more information.
