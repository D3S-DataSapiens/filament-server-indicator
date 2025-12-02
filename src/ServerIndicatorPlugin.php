<?php

declare(strict_types=1);

namespace D3SDataSapiens\ServerIndicator;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class ServerIndicatorPlugin implements Plugin
{
    protected ?string $version = null;

    protected ?string $copyright = null;

    protected ?string $cookieName = null;

    protected ?string $stripPrefix = null;

    protected bool $loggingEnabled = false;

    protected ?string $logChannel = null;

    protected bool $showVersion = true;

    protected bool $showServer = true;

    protected bool $showCopyright = true;

    protected ?Closure $tenantResolver = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'server-indicator';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn (): View => view('server-indicator::footer', [
                'version' => $this->getVersion(),
                'server' => $this->getServer(),
                'copyright' => $this->getCopyright(),
                'showVersion' => $this->showVersion,
                'showServer' => $this->showServer,
                'showCopyright' => $this->showCopyright,
            ]),
        );
    }

    /**
     * Set the application version.
     */
    public function version(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the application version.
     */
    public function getVersion(): string
    {
        return $this->version ?? config('server-indicator.version', '1.0.0');
    }

    /**
     * Set the copyright text.
     */
    public function copyright(string $copyright): static
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * Get the copyright text.
     */
    public function getCopyright(): string
    {
        $copyright = $this->copyright ?? config('server-indicator.copyright', '© :year D3S');

        return str_replace(':year', date('Y'), $copyright);
    }

    /**
     * Set the cookie name to read server info from.
     */
    public function cookieName(string $name): static
    {
        $this->cookieName = $name;

        return $this;
    }

    /**
     * Get the cookie name.
     */
    public function getCookieName(): string
    {
        return $this->cookieName ?? config('server-indicator.cookie_name', 'SRVNAME');
    }

    /**
     * Set the prefix pattern to strip from server name.
     */
    public function stripPrefix(?string $pattern): static
    {
        $this->stripPrefix = $pattern;

        return $this;
    }

    /**
     * Get the strip prefix pattern.
     */
    public function getStripPrefix(): ?string
    {
        return $this->stripPrefix ?? config('server-indicator.strip_prefix');
    }

    /**
     * Enable or disable logging.
     */
    public function logging(bool $enabled = true, ?string $channel = null): static
    {
        $this->loggingEnabled = $enabled;
        $this->logChannel = $channel;

        return $this;
    }

    /**
     * Check if logging is enabled.
     */
    public function isLoggingEnabled(): bool
    {
        return $this->loggingEnabled || config('server-indicator.logging.enabled', false);
    }

    /**
     * Get the log channel.
     */
    public function getLogChannel(): string
    {
        return $this->logChannel ?? config('server-indicator.logging.channel', 'stack');
    }

    /**
     * Show or hide version.
     */
    public function showVersion(bool $show = true): static
    {
        $this->showVersion = $show;

        return $this;
    }

    /**
     * Show or hide server info.
     */
    public function showServer(bool $show = true): static
    {
        $this->showServer = $show;

        return $this;
    }

    /**
     * Show or hide copyright.
     */
    public function showCopyright(bool $show = true): static
    {
        $this->showCopyright = $show;

        return $this;
    }

    /**
     * Set a custom tenant resolver for logging.
     */
    public function tenantResolver(Closure $resolver): static
    {
        $this->tenantResolver = $resolver;

        return $this;
    }

    /**
     * Get the tenant resolver.
     */
    public function getTenantResolver(): ?Closure
    {
        return $this->tenantResolver;
    }

    /**
     * Get the server name from cookie.
     */
    public function getServer(): ?string
    {
        $cookieName = $this->getCookieName();
        $serverName = $_COOKIE[$cookieName] ?? null;

        if (empty($serverName)) {
            $this->logServerInfo(null);

            return null;
        }

        $stripPrefix = $this->getStripPrefix();

        if ($stripPrefix !== null) {
            $serverName = preg_replace($stripPrefix, '', $serverName);
        }

        $this->logServerInfo($serverName);

        return $serverName;
    }

    /**
     * Log server information.
     */
    protected function logServerInfo(?string $server): void
    {
        if (! $this->isLoggingEnabled()) {
            return;
        }

        $config = config('server-indicator.logging', []);
        $parts = [];

        if ($server !== null) {
            $parts[] = "Server: {$server}";
        } else {
            $parts[] = 'Server: No '.$this->getCookieName().' cookie';
        }

        $user = auth()->user();

        if ($user && ($config['include_user'] ?? true)) {
            $parts[] = 'User: '.($user->name ?? $user->email ?? $user->getAuthIdentifier());
        }

        if ($user && ($config['include_tenant'] ?? true) && $this->tenantResolver !== null) {
            $tenant = ($this->tenantResolver)($user);
            if ($tenant !== null) {
                $parts[] = 'Tenant: '.$tenant;
            }
        } elseif ($user && ($config['include_tenant'] ?? true)) {
            // Try common tenant patterns
            $tenant = $this->resolveTenantFromUser($user);
            if ($tenant !== null) {
                $parts[] = 'Tenant: '.$tenant;
            }
        }

        \Illuminate\Support\Facades\Log::channel($this->getLogChannel())
            ->info(implode(' | ', $parts));
    }

    /**
     * Try to resolve tenant from user using common patterns.
     */
    protected function resolveTenantFromUser(mixed $user): ?string
    {
        // Try currentTenant (Filament multi-tenancy)
        if (method_exists($user, 'currentTenant') || property_exists($user, 'currentTenant')) {
            $tenant = $user->currentTenant;
            if ($tenant !== null) {
                return $tenant->alias ?? $tenant->name ?? $tenant->id ?? null;
            }
        }

        // Try getTenant method
        if (method_exists($user, 'getTenant')) {
            $tenant = $user->getTenant();
            if ($tenant !== null) {
                return $tenant->alias ?? $tenant->name ?? $tenant->id ?? null;
            }
        }

        return null;
    }
}
