# TrafegoHub

TrafegoHub é uma plataforma de gestão centralizada de mídia paga e campanhas digitais, construída para ajudar equipes de marketing, performance e operação a controlar anúncios, métricas e automações em um único painel.

A solução foi pensada para unir gestão de campanhas, indicadores de desempenho, CRM de leads e regras de automação em uma interface moderna, com foco em produtividade e tomada de decisão em tempo real.

## Visão geral

O projeto centraliza a operação de campanhas em canais como:

- Meta Ads
- Google Ads
- TikTok Ads
- LinkedIn Ads

Com o TrafegoHub, é possível:

- acompanhar desempenho de campanhas em tempo real;
- visualizar métricas consolidadas em dashboards;
- gerenciar clientes, campanhas, grupos e criativos;
- acompanhar leads e relacionamentos com clientes;
- automatizar regras de avaliação e ações de negócio;
- manter histórico e snapshots de métricas para análise e relatórios.

## Funcionalidades principais

### 1. Gestão de campanhas
O sistema permite criar e controlar campanhas, ad sets, grupos e anúncios, além de manter organização por cliente e organização de trabalho.

### 2. Dashboard analítico
A aplicação inclui painéis e relatórios com indicadores-chave de mídia paga, como alcance, cliques, conversões, custo e eficiência.

### 3. CRM e leads
O módulo de leads permite acompanhar registros e integrar a operação comercial com a performance de anúncios.

### 4. Automação de regras
Regras automáticas podem avaliar campanhas e disparar processos com base em eventos e métricas, reduzindo trabalho manual e acelerando respostas operacionais.

### 5. Relatórios e snapshots
O projeto oferece suporte a relatórios e snapshots de métricas, ajudando a comparar períodos, analisar tendências e facilitar a governança dos dados.

## Tecnologias utilizadas

O TrafegoHub utiliza uma stack moderna com foco em velocidade e escalabilidade:

- PHP 8.4
- Laravel 12
- Livewire 3
- Horizon
- Vite
- Tailwind CSS
- Chart.js
- Redis

## Estrutura do projeto

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
public/
resources/
routes/
src/
tests/
```

## Como rodar localmente

### Pré-requisitos

- PHP 8.4+
- Composer
- Node.js e npm
- Banco de dados configurado para o Laravel
- Redis (quando aplicável para filas e Horizon)

### Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate
php artisan serve
```

### Iniciar o ambiente frontend

```bash
npm run dev
```

### Executar filas e monitoramento

```bash
php artisan horizon
```

## Objetivo do projeto

O TrafegoHub foi criado para centralizar a operação de tráfego pago em uma ferramenta pensada para times que precisam tomar decisões com velocidade, base em dados e organização operacional. A proposta é reduzir a fragmentação de processos e dar uma visão mais clara do desempenho das campanhas e do impacto dos anúncios em negócios reais.

## Créditos

Este projeto foi desenvolvido com base em tecnologias e ecossistemas de código aberto, especialmente:

- [Laravel](https://laravel.com/) — framework principal da aplicação
- [Livewire](https://livewire.laravel.com/) — interfaces dinâmicas em PHP
- [Horizon](https://laravel.com/docs/horizon) — monitoramento e filas do Laravel
- [Vite](https://vitejs.dev/) — build do frontend
- [Tailwind CSS](https://tailwindcss.com/) — estilização visual
- [Chart.js](https://www.chartjs.org/) — gráficos e visualização de métricas
- [Redis](https://redis.io/) — cache e filas

Também agradecemos à comunidade open source e aos mantenedores das bibliotecas que tornam projetos como este possíveis.

## Licença

Este projeto está licenciado sob a licença MIT.
