# TrafegoHub

TrafegoHub é uma plataforma SaaS de gestão de tráfego pago e campanhas digitais, concebida para centralizar campanhas, métricas, leads, integrações de mídia e automações em um único ambiente. O projeto foi estruturado para operar em múltiplas organizações, clientes, contas e plataformas de anúncios, com foco em performance, rastreabilidade e governança operacional.

## Visão geral

A aplicação reúne, em uma mesma base, os principais elementos de operação de mídia paga:

- organizações e usuários com escopo multi-tenant;
- clientes e workspaces;
- integrações com plataformas de anúncios;
- campanhas, grupos, anúncios e criativos;
- métricas agregadas e snapshots;
- leads e CRM;
- filas e processamento assíncrono;
- relatórios e dashboards;
- regras de automação e processamento de webhooks.

## Requisitos

Antes de iniciar o projeto, verifique se seu ambiente atende aos itens abaixo:

- PHP 8.4 ou superior;
- Composer;
- Node.js e npm;
- Redis;
- Banco de dados MySQL, PostgreSQL ou SQLite para desenvolvimento local;
- Git;
- Laravel 12 e Laravel Horizon.

## Instalação

1. Clone o repositório:

   ```bash
   git clone <url-do-repositorio>
   cd trafegohub
   ```

2. Instale as dependências do PHP:

   ```bash
   composer install
   ```

3. Instale as dependências do frontend:

   ```bash
   npm install
   ```

4. Copie o arquivo de ambiente:

   ```bash
   cp .env.example .env
   ```

5. Gere a chave da aplicação:

   ```bash
   php artisan key:generate
   ```

6. Configure o banco e o Redis no arquivo .env.

7. Execute as migrações:

   ```bash
   php artisan migrate
   ```

8. Rode a aplicação localmente:

   ```bash
   php artisan serve
   ```

9. Em outro terminal, inicie o frontend:

   ```bash
   npm run dev
   ```

## Configuração

A configuração principal da aplicação está em:

- .env
- config/app.php
- config/database.php
- config/queue.php
- config/horizon.php
- config/advertising.php
- config/services.php

A aplicação usa sessões em banco e queue em Redis por padrão, além de um conjunto de integrações de mídia pagas isoladas por plataforma.

## Banco de dados

O projeto usa migrations para manter a estrutura do banco. A base principal inclui entidades como:

- users
- organizations
- organization_user
- clients
- workspaces
- integrations
- ad_accounts
- campaigns
- ad_sets
- ads
- creatives
- metrics
- leads
- sync_logs
- webhook_events
- webhook_failures
- reports
- audit_logs

Principais regras aplicadas:

- chaves estrangeiras com cascata ou nullOnDelete quando apropriado;
- indices para consultas por organização, cliente, campanha, data e plataforma;
- unique constraints em identificadores externos;
- soft deletes quando relevante;
- timestamps padronizados.

### Migrações principais

A estrutura contextual está distribuída em diversas migrations, incluindo:

- criação da base de usuários e organizações;
- criação de workspaces e integrações;
- criação de campanhas, criativos e leads;
- criação de métricas e snapshots;
- criação de conexões, contas e plataformas;
- criação de eventos de webhook e auditoria.

## Variáveis de ambiente

O arquivo .env.example já contém a base para configuração. Os principais blocos são:

```env
APP_NAME=TrafegoHub
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://trafegohub.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trafegohub
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=redis
CACHE_STORE=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

ADVERTISING_META_ENABLED=false
ADVERTISING_META_APP_ID=
ADVERTISING_META_APP_SECRET=
ADVERTISING_META_ACCESS_TOKEN=
ADVERTISING_META_ACCOUNT_ID=

ADVERTISING_GOOGLE_ENABLED=false
ADVERTISING_GOOGLE_DEVELOPER_TOKEN=
ADVERTISING_GOOGLE_CLIENT_ID=
ADVERTISING_GOOGLE_CLIENT_SECRET=
ADVERTISING_GOOGLE_REFRESH_TOKEN=
ADVERTISING_GOOGLE_CUSTOMER_ID=

ADVERTISING_TIKTOK_ENABLED=false
ADVERTISING_TIKTOK_APP_ID=
ADVERTISING_TIKTOK_APP_SECRET=
ADVERTISING_TIKTOK_ACCESS_TOKEN=
ADVERTISING_TIKTOK_ADVERTISER_ID=
```

Outros valores de webhook e OAuth também podem existir em config/services.php e config/advertising.php.

## Comandos úteis

### Rodar migrações

```bash
php artisan migrate
php artisan migrate:fresh --seed
```

### Limpando cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
````

### Rodar servidor local

```bash
php artisan serve
```

### Compilar frontend

```bash
npm run build
```

### Rodar frontend em modo de desenvolvimento

```bash
npm run dev
```

### Ver rotas da aplicação

```bash
php artisan route:list
```

