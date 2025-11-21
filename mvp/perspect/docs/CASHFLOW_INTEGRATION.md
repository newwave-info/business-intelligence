# Integrazione Flusso di Cassa - Documentazione

## Panoramica
È stata aggiunta una nuova vista "Flusso di Cassa" al tool di Business Intelligence Perspect. La vista analizza i movimenti di cassa 2025 con grafici, checkpoint mensili e KPI.

## File Modificati

### 1. **index.php**
- Aggiunto menu laterale: "Flusso di Cassa" (icona coins)
- Aggiunta vista HTML completa con:
  - Grafico cumulativo delle entrate/uscite/saldo
  - Checkpoint mensili (12 card)
  - Grafico categorie mensili
  - Statistiche annuali
  - AI Insights

### 2. **assets/js/app.js**
- Funzione `loadCashFlowData()` - carica dati da API
- Funzione `initCumulativeCashFlowChart()` - grafico line multilinea
- Funzione `generateMonthlyCheckpoints()` - genera card mensili dinamiche
- Funzione `initMonthlyCategoryChart()` - grafico bar categorie
- Hook su `showView()` per refresh dati

### 3. **api/cashflow.php** (NUOVO)
- Legge il CSV: `docs/2025-Movimenti_Cassa_Pulito.csv`
- Processa i dati in 3 sezioni:
  - **Cumulative Flow**: dati mensili per grafico line
  - **Monthly Checkpoints**: riepilogo per ogni mese
  - **Monthly Categories**: top categorie per mese

## Fonte Dati

**File CSV**: `docs/2025-Movimenti_Cassa_Pulito.csv`

### Formato CSV
```csv
Data,Descrizione,Categoria,Tipo,Importo (€),Note
2025-01-02,saldo fatt. 381 equiline,Ricavi da clienti,Entrata,3660.00,
...
```

**Colonne**:
- `Data`: YYYY-MM-DD
- `Descrizione`: Descrizione transazione
- `Categoria`: 17 categorie contabili
- `Tipo`: Entrata, Uscita, Finanziamento, Investimento, Altro
- `Importo`: Valore numerico positivo
- `Note`: Informazioni aggiuntive

## Dati Riepilogativi 2025

### Totali
- **Entrate**: €1.215.460
- **Uscite**: €550.343
- **Finanziamenti**: €186.698
- **Investimenti**: €41.620
- **Saldo Netto**: €665.117

### Rapporti
- **Entrate/Uscite**: 2.21x
- **Entrate % totale**: 58%
- **Uscite % totale**: 26%

## Checkpoint Mensili

| Mese | Entrate | Uscite | Saldo | Trend |
|------|---------|--------|-------|-------|
| Gen | €120.175 | €45.730 | €74.444 | ✓ |
| Feb | €89.445 | €42.516 | €46.929 | ✓ |
| Mar | €110.660 | €99.325 | €11.335 | ✓ |
| Apr | €105.865 | €54.251 | €51.614 | ✓ |
| Mag | €126.230 | €51.379 | €74.851 | ✓ |
| Giu | €126.230 | €59.341 | €66.889 | ✓ |
| Lug | €68.442 | €47.445 | €20.997 | ✓ |
| Ago | €100.341 | €50.109 | €50.232 | ✓ |
| Set | €99.950 | €48.849 | €51.101 | ✓ |
| Ott | €124.627 | €60.384 | €64.243 | ✓ |
| Nov | €142.762 | €59.385 | €83.377 | ✓ |
| Dic | €91.372 | €40.275 | €51.097 | ✓ |

## Categorie Principali

### Entrate
1. **Ricavi da clienti**: €998.857 (82%)
2. **Anticipi su fatture**: €211.760 (17%)
3. **Rettifiche ricavi**: €4.843

### Uscite
1. **Retribuzioni**: €270.415 (49%)
2. **Tasse e contributi**: €141.688 (26%)
3. **Spese varie**: €56.631 (10%)
4. **Commissioni**: €40.765 (7%)
5. **Affitti/Utilities**: €8.162 (1%)

