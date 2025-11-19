# Perspect Dashboard - Documentazione Tecnica

## Panoramica

Dashboard di analisi finanziaria che trasforma bilanci PDF in visualizzazioni interattive con KPI, grafici e insight AI. Progettato per PMI italiane con bilanci in formato CEE.

---

## Struttura JSON dei Dati

L'LLM deve estrarre i dati dai bilanci PDF e generare il JSON strutturato secondo lo schema in `data-schema.json`.

### Schema Principale

```json
{
  "metadata": {
    "company_name": "Nome Azienda",
    "fiscal_years": ["2023", "2024", "2025"],
    "currency": "EUR",
    "last_update": "YYYY-MM-DD"
  },
  "income_statement": {
    "ricavi": [526000, 553000, 825000],
    "costi_servizi": [241000, 254000, 314000],
    "costi_personale": [198000, 202000, 282000],
    "altri_costi_operativi": [54000, 32000, 30000],
    "ebitda": [40000, 65000, 205000],
    "ammortamenti": [19000, 29000, 26000],
    "ebit": [16000, 33000, 180000],
    "oneri_finanziari": [8000, 21000, 19000],
    "imposte": [6000, 8000, 10000],
    "utile_netto": [3000, 4000, 151000]
  },
  "balance_sheet": {
    "attivo": {
      "immobilizzazioni_immateriali": [0, 0, 1000],
      "immobilizzazioni_materiali": [400000, 436000, 471000],
      "immobilizzazioni_finanziarie": [0, 0, 5000],
      "crediti_commerciali": [150000, 323000, 355000],
      "attivita_finanziarie": [30000, 32000, 43000],
      "disponibilita_liquide": [20000, 7000, 17000],
      "ratei_risconti_attivi": [15000, 5000, 4000],
      "totale_attivo": [615000, 803000, 896000]
    },
    "passivo": {
      "capitale_sociale": [100000, 100000, 100000],
      "riserva_legale": [3000, 4000, 5000],
      "altre_riserve": [10000, 15000, 17000],
      "utili_esercizi_precedenti": [50000, 53000, 61000],
      "utile_esercizio": [3000, 4000, 151000],
      "totale_patrimonio_netto": [166000, 182000, 334000],
      "tfr": [30000, 42000, 54000],
      "debiti_breve_termine": [229000, 393000, 272000],
      "debiti_lungo_termine": [200000, 182000, 234000],
      "ratei_risconti_passivi": [0, 10000, 3000],
      "totale_passivo": [615000, 803000, 896000]
    }
  },
  "cash_flow": {
    "utile_netto": [0, 0, 151000],
    "ammortamenti": [0, 0, 26000],
    "variazione_tfr": [0, 0, 13000],
    "variazione_crediti": [0, 0, 31000],
    "variazione_debiti": [0, 0, -69000],
    "investimenti": [0, 0, -66000],
    "variazione_cassa": [0, 0, 10000],
    "cash_flow_operativo": [62000, 97000, 189000]
  },
  "kpi": {
    "redditivita": {
      "roe": [1.6, 2.0, 45.3],
      "roa": [0.43, 0.45, 16.8],
      "ros": [0.5, 0.7, 18.3],
      "ebitda_margin": [7.6, 11.7, 24.9]
    },
    "liquidita": {
      "current_ratio": [1.92, 0.92, 1.52],
      "quick_ratio": [0.87, 0.84, 1.53],
      "cash_ratio": [0.20, 0.02, 0.06],
      "margine_tesoreria": [-43000, -63000, 100000]
    },
    "solidita": {
      "debt_equity": [2.66, 3.16, 1.52],
      "leverage": [3.80, 4.41, 2.69],
      "icr": [2.2, 1.5, 8.2],
      "dscr": [0, 0, 9.06]
    },
    "efficienza": {
      "dso": [254, 214, 157],
      "dpo": [56, 56, 56],
      "ciclo_finanziario": [142, 142, 101],
      "asset_turnover": [0.86, 0.69, 0.92]
    },
    "rischio": {
      "z_score": [1.85, 1.42, 3.18],
      "z_score_zone": ["grey", "grey", "safe"]
    }
  },
  "executive_summary": {
    "salute_generale": "Buona",
    "aree": {
      "redditivita": {"stato": "Ottima", "valore": "ROE 45.3%"},
      "crescita": {"stato": "Forte", "valore": "+49% Ricavi"},
      "solidita": {"stato": "Buona", "valore": "D/E 1.52x"},
      "liquidita": {"stato": "Attenzione", "valore": "Cash 0.06x"},
      "efficienza": {"stato": "Migliorabile", "valore": "DSO 157gg"},
      "rischio": {"stato": "Basso", "valore": "Z-Score 3.18"}
    }
  },
  "risk_priorities": [
    {
      "priority": "P1",
      "criticita": "alta",
      "titolo": "Migliorare Cash Ratio",
      "target": ">0.2x",
      "azioni": "Accelerare incassi, factoring, linea RBF"
    }
  ],
  "ai_insights": {
    "executive_summary": {
      "negativi": ["Cash Ratio critico", "DSO sopra target"],
      "positivi": ["Ricavi +49%", "EBITDA +217%", "Z-Score zona sicura"]
    }
  }
}
```

