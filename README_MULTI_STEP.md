# 🚀 Generazione Data Schema JSON - Multi-Step Workflow

Sistema in 3 step per generare `data-schema.json` da file CSV dei bilanci, evitando timeout e ottimizzando costi.

---

## 📊 Overview

```
CSV files (4)
    ↓
[PHP Script] → data-raw.json      (~5KB, <1 sec)
    ↓
[PHP Script] → data-kpi.json      (~15KB, <1 sec)
    ↓
[LLM AI] → data-schema.json       (~80KB, 2-5 min)
```

| Step | Tool | Tempo | Tipo | Output |
|------|------|-------|------|--------|
| 1 | PHP | <1 sec | Estrazione meccanica | data-raw.json |
| 2 | PHP | <1 sec | Calcoli formule | data-kpi.json |
| 3 | LLM | 2-5 min | Analisi ragionamento | data-schema.json |

---

## 🎯 Opzione A: Script PHP (Raccomandato)

### Prerequisiti
- PHP 7.4+ già installato
- File CSV in `docs/bilanci/`

### Step 1: Estrazione Dati Grezzi

```bash
cd /Users/nicola/Documents/GitHub/business-intelligence
php scripts/step1_extract_raw_data.php
```

**Output**: `data-raw.json` con:
- Metadata azienda
- Income statement (ricavi, costi, utile, ecc.)
- Balance sheet (attivo, passivo)
- Calcoli base (EBITDA, altre_attivita_correnti, ecc.)

### Step 2: Calcolo KPI

```bash
php scripts/step2_calculate_kpi.php
```

**Output**: `data-kpi.json` con tutto di Step 1 più:
- KPI Redditività (ROE, ROA, ROS, EBITDA margin, DSCR)
- KPI Liquidità (Current ratio, Quick ratio, Cash ratio)
- KPI Solidità (D/E, Leverage, ICR, Autonomia)
- KPI Efficienza (DSO, DPO, Ciclo finanziario, Asset turnover)
- KPI Rischio (Z-Score Altman)
- Cash Flow (metodo indiretto)
- Structure Margin, Debt Structure, Interest Coverage
- DuPont Analysis, Break Even, Produttività, CAPEX
- Composizione Attivo/Passivo

### Step 3: Analisi con LLM

Usa il tuo LLM preferito (GPT-4, Claude Opus, ecc.) con:
- **Input**: `data-kpi.json`
- **Prompt**: `PROMPT_STEP3_ANALYSIS.md`
- **Output**: `data-schema.json`

#### Esempio con API OpenAI:

```bash
# Installa jq se non ce l'hai (brew install jq su Mac)

# Prepara il prompt
PROMPT=$(cat PROMPT_STEP3_ANALYSIS.md)
DATA=$(cat data-kpi.json)

# Chiama API
curl https://api.openai.com/v1/chat/completions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -d "{
    \"model\": \"gpt-4-turbo-preview\",
    \"messages\": [
      {
        \"role\": \"system\",
        \"content\": \"$PROMPT\"
      },
      {
        \"role\": \"user\",
        \"content\": \"Ecco i dati da analizzare: $DATA\"
      }
    ]
  }" | jq -r '.choices[0].message.content' > data-schema.json
```

#### Esempio con Claude AI:

```bash
# Usa Claude Code o API Anthropic
# Carica data-kpi.json e usa PROMPT_STEP3_ANALYSIS.md
```

---

## 🔄 Opzione B: Workflow n8n

Workflow visuale con 3 nodi per automazione completa.

### Setup n8n

1. **Crea nuovo workflow** in n8n
2. **Aggiungi 3 nodi**:

```
[Schedule Trigger] → [JS Step1] → [JS Step2] → [HTTP Request LLM] → [Write File]
```

### Nodo 1: JavaScript - Extract Raw Data

