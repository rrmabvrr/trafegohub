# Arquitetura do TrafegoHub

## Visão geral

O TrafegoHub foi concebido como uma aplicação Laravel multi-tenant para gestão de tráfego pago, campanhas digitais e CRM de leads. A arquitetura combina padrões de domínio, camada de serviços, jobs assíncronos, Livewire para interfaces e integrações com APIs externas.

## Principais pilares

### 1. Multi-tenancy

A aplicação trabalha com o conceito de organização como limite de negócio. A organização agrega:

- usuários;
- clientes;
- workspaces;
- integrações;
- campanhas;
- métricas;
- relatórios;
- webhooks e eventos.

Isso permite que uma mesma instância sirva diferentes clientes e empresas sem misturar dados de processo.

### 2. Domínio de mídia paga

O modelo principal do produto inclui:

- Organization
- Workspace
- Client
- Integration
- Connection
- AdAccount
- Campaign
- AdSet
- Ad
- Creative
- Metric
- Lead
- Report

Essa estrutura foi desenhada para refletir o fluxo real de gerenciamento de mídia paga, do vínculo com o cliente até o desempenho final da campanha.

### 3. Camada de serviços

A camada de serviços fica responsável por encapsular o comportamento de negócio e integração externa. A principal abstração é a AdvertisingPlatformManager, que resolve o adaptador correto a partir da plataforma configurada em Integration.

### 4. Processamento assíncrono

Jobs e filas são usados para:

- sincronização de métricas;
- avaliação de regras de automação;
- processamento de webhooks;
- tarefas que não devem bloquear a interface.

### 5. Observabilidade e auditoria

A aplicação também conta com registros de auditoria e eventos de webhook para rastrear ações críticas, falhas de assinatura e processamento de dados externos.

## Estrutura funcional

### Models

Os models representam as entidades principais do aplicativo e mantêm os relacionamentos do domínio.

### Livewire

Os componentes Livewire cuidam da interface administrativa e reativa do painel, como:

- dashboard;
- campanhas;
- integrações;
- clientes;
- leads;
- relatórios;
- automações.

### Services

Os services encapsulam a lógica de:

- autenticação com plataformas;
- consultas e atualizações de campanhas;
- normalização de dados externos;
- processamento de webhooks;
- geração de reports e métricas.

### Jobs

Jobs realizam tarefas de sincronização e processamento em background, com filas e utilização do Redis + Horizon.

## Fluxo principal de sincronização

1. Um usuário conecta uma integração de plataforma.
2. O registro de Integration guarda as credenciais e o identificador da plataforma.
3. O Scheduler ou uma ação do usuário dispara um job.
4. O AdvertisingPlatformManager resolve o serviço correto.
5. O serviço consulta a API externa.
6. O retorno é normalizado e persistido em campanhas, adsets, anuncios e métricas.
7. O dashboard atualiza o estado e os relatórios refletem o novo conjunto de dados.

## Princípio fundamental

O TrafegoHub não é um clone visual de nenhuma plataforma de anúncios. Ele funciona como uma camada de integração e gestão entre plataformas externas e o ecossistema interno do produto.

A arquitetura baseia-se no fluxo:

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

Cada plataforma possui sua própria estrutura, endpoints e convenções. O papel do connector é encapsular essas diferenças e transformar os dados em um formato unificado consumido pelo sistema.

## Princípios adotados

- organização como raiz multi-tenant;
- serviços por plataforma;
- jobs para orquestração assíncrona;
- policies para autorizações;
- migrações para evolução do schema;
- métricas padronizadas por data e campanha;
- eventos de webhook com reprocessamento e validação de assinatura;
- modelo unificado para múltiplas fontes externas.

## Pontos de extensão

Para adicionar uma nova plataforma, basta:

- criar um service específico;
- implementar a interface de plataforma;
- registrar o adaptador no container;
- adicionar configuração no config/advertising.php;
- incluir o identificador no fluxo de integração.

## Considerações operacionais

Em ambiente de produção, é recomendado:

- manter Redis e Horizon ativos;
- configurar filas por prioridade;
- monitorar métricas de workers;
- proteger webhooks com secrets específicos;
- aplicar auditoria e logs em ações sensíveis;
- usar políticas e escopo por organização para garantir isolamento de dados.
