# ⚡ Guia Rápido - Correção RotasGo

## 🎯 O Que Foi Feito

Implementado sistema de **fator de correção** para aproximar o tempo calculado do OSRM da realidade urbana.

**Problema:** OSRM 47 min vs Google Maps 78 min  
**Solução:** Aplicar fator 1.35 (adiciona 35%)  
**Resultado:** 47 min → **63 min** (mais realista)

---

## 📦 Arquivos Criados/Modificados

### Backend (Laravel)
```
✅ config/routesgo.php                          (NOVO)
✅ app/Services/OsrmService.php                 (NOVO)
✅ app/Http/Controllers/Api/RouteDestinationController.php (MODIFICADO)
✅ routes/api.php                               (MODIFICADO)
✅ docs/ROUTESGO_ANALISE.md                     (NOVO)
```

### Frontend (React Native)
```
✅ src/types/destination.ts                     (MODIFICADO)
✅ src/services/destinationService.ts           (MODIFICADO)
✅ src/screens/destinations/DestinationsListScreen.tsx (MODIFICADO)
```

---

## 🚀 Como Usar

### 1. Adicionar no `.env` do Laravel
```env
ROUTESGO_TRAFFIC_FACTOR=1.35
ROUTESGO_DEBUG=true
```

### 2. Testar no App
- Abra a tela "Rotas Inteligentes"
- Veja 3 métricas por destino:
  - 🧭 Distância
  - ⏱️ Tempo Calculado (teórico, riscado)
  - ⏰ **Tempo Estimado** (realista, destaque)

### 3. Ajustar Fator (se necessário)
```env
# Áreas urbanas típicas
ROUTESGO_TRAFFIC_FACTOR=1.35

# Metrópoles congestionadas
ROUTESGO_TRAFFIC_FACTOR=1.50

# Rodovias
ROUTESGO_TRAFFIC_FACTOR=1.10
```

---

## 📊 Novo Endpoint Backend

```http
POST /api/destinations/calculate-route
Authorization: Bearer {token}
Content-Type: application/json

{
  "origin_lat": -22.9083,
  "origin_lon": -43.1964,
  "dest_lat": -22.9068,
  "dest_lon": -43.1729
}
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "distance": 25432.4,
    "duration_calculated": 2815,
    "duration_estimated": 3800,
    "traffic_factor": 1.35
  }
}
```

---

## 🧪 Testar Logs

```bash
# Laravel
tail -f storage/logs/laravel.log | grep OSRM

# Expo
npx expo start
# Verificar console do Metro
```

---

## 📖 Documentação Completa

Ver [docs/ROUTESGO_ANALISE.md](./ROUTESGO_ANALISE.md) para análise técnica detalhada.

---

## ✅ Checklist

- [x] Configuração criada (`config/routesgo.php`)
- [x] Serviço OSRM implementado
- [x] Endpoint backend criado
- [x] Frontend atualizado
- [x] Interface exibe 2 tempos
- [x] Fator configurável via `.env`
- [x] Logs de debug disponíveis
- [x] Documentação completa

---

**Status:** ✅ Pronto para uso  
**Fator padrão:** 1.35 (+35%)  
**Ajustável:** Sim (via `.env`)