```javascript
// Leggi tutti i CSV
const fs = require('fs');
const path = require('path');
const csvParse = require('csv-parse/sync');

const csvDir = '/path/to/docs/bilanci/';
const files = fs.readdirSync(csvDir).filter(f => f.endsWith('.csv'));

// Mapping CSV → JSON (stesso mapping dello script PHP)
const mappings = {
  'ricavi': 'A.1 - Valore della produzione, ricavi delle vendite e delle prestazioni',
  // ... (tutti gli altri mapping)
};

// Parsing e aggregazione
const allYears = {};
files.forEach(file => {
  const content = fs.readFileSync(path.join(csvDir, file), 'utf8');
  const records = csvParse.parse(content, { delimiter: ';', from_line: 2 });

  // Estrai anno dal filename
  const year = file.match(/(\d{4})/)[1];

  // Processa record...
  // (logica simile allo script PHP)
});

// Return data-raw.json structure
return [{
  json: {
    metadata: { ... },
    income_statement: { ... },
    balance_sheet: { ... }
  }
}];
```

### Nodo 2: JavaScript - Calculate KPI

```javascript
const rawData = $input.item.json;

// Applica tutte le formule
const kpi = {
  redditivita: {
    roe: rawData.income_statement.utile_netto.map((u, i) =>
      (u / rawData.balance_sheet.passivo.patrimonio_netto_totale[i]) * 100
    ),
    // ... altre formule
  },
  // ... altri KPI
};

// Return data-kpi.json structure
return [{
  json: {
    ...rawData,
    kpi: kpi,
    cash_flow: { ... },
    // ... tutte le altre sezioni calcolate
  }
}];
```

### Nodo 3: HTTP Request - LLM Analysis

```
Method: POST
URL: https://api.openai.com/v1/chat/completions
Headers:
  Authorization: Bearer {{ $env.OPENAI_API_KEY }}
  Content-Type: application/json

Body:
{
  "model": "gpt-4-turbo-preview",
  "messages": [
    {
      "role": "system",
      "content": "{{ $('Read File').item.json.prompt }}"
    },
    {
      "role": "user",
      "content": "{{ $json }}"
    }
  ]
}
```

### Nodo 4: Write File

```
File Path: /path/to/data-schema.json
Content: {{ $json.choices[0].message.content }}
```

### Automazione

- **Trigger**: Schedule (es. ogni giorno alle 2:00 AM)
- **Notifiche**: Aggiungi nodo Slack/Email al completamento
- **Error handling**: Aggiungi nodo Error Trigger per retry

---

## 📁 Struttura File

```
business-intelligence/
├── docs/
│   └── bilanci/
│       ├── IT03731686-2024-Esercizio-1-101239144.csv
│       ├── IT03731686-2023-Esercizio-2-101239144.csv
│       ├── IT03731686-2022-Esercizio-3-101239144.csv
│       └── IT03731686-2021-Esercizio-4-101239144.csv
├── scripts/
│   ├── step1_extract_raw_data.php      ← Estrazione CSV
│   ├── step2_calculate_kpi.php         ← Calcolo KPI
│   └── run_all.sh                      ← Helper script
├── data-raw.json                        ← Output Step 1
├── data-kpi.json                        ← Output Step 2
├── data-schema.json                     ← Output finale Step 3
├── PROMPT_STEP3_ANALYSIS.md            ← Istruzioni per LLM
├── PROMPT_LLM_DATA_GENERATION.md       ← Prompt originale (reference)
├── STRATEGIA_GENERAZIONE_JSON.md       ← Documentazione strategia
└── README_MULTI_STEP.md                ← Questo file
```

---

## 🔧 Helper Script (Opzionale)

Crea `scripts/run_all.sh`:

```bash
#!/bin/bash

echo "🚀 Avvio generazione data-schema.json"
echo ""

# Step 1
echo "📊 Step 1: Estrazione dati da CSV..."
php scripts/step1_extract_raw_data.php
if [ $? -ne 0 ]; then
  echo "❌ Errore in Step 1"
  exit 1
fi

# Step 2
echo ""
echo "🧮 Step 2: Calcolo KPI..."
php scripts/step2_calculate_kpi.php
if [ $? -ne 0 ]; then
  echo "❌ Errore in Step 2"
  exit 1
fi

# Step 3
echo ""
echo "🧠 Step 3: Analisi LLM..."
echo "⚠️  Questo step richiede LLM manuale o API call"
echo "📝 Input: data-kpi.json"
echo "📄 Prompt: PROMPT_STEP3_ANALYSIS.md"
echo "📤 Output: data-schema.json"
echo ""
echo "Esegui:"
echo "  - Via web UI: carica data-kpi.json + prompt in ChatGPT/Claude"
echo "  - Via API: usa lo script di esempio nella documentazione"

echo ""
echo "✅ Step 1-2 completati!"
```

