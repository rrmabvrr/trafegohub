# Banco de dados

## Visão geral

O banco do TrafegoHub foi modelado para suportar um ambiente multi-tenant com foco em performance de campanhas, métricas e relatórios. A estrutura prioriza relacionamento entre organização, clientes, contas, campanhas, métricas e eventos.

## Entidades principais

### Organização e usuários

- organizations
- users
- organization_user

A organização funciona como raiz do escopo do cliente. Usuários podem pertencer a múltiplas organizações e ter papéis específicos em cada contexto.

### Clientes e workspaces

- clients
- workspaces

Essas entidades organizam o relacionamento entre a organização e as contas de mídia ou clientes finais.

### Integrações e conexões

- integrations
- connections
- platforms
- ad_accounts

Esse conjunto guarda credenciais, conexões, plataformas e contas vinculadas às integrações externas.

### Campanhas e mídia

- campaigns
- ad_sets
- ads
- creatives

Esses modelos representam a árvore de publicidade digital: campanha, conjunto, anúncio e criativo.

### Métricas e relatórios

- metrics
- campaign_metric_snapshots
- reports

Esses registros armazenam os indicadores de performance por data, campanha e plataforma.

### Leads e CRM

- leads
- webhook_events
- webhook_failures

Esses elementos permitem o rastreio de leads, processamento de eventos externos e integração com operações de CRM.

### Auditoria

- audit_logs

Permite registrar alterações críticas e ações importantes no sistema.

## Índices e consultas principais

A aplicação foi estruturada pensando em consultas por:

- organization_id
- client_id
- platform_id
- ad_account_id
- campaign_id
- date

Esses índices ajudam a melhorar a leitura de dashboards e relatórios de performance.

## Conventions importantes

- relações com foreign keys e constraints;
- unique constraints para IDs externos;
- campos de timestamp em tabelas críticas;
- values em JSON quando necessário para payloads e metadados;
- campos sensíveis em armazenamento protegido ou criptografado.

## Exemplo de fluxo de persistência

Quando uma sincronização da Meta Ads é executada, o sistema salva ou atualiza:

1. campanhas;
2. ad sets;
3. anúncios;
4. criativos;
5. métricas por período;
6. snapshots para relatórios.

## Migrations relevantes

Principais locais de schema:

- database/migrations/2026_09_22_000001_create_workspaces_table.php
- database/migrations/2026_09_22_000003_create_campaigns_table.php
- database/migrations/2026_09_23_000011_create_organizations_and_clients.php
- database/migrations/2026_09_25_000004_create_metrics_table.php
- database/migrations/2026_09_25_000006_create_webhook_events_table.php
- database/migrations/2026_09_25_000008_create_platforms_connections_and_ad_accounts_schema.php
- database/migrations/2026_09_26_000001_create_audit_logs_table.php

## Comandos úteis

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
php artisan tinker
```
