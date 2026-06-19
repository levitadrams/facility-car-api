#!/bin/bash

# ================================================
# Script de Teste - RotasGo OSRM
# ================================================
# 
# Este script testa o cálculo de rotas via API
# Substitua o TOKEN pelo seu token de autenticação

# Configurações
API_URL="http://localhost:8000/api"
TOKEN="SEU_TOKEN_AQUI"

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}🧪 Teste RotasGo - Cálculo de Rotas${NC}"
echo -e "${BLUE}================================================${NC}\n"

# Exemplo: Rio de Janeiro (Centro) → Copacabana
ORIGIN_LAT=-22.9083
ORIGIN_LON=-43.1964
DEST_LAT=-22.9711
DEST_LON=-43.1822

echo -e "${YELLOW}📍 Origem:${NC} Lat $ORIGIN_LAT, Lon $ORIGIN_LON"
echo -e "${YELLOW}📍 Destino:${NC} Lat $DEST_LAT, Lon $DEST_LON\n"

echo -e "${BLUE}🚀 Enviando requisição...${NC}\n"

curl -X POST "$API_URL/destinations/calculate-route" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"origin_lat\": $ORIGIN_LAT,
    \"origin_lon\": $ORIGIN_LON,
    \"dest_lat\": $DEST_LAT,
    \"dest_lon\": $DEST_LON
  }" \
  | jq '.'

echo -e "\n${GREEN}✅ Teste concluído!${NC}\n"

# Verificar logs (se ROUTESGO_DEBUG=true)
echo -e "${YELLOW}📋 Últimas linhas do log:${NC}"
tail -5 ../storage/logs/laravel.log | grep OSRM || echo "Nenhum log OSRM encontrado (ROUTESGO_DEBUG=false?)"
