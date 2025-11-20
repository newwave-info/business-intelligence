# Istruzioni per LLM - Generazione Data Schema JSON

## Obiettivo
Compilare il file `data-schema.json` con dati finanziari e valutazioni, assegnando classi CSS appropriate che comunicano il giudizio della salute aziendale.

---

## 🚨 ATTENZIONE - ERRORI CHE FANNO CRASHARE IL PHP

**SE MANCANO QUESTE SEZIONI, IL PHP GENERA ERRORE E LA PAGINA NON FUNZIONA:**

1. **`capex`** → DEVE avere l'array `dettagli` (il PHP fa foreach su questo array alla linea 1247)
2. **`ai_insights`** → DEVE avere TUTTI i 17 campi elencati nella sezione 16
3. **`executive_summary`** → DEVE avere l'oggetto `aree` con sottosezioni (NON un array diretto!)
4. **`risk_priorities`** → DEVE essere un array con almeno 1 elemento
5. **`structure_margin`** → DEVE essere presente con tutti i campi
6. **`debt_structure`** → DEVE essere presente
7. **`interest_coverage`** → DEVE essere presente
8. **`z_score_altman`** → DEVE essere presente
9. **`dupont_analysis`** → DEVE essere presente
10. **`produttivita`** → DEVE essere presente
11. **`risk_matrix`** → DEVE essere presente
12. **`documents`** → DEVE essere presente

**IMPORTANTE**: NON inventare sezioni nuove! Usa ESATTAMENTE la struttura mostrata in questo documento. Se crei sezioni diverse (es. `cash_flow_analysis`, `profitability_analysis`, ecc.), il PHP NON le riconoscerà e mancheranno quelle obbligatorie!

---

## ⚠️ REQUISITI CRITICI

**ENCODING**: Il JSON DEVE essere in UTF-8 valido. I caratteri speciali italiani (à, è, ì, ò, ù, é) devono apparire correttamente nel file, NON come sequenze corrotte tipo "Ã " o "Ã¨".

**STRUTTURA OBBLIGATORIA**: Segui ESATTAMENTE la struttura documentata in questo file. NON creare sezioni alternative o rinominare campi. Il PHP si aspetta ESATTAMENTE i nomi delle sezioni qui documentate.

**VERIFICA FINALE**: Prima di consegnare, controllare la checklist completa nella sezione "Validazione del JSON".

---

## Principio Fondamentale
Il **JSON è il bridge** tra:
- **Upstream**: Analisi AI dei dati finanziari (generazione dati + valutazione)
- **Frontend**: Visualizzazione neutra basata su classi CSS nel JSON

Il PHP rimane **completamente neutrale** e non prende decisioni su colori/stili. Tutte le decisioni sono nel JSON.

---

## Sistema Dinamico per Anni Fiscali

**IMPORTANTE**: Il sistema è completamente dinamico rispetto agli anni fiscali.

- `fiscal_years` nel metadata determina TUTTO
- Può contenere 2, 3, 4, 5+ anni
- Tutti gli array di dati DEVONO avere la stessa lunghezza di `fiscal_years`
- I confronti sono sempre tra **ultimo** e **penultimo** anno

Esempio con 3 anni:
```json
"fiscal_years": ["2023", "2024", "2025"],
"ricavi": [526061, 553475, 824708]  // 3 valori, uno per anno
```

Esempio con 2 anni:
```json
"fiscal_years": ["2024", "2025"],
"ricavi": [553475, 824708]  // 2 valori
```

---

## 🔢 Array vs Numeri Singoli - REGOLE CRITICHE

**Alcune sezioni usano ARRAY (dati storici per tutti gli anni), altre usano NUMERI SINGOLI (solo ultimo anno).**

### ✅ Sezioni con ARRAY (uno per anno fiscale):
- `income_statement` - tutti i campi sono array
- `balance_sheet.attivo` - tutti i campi sono array
- `balance_sheet.passivo` - tutti i campi sono array
- `kpi.redditivita` - tutti i campi sono array
- `kpi.liquidita` - tutti i campi sono array
- `kpi.solidita` - tutti i campi sono array
- `kpi.efficienza` - tutti i campi sono array
- `kpi.rischio` - tutti i campi sono array
- `cash_flow` - tutti i campi sono array
- `structure_margin` - tutti i campi sono array
- `dupont_analysis` - tutti i campi sono array
- `z_score_altman.punteggio` - array
- `z_score_altman.zone` - array
- `produttivita` - la maggior parte dei campi sono array

