# Integrações

## Visão geral

O TrafegoHub foi desenhado para integrar múltiplas plataformas de anúncios por meio de uma arquitetura de adaptadores. Cada plataforma tem um service responsável por encapsular chamadas à API externa e por padronizar a leitura de campanhas, métricas e recursos publicitários.

### Princípio de integração

A ideia central é tratar cada plataforma externa como um provedor com regras próprias, mas transformar tudo em uma estrutura comum para o produto:

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

Isso evita que o sistema dependa de detalhes específicos de cada fornecedor na camada de apresentação e análise.

## Manager de plataformas

O ponto central está em AdvertisingPlatformManager.

Esse componente recebe a lista de serviços de plataformas e escolhe o adaptador correto com base no valor de Integration::platform.

## Plataformas implementadas

### Meta Ads

- service: App\Services\Advertising\Meta\MetaAdsService
- identificador: meta
- uso: consulta de campanhas e atualização de status

### Google Ads

- service: App\Services\Advertising\Google\GoogleAdsService
- identificador: google

### TikTok Ads

- service: App\Services\Advertising\TikTok\TikTokAdsService
- identificador: tiktok

### LinkedIn Ads

- service: App\Services\Advertising\LinkedIn\LinkedInAdsService
- identificador: linkedin

## Estrutura de um adaptador

Cada adaptador deve:

- estender AbstractAdvertisingPlatformService;
- implementar platform();
- expor métodos como getCampaigns, getCampaign, pauseCampaign, activateCampaign e outros necessários;
- usar Http do Laravel para comunicação com a API externa;
- tratar erros e registrar logs;
- manter um retorno padronizado.

## Configuração por plataforma

As chaves de configuração de integrações ficam em config/advertising.php e recebem valores do .env.

Exemplos:

- ADVERTISING_META_ACCESS_TOKEN
- ADVERTISING_GOOGLE_DEVELOPER_TOKEN
- ADVERTISING_TIKTOK_APP_SECRET

## Fluxo de sincronização

1. O usuário cria ou atualiza uma Integration.
2. A integração informa a plataforma e as credenciais.
3. O processo de sincronização chama o manager apropriado.
4. O service consulta a API e retorna dados em formato bruto.
5. Esses dados são transformados e persistidos no banco.
6. O dashboard e os relatórios passam a refletir a informação nova.

## Webhooks

A aplicação também processa webhooks vindos de plataformas com validação de assinatura.

### Validadores de assinatura

O WebhookSignatureValidator analisa cabeçalhos comuns, por exemplo:

- Meta: x-hub-signature-256
- Google: x-goog-signature
- TikTok: x-tiktok-signature

### Processamento

Os webhooks são armazenados em WebhookEvent e, em seguida, processados por ProcessWebhookEventJob e WebhookEventProcessor.

## Boas práticas

- nunca executar chamadas externas em testes sem mock;
- manter sempre o token em armazenamento seguro;
- registrar falhas e eventos em logs;
- validar assinatura antes de processar payloads externos;
- padronizar a resposta da API externa com filtro e normalização.