## Filas

O projeto usa Laravel Queue com conexão Redis por padrão. Os jobs principais são:

- SyncPlatformMetricsJob
- EvaluateAutomationRulesJob
- ProcessWebhookEventJob

A fila pode ser processada localmente com:

```bash
php artisan queue:work
```

Para filas em background com suporte de monitoramento, o projeto também integra Horizon.

## Scheduler

O agendamento da aplicação está definido em routes/console.php. O scheduler dispara:

- sincronização de métricas a cada 15 minutos;
- avaliação de automações a cada hora.

Exemplo de execução manual:

```bash
php artisan schedule:list
php artisan schedule:run
```

## Horizon

Horizon é usado para monitorar e controlar o processamento de filas em Redis. A configuração fica em config/horizon.php.

### Iniciar Horizon

```bash
php artisan horizon
```

### Acessar a interface

A URL padrão do painel é:

```text
http://localhost:8000/horizon
```

Acesse a interface para acompanhar:

- jobs pendentes;
- jobs completados e falhos;
- métricas de fila;
- workers ativos;
- filas monitoradas.

## Arquitetura

A arquitetura do projeto combina Laravel + Livewire + Services + Jobs + Repositories.

### Princípio fundamental

O TrafegoHub foi projetado como uma plataforma de integração e gerenciamento, e não como um clone visual das plataformas de publicidade.

Cada plataforma externa possui regras, objetos, convenções e limites próprios. Portanto, o sistema cria uma camada de abstração comum:

```text
PLATAFORMA EXTERNA
      ↓
    CONNECTOR
      ↓
 NORMALIZAÇÃO
      ↓
 TRAFEGO HUB
      ↓
   BANCO
      ↓
 DASHBOARD
```

Essa abordagem permite que Meta, Google, TikTok, LinkedIn e outras plataformas sejam tratadas de forma uniforme, mesmo quando os modelos e endpoints são diferentes.

### Camadas principais

- Models: entidades e regras de negócio do domínio;
- Livewire: interfaces do painel e views interativas;
- Services: integrações e regras de processamento;
- Jobs: processamento assíncrono e integrações externas;
- Repositories: encapsulamento de acesso a dados;
- Policies: autorização por organização e recurso;
- Webhooks: recebimento e processamento de eventos externos;
- AdvertisingPlatformManager: roteamento do adaptador correto por plataforma.

### Fluxo principal

1. Usuário acessa o painel Livewire;
2. o componente consulta modelos e métricas;
3. o scheduler ou job dispara sincronização;
4. o manager resolve o connector de acordo com a plataforma;
5. o connector consulta a API externa;
6. os dados são normalizados e persistidos no banco;
7. dashboards e relatórios reagem com os dados atualizados.

## Como adicionar uma nova plataforma

O projeto foi desenhado para permitir extensões por plataforma por meio do manager e dos serviços concretos.

### Passos

1. Crie uma classe em app/Services/Advertising/NomeDaPlataforma.
2. Faça a classe estender AbstractAdvertisingPlatformService.
3. Implemente o método platform() retornando o identificador da plataforma.
4. Implemente os métodos de consulta e atualização exigidos pela interface.
5. Registre a nova plataforma no AppServiceProvider ou no container de dependências.
6. Adicione as configurações em config/advertising.php.
7. Adicione as variáveis de ambiente em .env.example.
8. Atualize o enum/validação de plataformas conforme a regra de negócio.

Exemplo de estrutura:

```php
class MyPlatformService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'myplatform';
    }
}
```

O AdvertisingPlatformManager resolve o adaptador correto pela propriedade platform do Integration.

## Como executar testes

O projeto usa PHPUnit e foi preparado para testes de integração e autenticação.

### Executar a suíte completa

```bash
php artisan test
```

### Executar um arquivo específico

```bash
php artisan test tests/Feature/AutomatedIntegrationCoverageTest.php
```

### Executar um teste específico por filtro

```bash
php artisan test --filter=webhook
```

### Ferramenta de qualidade

```bash
vendor/bin/pint --format agent
```

## Estrutura principal do repositório

```text
app/
  DTOs/
  Http/
  Jobs/
  Livewire/
  Models/
  Policies/
  Providers/
  Repositories/
  Services/
bootstrap/
config/
database/
docs/
public/
resources/
routes/
tests/
```

## Segurança e integrações

O projeto considera pontos importantes para operação real, como:

- autenticação de usuários;
- controle de organização e escopo;
- validação de webhooks;
- registro de eventos e auditoria;
- sincronização por jobs assíncronos;
- criptografia e proteção de tokens sensíveis.

## Créditos

O projeto utiliza tecnologias e bibliotecas de código aberto, com destaque para:

- Laravel
- Livewire
- Horizon
- Redis
- Tailwind CSS
- Vite
- PHPUnit

## Licença

Este projeto utiliza a licença MIT.