> **Nota**: Lo schema completo con tutti i campi è disponibile in `data-schema.json`

---

## Istruzioni per LLM - Estrazione Dati

### Prompt di Sistema

```
Sei un analista finanziario esperto. Devi estrarre dati strutturati da bilanci aziendali italiani in formato CEE (Codice Civile).

REGOLE DI ESTRAZIONE:

1. CONTO ECONOMICO
   - Identifica la sezione "Conto Economico" o "Profit & Loss"
   - Ricavi = Voce A.1 "Ricavi delle vendite e delle prestazioni"
   - Costi per servizi = Voce B.7 "Per servizi"
   - Costi del personale = Voce B.9 "Per il personale"
   - Ammortamenti = Voce B.10 "Ammortamenti e svalutazioni"
   - Oneri finanziari = Voce C.17 "Interessi e altri oneri finanziari"
   - Imposte = Voce 20 "Imposte sul reddito"
   - Utile netto = Voce 21 "Utile (perdita) dell'esercizio"

2. STATO PATRIMONIALE ATTIVO
   - Immobilizzazioni immateriali = Voce B.I
   - Immobilizzazioni materiali = Voce B.II
   - Immobilizzazioni finanziarie = Voce B.III
   - Crediti commerciali = Voce C.II.1 "Crediti verso clienti"
   - Attività finanziarie = Voce C.III
   - Disponibilità liquide = Voce C.IV
   - Ratei e risconti = Voce D

3. STATO PATRIMONIALE PASSIVO
   - Capitale sociale = Voce A.I
   - Riserve = Voci A.II-A.VII
   - Utili/perdite portati a nuovo = Voce A.VIII
   - Utile d'esercizio = Voce A.IX
   - TFR = Voce C
   - Debiti = Voce D (distinguere entro/oltre 12 mesi)
   - Ratei e risconti = Voce E

4. NORMALIZZAZIONE
   - Converti tutti i valori in numeri interi (euro)
   - I costi devono essere valori positivi
   - Le perdite devono essere valori negativi
   - Se un dato non è presente, usa 0
   - Mantieni la coerenza: Totale Attivo = Totale Passivo

5. VALIDAZIONE
   - EBITDA = Ricavi - Costi Servizi - Costi Personale - Altri Costi
   - EBIT = EBITDA - Ammortamenti
   - Verifica: Totale Attivo = Totale Passivo + Patrimonio Netto

OUTPUT: Restituisci SOLO il JSON strutturato, senza commenti.
```

### Prompt di Estrazione

```
Analizza i seguenti bilanci e estrai i dati nel formato JSON specificato.

BILANCI DA ANALIZZARE:
[Inserire qui il contenuto dei PDF]

PERIODI DISPONIBILI: [2023, 2024, 2025]

Estrai tutti i valori per ciascun periodo. Se un periodo non è disponibile, omettilo dall'array "periods" e dai relativi campi dati.

ATTENZIONE:
- I valori tra parentesi () sono negativi
- "migliaia di euro" significa moltiplicare per 1000
- Verifica sempre la quadratura Attivo = Passivo
```

---

## KPI Calcolati

### Redditività
| KPI | Formula | Benchmark |
|-----|---------|-----------|
| ROE | Utile Netto / Patrimonio Netto × 100 | >15% buono |
| ROA | Utile Netto / Totale Attivo × 100 | >5% buono |
| ROS | Utile Netto / Ricavi × 100 | >10% buono |
| EBITDA Margin | EBITDA / Ricavi × 100 | >15% buono |

### Liquidità
| KPI | Formula | Benchmark |
|-----|---------|-----------|
| Current Ratio | Attivo Circolante / Debiti Breve | >1.5x ottimale |
| Quick Ratio | (Attivo Circ. - Rimanenze) / Debiti Breve | >1x ottimale |
| Cash Ratio | Disponibilità Liquide / Debiti Breve | >0.2x ottimale |
| Margine Tesoreria | (Liquidità + Crediti) - Debiti Breve | >0 solvibile |

### Solidità
| KPI | Formula | Benchmark |
|-----|---------|-----------|
| D/E Ratio | Totale Debiti / Patrimonio Netto | <2x per PMI |
| Leverage | Totale Attivo / Patrimonio Netto | <3x ottimale |
| ICR | EBIT / Oneri Finanziari | >3x sicuro |

