# Filament Server Indicator

A Filament v4 plugin that displays server information, app version, and copyright in the panel footer. Perfect for load-balanced environments where you need to identify which server is handling requests.

## Requirements

- PHP 8.2+
- Laravel 10, 11 or 12
- Filament 4.x

## Installation

```bash
composer require d3s-datasapiens/filament-server-indicator
```

## Configuration

### Register the Plugin

Add the plugin to your Filament panel provider:

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

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=server-indicator-config
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
    'version' => env('APP_VERSION', '1.0.0'),
    'cookie_name' => env('SERVER_INDICATOR_COOKIE', 'SRVNAME'),
    'strip_prefix' => '/^load-balanced-/',
    'copyright' => env('SERVER_INDICATOR_COPYRIGHT', '© :year D3S'),

    'logging' => [
        'enabled' => env('SERVER_INDICATOR_LOG', false),
        'channel' => env('SERVER_INDICATOR_LOG_CHANNEL', 'stack'),
        'include_user' => true,
        'include_tenant' => true,
    ],

    'display' => [
        'show_version' => true,
        'show_server' => true,
        'show_copyright' => true,
    ],
];
```

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

## License

MIT License. See [LICENSE](LICENSE) for more information.
