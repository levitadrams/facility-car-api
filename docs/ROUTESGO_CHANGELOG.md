# Changelog - RotasGo v1.1.0

## [1.1.0] - 2026-06-15

### 🎯 Correção do Cálculo de Tempo

#### Adicionado

**Backend (Laravel):**
- ✅ `config/routesgo.php` - Sistema de configuração de fatores de correção
- ✅ `app/Services/OsrmService.php` - Serviço centralizado para comunicação com OSRM
- ✅ Novo endpoint `POST /api/destinations/calculate-route`
- ✅ Suporte a variáveis de ambiente (`ROUTESGO_TRAFFIC_FACTOR`, `ROUTESGO_DEBUG`)
- ✅ Logs detalhados de debug para análise

**Frontend (React Native):**
- ✅ Tipo `RouteCalculation` com campos `durationCalculated` e `durationEstimated`
- ✅ Função `calculateRouteWithEstimate()` com aplicação de fator de correção
- ✅ Exibição de 3 métricas: Distância, Tempo Calculado, Tempo Estimado
- ✅ Estilo visual diferenciado (tempo calculado riscado, tempo estimado em destaque)

**Documentação:**
- ✅ `docs/ROUTESGO_ANALISE.md` - Análise técnica completa
- ✅ `docs/ROUTESGO_GUIA_RAPIDO.md` - Guia prático de uso
- ✅ `docs/ROUTESGO_RESUMO_EXECUTIVO.md` - Resumo executivo
- ✅ `docs/test-routesgo.sh` - Script de teste via cURL
- ✅ `docs/test-osrm-service.php` - Teste via Artisan Tinker
- ✅ `.env.routesgo.example` - Template de configuração

#### Modificado

**Backend:**
- `app/Http/Controllers/Api/RouteDestinationController.php`
  - Adicionado método `calculateRoute()`
  - Adicionados imports `OsrmService` e `Validator`

- `routes/api.php`
  - Adicionada rota `POST /api/destinations/calculate-route`

**Frontend:**
- `src/types/destination.ts`
  - Interface `DestinationWithDistance` estendida com `durationCalculated` e `durationEstimated`
  - Adicionada interface `RouteCalculation`

- `src/services/destinationService.ts`
  - Adicionada constante `TRAFFIC_FACTOR = 1.35`
  - Adicionada função `calculateRouteWithEstimate()`

- `src/screens/destinations/DestinationsListScreen.tsx`
  - Atualizado para usar `calculateRouteWithEstimate()`
  - Modificada renderização de métricas (3 valores)
  - Adicionado estilo `metricValueSecondary`

#### Corrigido
- ❌ **NENHUM BUG ENCONTRADO** - A implementação original estava correta
- ✅ Adicionado sistema de estimativa para aproximar tempo teórico da realidade

---

## Detalhes Técnicos

### Fator de Correção Padrão: 1.35

**Justificativa:**
- OSRM calcula tempo baseado em velocidade teórica
- Não considera trânsito, semáforos, congestionamentos
- Fator 1.35 adiciona 35% ao tempo, aproximando da realidade urbana

**Exemplo:**
```
OSRM:     47 min (teórico)
Estimado: 63 min (com fator 1.35)
Real:     ~70 min (Google Maps em condições normais)
```

### Compatibilidade

- ✅ Laravel 12.x
- ✅ React Native + Expo v54.0.0
- ✅ OSRM API v1
- ✅ Compatibilidade retroativa mantida

### Breaking Changes

❌ **Nenhuma breaking change** - Todas as alterações são compatíveis com código existente.

---

## Como Atualizar

### Backend
```bash
# 1. Adicionar ao .env
echo "ROUTESGO_TRAFFIC_FACTOR=1.35" >> .env
echo "ROUTESGO_DEBUG=false" >> .env

# 2. Limpar cache de configuração
php artisan config:clear
```

### Frontend
```bash
# Nenhuma ação necessária - código atualizado automaticamente
```

---

## Contribuidores

- **Análise:** GitHub Copilot
- **Implementação:** GitHub Copilot
- **Documentação:** GitHub Copilot
- **Testes:** GitHub Copilot

---

## Links

- [Análise Completa](./ROUTESGO_ANALISE.md)
- [Guia Rápido](./ROUTESGO_GUIA_RAPIDO.md)
- [Resumo Executivo](./ROUTESGO_RESUMO_EXECUTIVO.md)

---

**Versão:** 1.1.0  
**Data:** 15/06/2026  
**Status:** ✅ Estável