### Efficienza
| KPI | Formula | Benchmark |
|-----|---------|-----------|
| DSO | (Crediti / Ricavi) × 365 | <90gg ottimale |
| DPO | (Debiti Fornitori / Acquisti) × 365 | 60-90gg |
| Asset Turnover | Ricavi / Totale Attivo | >1x efficiente |

### Rischio
| KPI | Formula | Interpretazione |
|-----|---------|-----------------|
| Z-Score Altman | 0.717×X1 + 0.847×X2 + 3.107×X3 + 0.420×X4 + 0.998×X5 | >2.9 sicuro, 1.23-2.9 grigio, <1.23 rischio |

Dove per Z-Score:
- X1 = Working Capital / Total Assets
- X2 = Retained Earnings / Total Assets
- X3 = EBIT / Total Assets
- X4 = Book Value Equity / Total Liabilities
- X5 = Sales / Total Assets

---

## Struttura File Dashboard

```
controllo_gestione/
├── perspect-dashboard.html    # Dashboard principale
├── assets/
│   ├── css/
│   │   └── styles.css         # Stili custom
│   └── js/
│       └── app.js             # Configurazioni grafici
├── TOOL_DOCUMENTATION.md      # Questa documentazione
└── data/
    └── company_data.json      # Dati estratti (opzionale)
```

---

## Personalizzazione per Nuova Azienda

### Step 1: Estrazione Dati
1. Raccogli i bilanci PDF (minimo 2 anni, ideale 3)
2. Usa il prompt LLM per estrarre il JSON strutturato
3. Valida la quadratura dei dati

### Step 2: Aggiornamento Dashboard
1. In `app.js`, aggiorna i valori nei dataset dei grafici
2. In `perspect-dashboard.html`, aggiorna:
   - Nome azienda nel titolo
   - Valori nei widget
   - Testi negli AI Insight
   - Periodi nelle label

### Step 3: Ricalcolo KPI
Usa le formule sopra per calcolare tutti i KPI derivati.

### Step 4: AI Insights
Genera gli insight contestuali basandoti su:
- Trend positivi/negativi
- Confronto con benchmark di settore
- Aree di attenzione critica
- Raccomandazioni operative

---

## Template Prompt per Generazione Insight

```
Basandoti sui seguenti KPI aziendali, genera un insight sintetico (max 50 parole) che:
1. Evidenzi il dato principale
2. Mostri la variazione vs anno precedente
3. Fornisca un'interpretazione operativa

KPI: [Nome KPI]
Valore attuale: [Valore]
Valore precedente: [Valore]
Benchmark settore: [Valore]

Tono: professionale, diretto, orientato all'azione
Formato: Una frase con icona emoji iniziale
```

---

## Checklist Validazione Dati

- [ ] Totale Attivo = Totale Passivo (per ogni anno)
- [ ] EBITDA = Ricavi - Costi Operativi
- [ ] EBIT = EBITDA - Ammortamenti
- [ ] Utile Netto coerente con Conto Economico
- [ ] Variazioni % calcolate correttamente
- [ ] Nessun valore nullo dove richiesto
- [ ] Periodi ordinati cronologicamente

---

## Dipendenze Tecniche

- **Chart.js** v3.9.1 - Grafici
- **Patternomaly** v1.3.2 - Pattern SVG
- **Tailwind CSS** v3.4 - Styling
- **Font Awesome** v6 - Icone
- **Tablesort** - Ordinamento tabelle

---

## Funzionalità UI

### Tema Scuro
La dashboard supporta un tema chiaro e scuro con toggle nell'header:
- Icona luna/sole per switch tema
- Preferenza salvata in localStorage
- Colori grafici Chart.js adattati automaticamente
- Griglie, testi e bordi ottimizzati per entrambi i temi

### Design Responsive
Layout completamente responsive per dispositivi mobile e tablet:
- Breakpoint: sm (640px), md (768px), lg (1024px)
- Grid adattivi: 1 → 2 → 3/4 colonne
- Altezze grafici ridotte su mobile
- Padding e margin ottimizzati
- Tabelle con scroll orizzontale
- Sidebar con toggle mobile

---

## Estensioni Future

1. **Import automatico** - Parser PDF → JSON
2. **Multi-azienda** - Confronto tra società
3. **Proiezioni** - Forecast con trend analysis
4. **Export** - PDF report generator
5. **API** - Endpoint per integrazione esterna

---

## Contatti e Supporto

Per assistenza nella configurazione o personalizzazione, consultare la documentazione inline nel codice o i commenti nei file sorgente.

---

*Generato con Claude Code - Dashboard di Controllo di Gestione v1.0*
