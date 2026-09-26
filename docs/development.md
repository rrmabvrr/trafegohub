# Guia de desenvolvimento

## Ambiente local

Para desenvolver nessa aplicação, é importante ter:

- PHP 8.4+
- Composer
- Redis em execução
- Node.js e npm
- banco configurado localmente

## Setup inicial

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
```

## Estrutura de arquivos

### app

Camada principal da aplicação:

- Models
- Policies
- Repositories
- Services
- Jobs
- Http
- Livewire

### config

Toda a configuração do Laravel, incluindo Redis, Horizon, campanhas, integrações e serviços externos.

### routes

Rotas do painel e endpoints de webhook.

### resources

Views Blade e assets frontend.

### tests

Testes automatizados de feature e integração.

## Convenções de desenvolvimento

- seguir o padrão Laravel 12;
- usar serviços para lógica de integração;
- jobs para processamento assíncrono;
- policies para autorização;
- migrações para qualquer alteração de schema;
- testes cobrindo comportamento real;
- nunca depender de API externa real em testes automatizados.

## Trabalhando com jobs e filas

```bash
php artisan queue:work
php artisan queue:restart
```

## Trabalhando com scheduler

```bash
php artisan schedule:list
php artisan schedule:run
```

## Trabalhando com Horizon

```bash
php artisan horizon
```

## Good practices

- manter as credenciais fora do código;
- incluir novos campos de ambiente em .env.example;
- registrar logs em casos de falha de integração;
- testar todas as novas integrações com Http::fake();
- manter validação e autenticação em todos os endpoints sensíveis.

## Testes

```bash
php artisan test
php artisan test tests/Feature/AutomatedIntegrationCoverageTest.php
php artisan test --filter=webhook
```

## Formatação

```bash
vendor/bin/pint --format agent
```

## Próximos passos recomendados

- consolidar políticas por organização e usuário;
- expandir testes de autorização e permissões;
- reforçar autenticação de webhooks;
- validar conectores adicionais em ambiente real;
- evoluir billing e módulos de SaaS;
- implementar IA e automações avançadas em uma próxima fase.