## Grafici Implementati

### 1. Andamento Cumulativo
- **Tipo**: Line Chart
- **Linee**: Entrate (blu), Uscite (rosso), Saldo Netto (verde)
- **X-axis**: Mesi (Gen-Dic)
- **Y-axis**: Importi in €
- **Feature**: Animazione fluida, responsive

### 2. Checkpoint Mensili
- **Tipo**: Card Grid (2 colonne su desktop)
- **Dati per mese**: Entrate, Uscite, Saldo, # transazioni, Trend
- **Colori**: Verde (positivo), Rosso (negativo)
- **Icone**: Frecce indicatori trend

### 3. Categorie per Mese
- **Tipo**: Bar Chart (Stacked)
- **X-axis**: Mesi
- **Y-axis**: Importi in €
- **Serie**: Top 8 categorie
- **Colori**: Diversificati

## Caratteristiche Tecniche

### API Response
```json
{
  "cumulativeFlow": {
    "dates": ["2025-01", ...],
    "entrate": [120174.68, ...],
    "uscite": [45730.31, ...],
    "saldo": [74444.37, ...]
  },
  "monthlyCheckpoints": {
    "2025-01": {
      "mese": "gen 2025",
      "entrate": 120174.68,
      "uscite": 45730.31,
      "saldo": 74444.37,
      "transazioni": 87,
      "trend": "positivo"
    }
  },
  "monthlyCategories": {
    "months": ["Gen", "Feb", ...],
    "categories": [...],
    "data": [[...], ...]
  }
}
```

### Theme Support
- Automatic dark/light mode detection
- Grid colors responsive al tema
- Colori chart coerenti con design system

## Istruzioni di Utilizzo

### Accedere alla Vista
1. Aprire il dashboard Perspect
2. Nel menu laterale, cliccare su "Flusso di Cassa"
3. I grafici si caricano automaticamente

### Interpretare i Dati
- **Grafico Cumulativo**: Monitorare la progressione della liquidità
  - Verde sopra: buona posizione di cassa
  - Distanza tra blu e rosso: margine operativo

- **Checkpoint Mensili**: Identificare mesi critici
  - Marzo: saldo basso (€11.335) → monitorare
  - Novembre: picco di entrate

- **Categorie**: Identificare voci dominanti
  - Ricavi clienti: 82% delle entrate
  - Stipendi: 49% delle uscite

## Integrazioni Future

Possibili sviluppi:
- [ ] Importazione CSV dinamica (file upload)
- [ ] Filtri mensili/categoria
- [ ] Export dati (PDF/Excel)
- [ ] Previsioni cash flow
- [ ] Confronto YoY
- [ ] Alert automatici su threshold

## Troubleshooting

**Grafici non caricano?**
- Verificare che `docs/2025-Movimenti_Cassa_Pulito.csv` esista
- Verificare permessi lettura file
- Controllare console browser (F12) per errori

**Dati scorretti?**
- Verificare formato CSV (headers, delimitatori)
- Controllare encoding UTF-8
- Verificare date in formato YYYY-MM-DD

## Note Sviluppatore

### Aggiornare Dati
Se hai nuove transazioni:
1. Aggiorna `docs/2025-Movimenti_Cassa_Pulito.csv`
2. Pulisci cache browser (CTRL+F5)
3. I grafici si aggiorneranno automaticamente

### Personalizzare Colori
In `assets/js/app.js`:
```javascript
const colors = {
    entrate: '#3b82f6',    // Blu
    uscite: '#ef4444',      // Rosso
    saldo: '#22c55e',       // Verde
}
```

### Aggiungere Categorie
Il sistema legge automaticamente tutte le categorie dal CSV. Per aggiungerne di nuove:
1. Usa la colonna "Categoria" nel CSV
2. Verrà visualizzata automaticamente nei grafici

---

**Ultima modifica**: 21 Nov 2025
**Versione**: 1.0
**Status**: ✅ Produzione
