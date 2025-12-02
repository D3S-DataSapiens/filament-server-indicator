# filament-server-indicator

Plugin Filament v4 para exibir informações do servidor no footer do painel admin.

## Identificação

- **Vendor:** d3s-datasapiens
- **Namespace PHP:** D3SDataSapiens\ServerIndicator
- **Organização GitHub:** https://github.com/D3S-DataSapiens
- **Autor:** Claudio (cpereiraweb)

## Requisitos

- PHP 8.2+
- Laravel 10, 11 ou 12
- Filament v4
- spatie/laravel-package-tools ^1.16

## Estrutura do Projeto

```
filament-server-indicator/
├── config/
│   └── server-indicator.php      # Configurações do plugin
├── resources/
│   └── views/
│       └── footer.blade.php      # View do footer
├── src/
│   ├── ServerIndicatorPlugin.php       # Plugin Filament
│   └── ServerIndicatorServiceProvider.php  # Service Provider
├── tests/
│   ├── TestCase.php
│   └── ServerIndicatorPluginTest.php
├── composer.json
├── CLAUDE.md                     # Este arquivo
└── README.md
```

## Funcionalidades

O plugin exibe no footer do Filament:
- **Copyright:** Texto customizável com placeholder `:year`
- **Versão:** Versão da aplicação
- **Servidor:** Nome do servidor (lido de cookie de load balancer)

### Layout do Footer
```
© 2025 D3S | 0037::2025:06:23 | aws-d3s-web-002
```
Formato: `Copyright | Versão | Servidor` - elementos separados por `|`

### Logging
Opcionalmente registra informações do servidor em cada request:
- Servidor atual
- Usuário autenticado
- Tenant (multi-tenancy)

## Uso no PanelProvider

```php
use D3SDataSapiens\ServerIndicator\ServerIndicatorPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            ServerIndicatorPlugin::make()
                ->version('1.0.0')
                ->copyright('© :year D3S')
                ->cookieName('SRVNAME')
                ->logging(true)
        );
}
```

## Configuração via .env

```env
APP_VERSION=1.0.0
SERVER_INDICATOR_COOKIE=SRVNAME
SERVER_INDICATOR_COPYRIGHT="© :year D3S"
SERVER_INDICATOR_LOG=false
SERVER_INDICATOR_LOG_CHANNEL=stack
```

## Comandos Úteis

```bash
# Publicar config
php artisan vendor:publish --tag=server-indicator-config

# Executar testes
./vendor/bin/phpunit
```

## Convenções

- Commits seguem Conventional Commits
- Código PHP segue PSR-12
- Views usam Tailwind CSS (compatível com Filament)