Rendi eseguibile:
```bash
chmod +x scripts/run_all.sh
./scripts/run_all.sh
```

---

## ✅ Vantaggi Multi-Step

| Vantaggio | Descrizione |
|-----------|-------------|
| **NO Timeout** | Ogni step completa in <5 min |
| **NO Blocchi** | Se uno step fallisce, riparti da lì |
| **Costo Minimo** | LLM solo per Step 3 (~€0.10 invece di €0.50) |
| **Velocità** | Step 1-2 istantanei (2 sec totali) |
| **Qualità** | Dati 100% corretti, LLM si concentra su analisi |
| **Debug Facile** | 3 file intermedi ispezionabili |
| **Riutilizzo** | data-kpi.json utile per altri report |

---

## 🧪 Testing

### Test Step 1
```bash
php scripts/step1_extract_raw_data.php
# Verifica: data-raw.json creato, ~5KB
# Controlla: ricavi, patrimonio_netto, ecc.
```

### Test Step 2
```bash
php scripts/step2_calculate_kpi.php
# Verifica: data-kpi.json creato, ~15KB
# Controlla: ROE, Z-Score, DSO, ecc.
```

### Test Step 3
```bash
# Carica data-kpi.json in ChatGPT con PROMPT_STEP3_ANALYSIS.md
# Verifica output ha tutte le sezioni:
# - executive_summary con aree
# - risk_priorities array
# - risk_matrix array
# - ai_insights con 17 campi
# - documents array
```

---

## ❓ FAQ

### Q: Posso usare solo Step 3 con i miei dati CSV?
**A:** No, Step 1-2 sono obbligatori per preparare `data-kpi.json` nel formato corretto.

### Q: Posso modificare i mappings CSV?
**A:** Sì, modifica l'array `$mappings` in `step1_extract_raw_data.php`.

### Q: Quale LLM usare per Step 3?
**A:** GPT-4 Turbo o Claude Opus. Modelli più economici (GPT-3.5, Haiku) potrebbero commettere errori.

### Q: Quanto costa Step 3?
**A:** ~€0.05-0.15 con GPT-4 Turbo (15KB input + 80KB output).

### Q: Posso automatizzare tutto?
**A:** Sì, usa workflow n8n o cron job + API LLM.

### Q: Gli script funzionano su Windows?
**A:** Sì, PHP è cross-platform. Usa `php` invece di `./run_all.sh`.

---

## 🚨 Troubleshooting

### Errore: "File non trovato: data-raw.json"
Esegui prima `php scripts/step1_extract_raw_data.php`.

### Errore: "Nessun file CSV trovato"
Verifica path in `step1_extract_raw_data.php` (linea 12).

### Warning: Division by zero
Normale se alcuni KPI non sono calcolabili (es. debiti finanziari = 0).

### LLM Output incompleto
- Aumenta `max_tokens` dell'API (almeno 4000)
- Usa modello più potente (GPT-4 invece di GPT-3.5)
- Spezza il prompt se troppo lungo

---

## 📞 Supporto

Per problemi:
1. Verifica struttura file CSV
2. Controlla log output degli script PHP
3. Valida JSON intermedi con jsonlint.com
4. Confronta con file di esempio `_data-schema.json`

---

## 🎉 Quick Start

```bash
# 1. Assicurati di avere CSV in docs/bilanci/
ls docs/bilanci/*.csv

# 2. Esegui Step 1-2
php scripts/step1_extract_raw_data.php
php scripts/step2_calculate_kpi.php

# 3. Usa LLM per Step 3
# Carica data-kpi.json + PROMPT_STEP3_ANALYSIS.md in ChatGPT

# 4. Salva output come data-schema.json

# Done! 🎊
```