### ❌ Sezioni con NUMERI SINGOLI (solo ultimo anno o valore calcolato):
- **`break_even`** - TUTTI i campi sono NUMERI SINGOLI (non array!)
  - `costi_fissi` - numero singolo
  - `costi_variabili` - numero singolo
  - `margine_contribuzione` - decimale (es. 0.634)
  - `punto_pareggio` - numero singolo
  - `margine_sicurezza_pct` - numero singolo
  - `margine_sicurezza_eur` - numero singolo
  - `ricavi_XXXX` - numero singolo (dove XXXX è l'ultimo anno fiscale)
- `debt_structure.costo_medio_debito` - numero singolo (percentuale)
- `debt_structure.totale` - array (somma di breve + lungo per ogni anno)
- `interest_coverage.costo_medio_debito` - numero singolo (percentuale)
- `z_score_altman.components_2025` - oggetto con numeri singoli (x1, x2, x3, x4, x5)
- `produttivita.dipendenti_stimati` - numero singolo
- `executive_summary.salute_generale` - stringa
- Tutti i campi `css_class` - stringa

### 🚨 ERRORE COMUNE:
**SBAGLIATO**:
```json
"break_even": {
  "margine_contribuzione": [60.3, 64.08, 60.26, 64.53],  // ARRAY - ERRORE!
  "punto_pareggio": [2089486, 2378159, 2636458, 2547057]  // ARRAY - ERRORE!
}
```

**CORRETTO**:
```json
"break_even": {
  "margine_contribuzione": 0.6453,  // NUMERO SINGOLO (ultimo anno)
  "punto_pareggio": 2547057  // NUMERO SINGOLO (ultimo anno)
}
```

---

## Struttura delle Classi CSS Disponibili

### Classi per Widget Principali

| Classe CSS | Uso | Colore | Quando usare |
|-----------|-----|--------|--------------|
| `widget-positive` | Dati positivi/eccellenti | Verde gradiente | ROE alto, ricavi in crescita, liquidità buona |
| `widget-negative` | Dati negativi/critici | Rosso gradiente | Cash Ratio basso, DSO sopra target |
| `widget-purple` | Dati neutrali/informativi | Viola gradiente | Informazioni generali, rischi medi |

### Classi per Sub-Widget Executive Summary

| Classe CSS | Uso | Colore | Quando usare |
|-----------|-----|--------|--------------|
| `subwidget-neutral` | Stati positivi/neutri | Viola sfumato con bordo | Ottima, Forte, Buona, Basso, Accettabile |
| `subwidget-negative` | Stati critici | Rosso sfumato con bordo | Attenzione, Migliorabile, Critico |

---

## Struttura Completa del JSON

### 1. Metadata (obbligatorio)

```json
{
  "metadata": {
    "company_name": "Nome Azienda srl",
    "fiscal_years": ["2023", "2024", "2025"],
    "currency": "EUR",
    "last_update": "18 nov 2025",
    "notes": "Note opzionali sull'azienda o sui dati"
  }
}
```

### 2. Income Statement

Tutti gli array devono avere N elementi (uno per anno fiscale).

```json
{
  "income_statement": {
    "ricavi": [526061, 553475, 824708],
    "costi_servizi": [241142, 254242, 314464],
    "costi_personale": [198004, 202405, 282113],
    "altri_costi_operativi": [53785, 31995, 29520],
    "ebitda": [40039, 64833, 205335],
    "ammortamenti": [18921, 28687, 25621],
    "ebit": [16311, 32605, 180049],
    "oneri_finanziari": [7774, 21104, 18948],
    "imposte": [5871, 7861, 10424],
    "utile_netto": [2666, 3640, 150677]
  }
}
```

### 3. Balance Sheet

```json
{
  "balance_sheet": {
    "attivo": {
      "immobilizzazioni_totale": [285000, 420000, 477000],
      "crediti_commerciali": [310000, 380000, 450000],
      "attivita_finanziarie": [15000, 20000, 25000],
      "disponibilita_liquide": [25000, 35000, 30000],
      "totale_attivo": [635000, 855000, 982000]
    },
    "passivo": {
      "totale_patrimonio_netto": [182000, 185000, 333000],
      "tfr": [45000, 52000, 58000],
      "debiti_breve_termine": [308000, 468000, 401000],
      "debiti_lungo_termine": [100000, 150000, 190000],
      "totale_passivo": [635000, 855000, 982000]
    }
  }
}
```

### 4. KPI

```json
{
  "kpi": {
    "redditivita": {
      "roe": [1.5, 2.0, 45.3],
      "ros": [0.5, 0.7, 18.3],
      "ebitda_margin": [7.6, 11.7, 24.9],
      "dscr": [0.8, 1.2, 2.5]
    },
    "liquidita": {
      "current_ratio": [1.14, 0.92, 1.52],
      "cash_ratio": [0.08, 0.07, 0.06],
      "margine_tesoreria": [-50000, -80000, 45000]
    },
    "efficienza": {
      "dso": [180, 145, 157],
      "dpo": [90, 85, 80],
      "ciclo_finanziario": [142, 120, 101]
    },
    "rischio": {
      "z_score": [1.85, 1.42, 3.18],
      "d_e_ratio": [2.5, 3.17, 1.52]
    }
  }
}
```

### 5. Executive Summary (OBBLIGATORIO)

**🚨 ATTENZIONE STRUTTURA CRITICA**:
- Questa sezione DEVE avere l'oggetto `aree` come mostrato sotto
- NON usare un array diretto per executive_summary
- La struttura corretta è: `executive_summary.aree.liquidita`, `executive_summary.aree.efficienza`, ecc.
- Se usi una struttura diversa (es. executive_summary come array), il PHP crolla!

**IMPORTANTE**:
- Usare `subwidget-neutral` e `subwidget-negative` per le aree
- **ORDINAMENTO**: Gli elementi con `subwidget-negative` devono essere SEMPRE elencati PRIMA di quelli con `subwidget-neutral`

```json
{
  "executive_summary": {
    "salute_generale": "Buona",
    "aree": {
      "liquidita": {
        "stato": "Attenzione",
        "valore": "Cash 0.06x",
        "css_class": "subwidget-negative"
      },
      "efficienza": {
        "stato": "Migliorabile",
        "valore": "DSO 157gg",
        "css_class": "subwidget-negative"
      },
      "redditivita": {
        "stato": "Ottima",
        "valore": "ROE 45.3%",
        "css_class": "subwidget-neutral"
      },
      "crescita": {
        "stato": "Forte",
        "valore": "+49% Ricavi",
        "css_class": "subwidget-neutral"
      },
      "solidita": {
        "stato": "Buona",
        "valore": "D/E 1.52x",
        "css_class": "subwidget-neutral"
      },
      "rischio": {
        "stato": "Basso",
        "valore": "Z-Score 3.18",
        "css_class": "subwidget-neutral"
      }
    }
  }
}
```

**Mapping `stato` → `css_class` per Executive Summary:**
- "Ottima", "Forte", "Buona", "Basso", "Accettabile" → `subwidget-neutral`
- "Attenzione", "Migliorabile", "Critico" → `subwidget-negative`

### 6. Risk Priorities

**IMPORTANTE - ORDINAMENTO**: Gli elementi con `widget-negative` (criticità alta) devono essere elencati PRIMA di quelli con `widget-purple` o `widget-positive` (criticità media/bassa).

```json
{
  "risk_priorities": [
    {
      "priority": "P1",
      "criticita": "alta",
      "titolo": "Migliorare Cash Ratio",
      "target": ">0.2x (attuale 0.06x)",
      "azioni": "Accelerare incassi, factoring, linea RBF",
      "cassa_liberabile": 75000,
      "css_class": "widget-negative"
    },
    {
      "priority": "P2",
      "criticita": "alta",
      "titolo": "Ridurre DSO a 120 giorni",
      "target": "120gg (attuale 157gg)",
      "azioni": "Sconti pagamento anticipato, credit management",
      "miglioramento": "27%",
      "css_class": "widget-negative"
    },
    {
      "priority": "P3",
      "criticita": "media",
      "titolo": "Diversificare clienti",
      "target": "Top 3 <50%",
      "azioni": "Sviluppo commerciale, nuovi mercati",
      "css_class": "widget-purple"
    }
  ]
}
```

### 7. Cash Flow

```json
{
  "cash_flow": {
    "autofinanziamento": [21587, 32327, 189022],
    "delta_capitale_circolante": [40000, 30000, 100000],
    "investimenti": [-50000, -160000, -82621],
    "delta_debiti_finanziari": [-10000, 100000, -200000]
  }
}
```

### 8. Structure Margin

```json
{
  "structure_margin": {
    "patrimonio_netto": [182000, 185000, 333000],
    "immobilizzazioni": [285000, 420000, 477000],
    "margine": [-103000, -235000, -144000],
    "stato": ["Negativo", "Negativo", "Negativo"]
  }
}
```

### 9. Debt Structure

```json
{
  "debt_structure": {
    "breve_termine": [308000, 468000, 401000],
    "lungo_termine": [100000, 150000, 190000],
    "totale": [408000, 618000, 591000],
    "breve_pct": [75, 76, 68],
    "lungo_pct": [25, 24, 32],
    "costo_medio_debito": 4.33
  }
}
```

**NOTA**:
- `breve_termine`, `lungo_termine`, `totale`, `breve_pct`, `lungo_pct` sono array (uno per anno)
- `costo_medio_debito` è un **numero singolo** (percentuale media, es. 4.33 = 4.33%)
- `totale` = breve_termine + lungo_termine per ogni anno

### 10. Interest Coverage

```json
{
  "interest_coverage": {
    "ebit": [16311, 32605, 180049],
    "oneri_finanziari": [7774, 21104, 18948],
    "icr": [2.1, 1.5, 9.5],
    "costo_medio_debito": 3.2
  }
}
```

**NOTA**:
- `ebit`, `oneri_finanziari`, `icr` sono array (uno per anno)
- `costo_medio_debito` è un **numero singolo** (percentuale media, es. 3.2 = 3.2%)

### 11. DuPont Analysis

```json
{
  "dupont_analysis": {
    "margine_netto": [0.5, 0.7, 18.3],
    "rotazione_attivo": [0.83, 0.65, 0.84],
    "leva_finanziaria": [3.49, 4.62, 2.95],
    "roa": [0.4, 0.4, 15.3]
  }
}
```

### 12. Break Even

**IMPORTANTE**: Tutti i campi in questa sezione devono essere **numeri singoli** (solo ultimo anno), **NON array**!

```json
{
  "break_even": {
    "costi_fissi": 350000,
    "costi_variabili": 302000,
    "margine_contribuzione": 0.634,
    "punto_pareggio": 552000,
    "margine_sicurezza_pct": 33,
    "margine_sicurezza_eur": 273000,
    "ricavi_2025": 824708
  }
}
```

**NOTA**:
- `margine_contribuzione` è un decimale (es. 0.634 = 63.4%), NON una percentuale già moltiplicata per 100
- `ricavi_2025` deve essere sostituito con `ricavi_XXXX` dove XXXX è l'ultimo anno fiscale
- Se mancano dati per calcolare alcuni valori, usa 0 come default

### 13. Z-Score Altman

```json
{
  "z_score_altman": {
    "punteggio": [1.85, 1.42, 3.18],
    "zone": ["grey", "grey", "safe"],
    "components_2025": {
      "x1": 0.08,
      "x2": 0.15,
      "x3": 0.18,
      "x4": 0.22,
      "x5": 0.84
    }
  }
}
```

**Mapping `zone` → interpretazione:**
- "safe" (>2.9) → Zona sicura
- "grey" (1.23-2.9) → Zona grigia
- "risk" (<1.23) → Zona rischio

### 14. Produttività

```json
{
  "produttivita": {
    "dipendenti": [8, 10, 12],
    "dipendenti_stimati": 12,
    "ricavi_per_dipendente": [65757, 55347, 68725],
    "valore_aggiunto_per_dipendente_euro": [33130, 36428, 59611]
  }
}
```

**NOTA**:
- `dipendenti`, `ricavi_per_dipendente`, `valore_aggiunto_per_dipendente_euro` sono array (uno per anno)
- `dipendenti_stimati` è un **numero singolo** (stima dell'organico attuale)

### 15. CAPEX (OBBLIGATORIO)

**IMPORTANTE**: Questa sezione è OBBLIGATORIA. Il PHP esegue un foreach su `capex.dettagli` - se manca, genera errore.

La sezione deve contenere:
- `periodi`: array di stringhe con i periodi (es. "2023-2024")
- `valori`: array di numeri con i valori CAPEX per ogni periodo
- `dettagli`: array di oggetti con informazioni dettagliate per ogni periodo

```json
{
  "capex": {
    "periodi": ["2023-2024", "2024-2025"],
    "valori": [319000, 66000],
    "dettagli": [
      {
        "periodo": "2023-2024",
        "totale": 319000,
        "delta_immobilizzazioni": 291000,
        "ammortamenti": 29000,
        "nota": "Ciclo investimento pesante"
      },
      {
        "periodo": "2024-2025",
        "totale": 66000,
        "delta_immobilizzazioni": 41000,
        "ammortamenti": 26000,
        "nota": "Riduzione 79% vs periodo precedente"
      }
    ]
  }
}
```

**Calcolo CAPEX**: Per ogni periodo, il CAPEX si calcola come:
- `delta_immobilizzazioni` = Immobilizzazioni(anno_corrente) - Immobilizzazioni(anno_precedente)
- `totale` = delta_immobilizzazioni + ammortamenti del periodo

### 16. AI Insights (OBBLIGATORIO)

Tutti i testi narrativi devono essere nel JSON, non hardcoded nel PHP.

```json
{
  "ai_insights": {
    "executive_summary": {
      "negativi": [
        "Cash Ratio critico a 0.06x richiede azione immediata",
        "DSO a 157 giorni sopra target 120gg"
      ],
      "positivi": [
        "ROE eccezionale al 45.3% (+43pp vs anno precedente)",
        "Ricavi +49% con margini in forte espansione",
        "Z-Score 3.18 in zona sicura"
      ]
    },
    "leva_finanziaria": "Il D/E sceso da 3.17x a 1.52x (-52%) indica un riequilibrio della struttura finanziaria...",
    "efficienza_costi_dso": "I costi sono scesi dal 82.5% al 72.3% dei ricavi (-10.2pp)...",
    "crescita_ricavi": "Ricavi +49% (€825k) con EBITDA +216% (€205k)...",
    "profittabilita": "EBIT +452% (€180k) e Utile Netto +4039% (€151k)...",
    "ciclo_capitale": "Il ciclo finanziario è sceso del 29% (da 142 a 101 giorni)...",
    "liquidita_ratios": "Il Current Ratio è migliorato al 1.52x (+65% vs anno precedente)...",
    "cash_flow_waterfall": "Il flusso operativo è positivo (€289k = €189k autofinanziamento + €100k Δ capitale circolante)...",
    "cash_flow_trend": "Il CFO è triplicato nel triennio (€62k → €189k, +205%)...",
    "struttura_debiti": "Mix migliorato di 14pp vs anno precedente (da 68% a 54% breve termine)...",
    "sostenibilita_debito": "ICR da 1.5x a 8.2x (+447%)...",
    "margine_struttura": "Il margine di struttura è migliorato del 43% (da -€254k a -€144k)...",
    "performance_economica": "I ricavi sono cresciuti del 49% (€825k)...",
    "solidita_patrimoniale": "Il patrimonio netto è cresciuto dell'83% (€333k)...",
    "break_even": "I ricavi possono scendere del 33% (€273k) prima di entrare in perdita.",
    "produttivita": "L'EBITDA per dipendente è aumentato del 127% (€73k)...",
    "capex": "I CAPEX sono calati del 79% vs anno precedente...",
    "dupont": "Il ROE è trainato dal margine netto (+17.6pp vs anno precedente)...",
    "z_score": "Il punteggio Z-Score di 3.18 posiziona l'azienda in ZONA SICURA..."
  }
}
```

### 17. Risk Matrix (OBBLIGATORIO)

Array di categorie di rischio con livelli e indicatori.

```json
{
  "risk_matrix": [
    {
      "categoria": "Rischio Operativo",
      "livello": "Basso",
      "indicatori": [
        "EBITDA 24.9% (+13.2pp vs 2024)",
        "Break-even +33%",
        "Leva operativa controllata"
      ],
      "css_class": "widget-positive"
    },
    {
      "categoria": "Rischio Finanziario",
      "livello": "Basso",
      "indicatori": [
        "D/E 1.52x (-52% vs 2024)",
        "ICR 8.2x (+447% vs 2024)",
        "Struttura debiti riequilibrata"
      ],
      "css_class": "widget-positive"
    },
    {
      "categoria": "Rischio Liquidità",
      "livello": "Medio",
      "indicatori": [
        "Cash Ratio 0.06x critico",
        "DSO 157gg sopra target",
        "Current Ratio 1.52x ok"
      ],
      "css_class": "widget-negative"
    },
    {
      "categoria": "Rischio Concentrazione",
      "livello": "Info",
      "indicatori": [
        "Dipendenza top client da verificare",
        "Mix settoriale da analizzare"
      ],
      "css_class": "widget-purple"
    }
  ]
}
```

**Mapping `livello` → `css_class`:**
- "Basso" → `widget-positive`
- "Medio" / "Moderato" → `widget-negative` o `widget-purple` (a seconda della gravità)
- "Alto" / "Critico" → `widget-negative`
- "Info" → `widget-purple`

### 18. Concentrazione Ricavi (opzionale)

```json
{
  "concentrazione_ricavi": {
    "clienti": ["Cliente A", "Cliente B", "Cliente C", "Cliente D", "Cliente E", "Altri"],
    "valori_ultimo_anno": [314464, 282113, 25621, 13000, 10000, 7000],
    "percentuali_ultimo_anno": [47.3, 42.4, 3.8, 1.9, 1.5, 1.0]
  }
}
```

### 19. Composizione Attivo/Passivo/Costi (opzionali)

Queste sezioni forniscono dati per grafici di composizione:

```json
{
  "composizione_attivo": {
    "labels": ["Immob. Materiali", "Crediti", "Attività Fin.", "Liquidità", "Immob. Fin.", "Ratei", "Immob. Immat."],
    "valori_2025": [471000, 355000, 43000, 17000, 5000, 4000, 1000],
    "percentuali_2025": [52.6, 39.6, 4.8, 1.9, 0.6, 0.4, 0.1]
  },
  "composizione_passivo": {
    "labels": ["Debiti Breve", "Debiti Lungo", "Utile Esercizio", "Capitale", "Utili a Nuovo", "TFR", "Altre Riserve", "Riserva Legale", "Ratei"],
    "valori_2025": [272300, 233700, 151000, 100000, 61000, 54100, 17000, 5000, 3000],
    "percentuali_2025": [30.4, 26.1, 16.8, 11.2, 6.8, 6.0, 1.9, 0.6, 0.3]
  },
  "composizione_costi": {
    "labels": ["Servizi", "Personale", "Ammortamenti", "Oneri Diversi", "Godim. Beni", "Materie Prime"],
    "valori_2025": [314464, 282113, 25621, 13000, 10000, 7000],
    "percentuali_2025": [47.3, 42.4, 3.8, 1.9, 1.5, 1.0]
  }
}
```

**NOTA**: Sostituire "2025" con l'ultimo anno fiscale disponibile (es. "2024", "2023", ecc.).

### 20. Documents (OBBLIGATORIO)

```json
{
  "documents": [
    {
      "name": "Bilancio 2025",
      "type": "Bilancio Integrale",
      "date": "2025-05-31",
      "size": "2.4 MB",
      "url": "#"
    }
  ]
}
```

---

## Processo di Compilazione

### Step 1: Estrarre dati finanziari
- Leggere bilanci, conto economico, cash flow
- Definire `fiscal_years` nel metadata
- Popolare tutti gli array con N elementi (uno per anno)

### Step 2: Calcolare metriche derivate
- Calcolare KPI dai dati: ROE, ROA, ROS, ecc.
- Eseguire analisi DuPont, break-even, Z-Score
- Tutti i calcoli usano dati dall'ultimo anno

### Step 3: Valutare lo stato (AI judgment)
- Per ogni metrica, assegnare uno `stato`
- Basarsi su:
  - Confronto ultimo vs penultimo anno
  - Benchmarks settoriali
  - Target aziendali
  - Regole finanziarie

### Step 4: Assegnare css_class
- **Executive Summary**: usare `subwidget-neutral` o `subwidget-negative`
- **Altri widget**: usare `widget-positive`, `widget-negative`, `widget-purple`

### Step 5: Generare insight AI
- Popolare TUTTI i campi di `ai_insights`
- Usare riferimenti relativi ("anno precedente") non assoluti ("2024")
- Includere percentuali e valori concreti

---

## Validazione del JSON

Prima di consegnare il JSON, verificare **OGNI SINGOLO PUNTO** di questa checklist:

### ✅ Struttura e Sintassi
- [ ] JSON è valido sintatticamente (nessuna virgola finale, parentesi bilanciate)
- [ ] Encoding UTF-8 corretto (caratteri à, è, ì, ò, ù, é visualizzati correttamente, NON come "Ã " o "Ã¨")
- [ ] `fiscal_years` definito correttamente nel metadata
- [ ] Tutti gli array di dati storici hanno lunghezza = numero di anni fiscali
- [ ] Nessun campo undefined, null o mancante dove non previsto
- [ ] Array vs Numeri Singoli: verificato che ogni sezione usi il tipo corretto (vedi sezione "Array vs Numeri Singoli")

### ✅ Sezioni Obbligatorie Presenti (CRITICO - PHP crolla se mancano!)
- [ ] `metadata` con `company_name`, `fiscal_years`, `currency`, `last_update`
- [ ] `income_statement` completo con tutti i campi
- [ ] `balance_sheet` con `attivo` e `passivo` completi
- [ ] `kpi` con tutte le sottosezioni: `redditivita`, `liquidita`, `solidita`, `efficienza`, `rischio`
- [ ] `executive_summary` con `salute_generale` e **oggetto** `aree` (NON array!)
- [ ] `risk_priorities` array con almeno 1 elemento
- [ ] `cash_flow` con tutti i campi
- [ ] `structure_margin` con tutti i campi
- [ ] `debt_structure` completo
- [ ] `interest_coverage` completo
- [ ] `dupont_analysis` completo
- [ ] `break_even` completo (NUMERI SINGOLI, non array!)
- [ ] `z_score_altman` con `punteggio`, `zone`, `components_2025`
- [ ] `produttivita` completa
- [ ] `capex` con `periodi`, `valori` e **array** `dettagli` (CRITICO!)
- [ ] `ai_insights` con TUTTI i 17 campi richiesti (CRITICO!)
- [ ] `risk_matrix` array con almeno 3-4 elementi
- [ ] `documents` array (anche vuoto va bene, ma deve esistere)

### ✅ Struttura Executive Summary (CRITICO!)
- [ ] `executive_summary` è un oggetto (NON un array!)
- [ ] Ha il campo `salute_generale`
- [ ] Ha il campo `aree` che è un oggetto
- [ ] Ogni area in `aree` ha: `stato`, `valore`, `css_class`
- [ ] Usa `subwidget-neutral` o `subwidget-negative` per le aree
- [ ] Elementi `subwidget-negative` sono elencati PRIMA di `subwidget-neutral`

### ✅ Struttura CAPEX (CRITICO!)
- [ ] Campo `capex` esiste
- [ ] Ha l'array `periodi` con almeno 1 periodo
- [ ] Ha l'array `valori` con stessa lunghezza di `periodi`
- [ ] Ha l'array `dettagli` con stessa lunghezza di `periodi`
- [ ] Ogni elemento in `dettagli` ha: `periodo`, `totale`, `delta_immobilizzazioni`, `ammortamenti`, `nota`

### ✅ Struttura Break Even (CRITICO!)
- [ ] Campo `break_even` esiste
- [ ] TUTTI i campi sono NUMERI SINGOLI (NON array!)
- [ ] Ha `costi_fissi` come numero singolo
- [ ] Ha `costi_variabili` come numero singolo
- [ ] Ha `margine_contribuzione` come decimale (es. 0.6453, NON 64.53)
- [ ] Ha `punto_pareggio` come numero singolo
- [ ] Ha `margine_sicurezza_pct` come numero singolo
- [ ] Ha `margine_sicurezza_eur` come numero singolo
- [ ] Ha `ricavi_XXXX` (con anno fiscale corretto) come numero singolo

### ✅ Struttura Debt Structure
- [ ] Campo `debt_structure` esiste
- [ ] `breve_termine` è un array (uno per anno)
- [ ] `lungo_termine` è un array (uno per anno)
- [ ] `totale` è un array (somma di breve + lungo per ogni anno)
- [ ] `breve_pct` è un array (uno per anno)
- [ ] `lungo_pct` è un array (uno per anno)
- [ ] `costo_medio_debito` è un NUMERO SINGOLO (NON array!)

### ✅ Struttura Interest Coverage
- [ ] Campo `interest_coverage` esiste
- [ ] `ebit` è un array (uno per anno)
- [ ] `oneri_finanziari` è un array (uno per anno)
- [ ] `icr` è un array (uno per anno)
- [ ] `costo_medio_debito` è un NUMERO SINGOLO (NON array!)

### ✅ Struttura Produttività
- [ ] Campo `produttivita` esiste
- [ ] `dipendenti` è un array (uno per anno)
- [ ] `ricavi_per_dipendente` è un array (uno per anno)
- [ ] `valore_aggiunto_per_dipendente_euro` è un array (uno per anno)
- [ ] `dipendenti_stimati` è un NUMERO SINGOLO (NON array!)

### ✅ Struttura AI Insights (CRITICO!)
- [ ] Campo `ai_insights` esiste
- [ ] Ha l'oggetto `executive_summary` con array `negativi` e `positivi`
- [ ] Ha TUTTI questi campi: `leva_finanziaria`, `efficienza_costi_dso`, `crescita_ricavi`, `profittabilita`, `ciclo_capitale`, `liquidita_ratios`, `cash_flow_waterfall`, `cash_flow_trend`, `struttura_debiti`, `sostenibilita_debito`, `margine_struttura`, `performance_economica`, `solidita_patrimoniale`, `break_even`, `produttivita`, `capex`, `dupont`, `z_score`
- [ ] Ogni campo contiene testo significativo (NON vuoto, NON placeholder)

### ✅ Classi CSS e Ordinamento
- [ ] Executive Summary usa SOLO `subwidget-neutral` o `subwidget-negative`
- [ ] Executive Summary: elementi `subwidget-negative` elencati PRIMA di `subwidget-neutral`
- [ ] Risk Priorities: elementi `widget-negative` (criticità alta) elencati PRIMA di altri
- [ ] Altri widget usano `widget-positive`, `widget-negative`, `widget-purple`
- [ ] Nessuna classe CSS inventata o non documentata

### ✅ Consistenza Dati
- [ ] Totale attivo = Totale passivo (per ogni anno fiscale)
- [ ] Tutti i valori numerici sono ragionevoli (non negativi dove non ha senso)
- [ ] I valori calcolati (ROE, ROA, margini, ecc.) sono coerenti con i dati di base
- [ ] Le percentuali sommano a 100 dove appropriato (composizione_attivo, ecc.)
- [ ] I confronti anno su anno usano indici corretti (ultimo vs penultimo anno)

### ✅ Riferimenti Temporali
- [ ] Gli `ai_insights` usano riferimenti relativi ("anno precedente", "ultimo anno") NON assoluti ("2024", "2023")
- [ ] Le note sono aggiornate alla data corrente
- [ ] Gli anni fiscali sono in ordine crescente

---

## Errori Comuni da Evitare

### 🚫 ERRORE FATALE #1: Struttura Executive Summary sbagliata
**SBAGLIATO** (causa errore PHP):
```json
"executive_summary": [
  {
    "title": "Liquidità",
    "value": "0.06x",
    "description": "...",
    "css_class": "subwidget-negative"
  }
]
```

**CORRETTO**:
```json
"executive_summary": {
  "salute_generale": "Buona",
  "aree": {
    "liquidita": {
      "stato": "Attenzione",
      "valore": "Cash 0.06x",
      "css_class": "subwidget-negative"
    }
  }
}
```

### 🚫 ERRORE FATALE #2: Sezione CAPEX mancante o senza "dettagli"
**SBAGLIATO** (causa errore PHP alla linea 1247):
```json
// Sezione capex completamente assente
// OPPURE:
"capex": {
  "periodi": ["2023-2024"],
  "valori": [100000]
  // MANCA l'array "dettagli"!
}
```

**CORRETTO**:
```json
"capex": {
  "periodi": ["2023-2024", "2024-2025"],
  "valori": [319000, 66000],
  "dettagli": [
    {
      "periodo": "2023-2024",
      "totale": 319000,
      "delta_immobilizzazioni": 291000,
      "ammortamenti": 29000,
      "nota": "Ciclo investimento pesante"
    },
    {
      "periodo": "2024-2025",
      "totale": 66000,
      "delta_immobilizzazioni": 41000,
      "ammortamenti": 26000,
      "nota": "Riduzione investimenti"
    }
  ]
}
```

### 🚫 ERRORE FATALE #3: Inventare sezioni non documentate
**SBAGLIATO** (il PHP non riconosce queste sezioni):
```json
{
  "cash_flow_analysis": { ... },  // NON ESISTE nel template!
  "profitability_analysis": { ... },  // NON ESISTE nel template!
  "growth_analysis": { ... },  // NON ESISTE nel template!
  "financial_health": { ... },  // NON ESISTE nel template!
  "working_capital_management": { ... }  // NON ESISTE nel template!
}
```

**CORRETTO** (usa SOLO le sezioni documentate in questo file):
```json
{
  "cash_flow": { ... },  // Documentato nella sezione 7
  "dupont_analysis": { ... },  // Documentato nella sezione 11
  "break_even": { ... },  // Documentato nella sezione 12
  "ai_insights": {  // Documentato nella sezione 16
    "profittabilita": "...",
    "crescita_ricavi": "...",
    ...
  }
}
```

### 🚫 ERRORE FATALE #4: Usare array invece di numeri singoli in break_even
**SBAGLIATO** (causa errore PHP "Unsupported operand types: array * int"):
```json
"break_even": {
  "costi_fissi": [1260234, 1524418, 1588711, 1643491],  // ARRAY - ERRORE!
  "costi_variabili": [1236218, 1308404, 1739844, 1693308],  // ARRAY - ERRORE!
  "margine_contribuzione": [60.3, 64.08, 60.26, 64.53],  // ARRAY - ERRORE!
  "punto_pareggio": [2089486, 2378159, 2636458, 2547057]  // ARRAY - ERRORE!
}
```

**CORRETTO** (usa SOLO valori dell'ultimo anno):
```json
"break_even": {
  "costi_fissi": 1643491,  // NUMERO SINGOLO
  "costi_variabili": 1693308,  // NUMERO SINGOLO
  "margine_contribuzione": 0.6453,  // DECIMALE (non percentuale!)
  "punto_pareggio": 2547057,  // NUMERO SINGOLO
  "margine_sicurezza_pct": 46,  // NUMERO SINGOLO
  "margine_sicurezza_eur": 2226199,  // NUMERO SINGOLO
  "ricavi_2024": 4773256  // NUMERO SINGOLO (usa anno fiscale corretto)
}
```

### 🚫 ERRORE FATALE #5: Usare array per costo_medio_debito e dipendenti_stimati
**SBAGLIATO** (causa errori PHP in operazioni matematiche):
```json
"debt_structure": {
  "breve_termine": [229000, 393000, 272300],
  "lungo_termine": [200000, 182000, 233700],
  "costo_medio_debito": [4.5, 4.2, 4.33]  // ARRAY - ERRORE!
},
"interest_coverage": {
  "ebit": [16311, 32605, 180049],
  "oneri_finanziari": [7774, 21104, 18948],
  "icr": [2.2, 1.5, 8.2],
  "costo_medio_debito": [3.5, 3.0, 3.2]  // ARRAY - ERRORE!
},
"produttivita": {
  "dipendenti": [198, 203, 283],
  "dipendenti_stimati": [12, 12, 12]  // ARRAY - ERRORE!
}
```

**CORRETTO**:
```json
"debt_structure": {
  "breve_termine": [229000, 393000, 272300],
  "lungo_termine": [200000, 182000, 233700],
  "totale": [429000, 575000, 506000],
  "breve_pct": [53, 68, 54],
  "lungo_pct": [47, 32, 46],
  "costo_medio_debito": 4.33  // NUMERO SINGOLO
},
"interest_coverage": {
  "ebit": [16311, 32605, 180049],
  "oneri_finanziari": [7774, 21104, 18948],
  "icr": [2.2, 1.5, 8.2],
  "costo_medio_debito": 3.2  // NUMERO SINGOLO
},
"produttivita": {
  "dipendenti": [198, 203, 283],
  "dipendenti_stimati": 12  // NUMERO SINGOLO
}
```

### 🚫 Altri Errori Comuni

1. **Array di lunghezza diversa**: TUTTI gli array di dati finanziari devono avere N elementi (uno per anno fiscale)
2. **Classi CSS sbagliate per Executive Summary**: usare `subwidget-*` NON `widget-*`
3. **ai_insights incompleti**: il PHP si aspetta TUTTI i 17 campi elencati nella sezione 16
4. **Riferimenti assoluti agli anni**: usare "anno precedente" NON "2024" negli ai_insights
5. **Dati inconsistenti**: totale attivo deve = totale passivo per ogni anno
6. **Ordinamento errato**: elementi critici (negative/rossi) devono essere elencati PRIMA di quelli positivi/neutri
7. **Encoding caratteri speciali**: Il JSON DEVE essere UTF-8 valido, non accettare corruzioni tipo "Ã " invece di "à"
8. **Campi mancanti negli oggetti**: Ogni oggetto deve avere TUTTI i campi richiesti dalla struttura documentata
9. **Array vs Numeri Singoli**: Verificare SEMPRE nella sezione "Array vs Numeri Singoli" quale tipo di dato usare per ogni campo
10. **margine_contribuzione come percentuale**: Deve essere decimale (0.6453) NON percentuale già moltiplicata (64.53)

---

## Template JSON Minimo Obbligatorio

Per evitare errori PHP, parti SEMPRE da questo template minimo e popola tutte le sezioni:

```json
{
  "metadata": {
    "company_name": "...",
    "fiscal_years": ["2023", "2024", "2025"],
    "currency": "EUR",
    "last_update": "...",
    "notes": "..."
  },
  "income_statement": { /* ... */ },
  "balance_sheet": {
    "attivo": { /* ... */ },
    "passivo": { /* ... */ }
  },
  "kpi": {
    "redditivita": { /* ... */ },
    "liquidita": { /* ... */ },
    "solidita": { /* ... */ },
    "efficienza": { /* ... */ },
    "rischio": { /* ... */ }
  },
  "executive_summary": {
    "salute_generale": "...",
    "aree": {
      "liquidita": { "stato": "...", "valore": "...", "css_class": "subwidget-..." },
      "efficienza": { "stato": "...", "valore": "...", "css_class": "subwidget-..." },
      "redditivita": { "stato": "...", "valore": "...", "css_class": "subwidget-..." }
    }
  },
  "risk_priorities": [
    { "priority": "P1", "criticita": "alta", "titolo": "...", "target": "...", "azioni": "...", "css_class": "widget-negative" }
  ],
  "cash_flow": { /* ... */ },
  "structure_margin": {
    "patrimonio_netto": [...],
    "immobilizzazioni": [...],
    "margine": [...],
    "stato": [...]
  },
  "debt_structure": {
    "breve_termine": [...],
    "lungo_termine": [...],
    "totale": [...],
    "breve_pct": [...],
    "lungo_pct": [...],
    "costo_medio_debito": 0
  },
  "interest_coverage": {
    "ebit": [...],
    "oneri_finanziari": [...],
    "icr": [...],
    "costo_medio_debito": 0
  },
  "dupont_analysis": { /* ... */ },
  "break_even": { /* ... */ },
  "z_score_altman": {
    "punteggio": [...],
    "zone": [...],
    "components_2025": { "x1": 0, "x2": 0, "x3": 0, "x4": 0, "x5": 0 }
  },
  "produttivita": {
    "dipendenti": [...],
    "dipendenti_stimati": 0,
    "ricavi_per_dipendente": [...],
    "valore_aggiunto_per_dipendente_euro": [...]
  },
  "capex": {
    "periodi": ["2023-2024"],
    "valori": [0],
    "dettagli": [
      {
        "periodo": "2023-2024",
        "totale": 0,
        "delta_immobilizzazioni": 0,
        "ammortamenti": 0,
        "nota": "..."
      }
    ]
  },
  "ai_insights": {
    "executive_summary": {
      "negativi": ["..."],
      "positivi": ["..."]
    },
    "leva_finanziaria": "...",
    "efficienza_costi_dso": "...",
    "crescita_ricavi": "...",
    "profittabilita": "...",
    "ciclo_capitale": "...",
    "liquidita_ratios": "...",
    "cash_flow_waterfall": "...",
    "cash_flow_trend": "...",
    "struttura_debiti": "...",
    "sostenibilita_debito": "...",
    "margine_struttura": "...",
    "performance_economica": "...",
    "solidita_patrimoniale": "...",
    "break_even": "...",
    "produttivita": "...",
    "capex": "...",
    "dupont": "...",
    "z_score": "..."
  },
  "risk_matrix": [
    { "categoria": "Rischio Operativo", "livello": "...", "indicatori": [...], "css_class": "widget-..." }
  ],
  "documents": []
}
```

**IMPORTANTE**: Questo è il MINIMO richiesto. Puoi aggiungere sezioni opzionali (composizione_attivo, composizione_passivo, ecc.), ma NON puoi rimuovere nessuna di queste sezioni base!

---

## Riepilogo Processo Completo

1. **Parti dal template minimo** sopra per assicurarti di non dimenticare sezioni obbligatorie
2. **Estrai i dati finanziari** dai bilanci e popolali nel JSON
3. **Calcola le metriche** derivate (KPI, ratios, ecc.)
4. **Analizza e valuta** ogni aspetto della situazione finanziaria
5. **Assegna css_class** appropriate basandoti sulle valutazioni
6. **Genera ai_insights** dettagliati per ogni sezione
7. **Verifica con la checklist** nella sezione "Validazione del JSON"
8. **Controlla encoding UTF-8** e validità sintattica
9. **Confronta con _data-schema.json** se hai dubbi sulla struttura

---

## File di Riferimento

- **data-schema.json** - Il file da compilare
- **assets/css/styles.css** - Le classi CSS disponibili
- **index.php** - Il template neutrale che legge il JSON
