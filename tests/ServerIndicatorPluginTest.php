<?php

declare(strict_types=1);

namespace D3SDataSapiens\ServerIndicator\Tests;

use D3SDataSapiens\ServerIndicator\ServerIndicatorPlugin;

class ServerIndicatorPluginTest extends TestCase
{
    public function test_plugin_can_be_instantiated(): void
    {
        $plugin = ServerIndicatorPlugin::make();

        $this->assertInstanceOf(ServerIndicatorPlugin::class, $plugin);
    }

    public function test_plugin_has_correct_id(): void
    {
        $plugin = ServerIndicatorPlugin::make();

        $this->assertEquals('server-indicator', $plugin->getId());
    }

    public function test_version_can_be_set(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->version('2.0.0');

        $this->assertEquals('2.0.0', $plugin->getVersion());
    }

    public function test_version_defaults_to_config(): void
    {
        config()->set('server-indicator.version', '1.5.0');

        $plugin = ServerIndicatorPlugin::make();

        $this->assertEquals('1.5.0', $plugin->getVersion());
    }

    public function test_copyright_can_be_set(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->copyright('© :year Test Corp');

        $expected = '© '.date('Y').' Test Corp';
        $this->assertEquals($expected, $plugin->getCopyright());
    }

    public function test_copyright_replaces_year_placeholder(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->copyright('Copyright :year');

        $this->assertStringContainsString(date('Y'), $plugin->getCopyright());
    }

    public function test_cookie_name_can_be_set(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->cookieName('MY_SERVER');

        $this->assertEquals('MY_SERVER', $plugin->getCookieName());
    }

    public function test_strip_prefix_can_be_set(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->stripPrefix('/^prefix-/');

        $this->assertEquals('/^prefix-/', $plugin->getStripPrefix());
    }

    public function test_logging_can_be_enabled(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->logging(true, 'daily');

        $this->assertTrue($plugin->isLoggingEnabled());
        $this->assertEquals('daily', $plugin->getLogChannel());
    }

    public function test_show_version_can_be_toggled(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->showVersion(false);

        $reflection = new \ReflectionClass($plugin);
        $property = $reflection->getProperty('showVersion');
        $property->setAccessible(true);

        $this->assertFalse($property->getValue($plugin));
    }

    public function test_show_server_can_be_toggled(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->showServer(false);

        $reflection = new \ReflectionClass($plugin);
        $property = $reflection->getProperty('showServer');
        $property->setAccessible(true);

        $this->assertFalse($property->getValue($plugin));
    }

    public function test_show_copyright_can_be_toggled(): void
    {
        $plugin = ServerIndicatorPlugin::make()
            ->showCopyright(false);

        $reflection = new \ReflectionClass($plugin);
        $property = $reflection->getProperty('showCopyright');
        $property->setAccessible(true);

        $this->assertFalse($property->getValue($plugin));
    }

    public function test_tenant_resolver_can_be_set(): void
    {
        $resolver = fn ($user) => $user->tenant_id;

        $plugin = ServerIndicatorPlugin::make()
            ->tenantResolver($resolver);

        $this->assertSame($resolver, $plugin->getTenantResolver());
    }

    public function test_fluent_api_returns_self(): void
    {
        $plugin = ServerIndicatorPlugin::make();

        $this->assertSame($plugin, $plugin->version('1.0.0'));
        $this->assertSame($plugin, $plugin->copyright('Test'));
        $this->assertSame($plugin, $plugin->cookieName('TEST'));
        $this->assertSame($plugin, $plugin->stripPrefix('/test/'));
        $this->assertSame($plugin, $plugin->logging(true));
        $this->assertSame($plugin, $plugin->showVersion(true));
        $this->assertSame($plugin, $plugin->showServer(true));
        $this->assertSame($plugin, $plugin->showCopyright(true));
    }
}
