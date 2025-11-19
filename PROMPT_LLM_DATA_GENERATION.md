# Istruzioni per LLM - Generazione Data Schema JSON

## Obiettivo
Compilare il file `data-schema.json` con dati finanziari e valutazioni, assegnando classi CSS appropriate che comunicano il giudizio della salute aziendale.

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

### 5. Executive Summary

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
    "breve_pct": [75, 76, 68],
    "lungo_pct": [25, 24, 32]
  }
}
```

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

```json
{
  "break_even": {
    "punto_pareggio": 551000,
    "margine_sicurezza_pct": 33,
    "margine_sicurezza_eur": 273708
  }
}
```

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

### 15. CAPEX

```json
{
  "capex": {
    "dettagli": [
      {
        "periodo": "2023-2024",
        "valore": 160000,
        "descrizione": "Investimento infrastruttura IT"
      },
      {
        "periodo": "2024-2025",
        "valore": 82621,
        "descrizione": "Consolidamento asset esistenti"
      }
    ]
  }
}
```

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

### 17. Concentrazione Ricavi

```json
{
  "concentrazione_ricavi": {
    "clienti": ["Cliente A", "Cliente B", "Cliente C", "Cliente D", "Cliente E", "Altri"],
    "valori_ultimo_anno": [314464, 282113, 25621, 13000, 10000, 7000],
    "percentuali_ultimo_anno": [47.3, 42.4, 3.8, 1.9, 1.5, 1.0]
  }
}
```

### 18. Documents

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

Prima di consegnare il JSON, verificare:
- ✅ `fiscal_years` definito correttamente
- ✅ Tutti gli array hanno lunghezza = numero di anni fiscali
- ✅ Executive Summary usa `subwidget-neutral`/`subwidget-negative`
- ✅ **Executive Summary: elementi `subwidget-negative` PRIMA di `subwidget-neutral`**
- ✅ **Risk Priorities: elementi `widget-negative` PRIMA di altri**
- ✅ Altri widget usano `widget-positive`/`widget-negative`/`widget-purple`
- ✅ Tutti i campi `ai_insights` sono popolati
- ✅ JSON è valido sintatticamente

---

## Errori Comuni da Evitare

1. **Array di lunghezza diversa**: TUTTI gli array devono avere N elementi
2. **Classi CSS sbagliate per Executive Summary**: usare `subwidget-*` non `widget-*`
3. **ai_insights mancanti**: il PHP si aspetta TUTTI i campi elencati
4. **Riferimenti assoluti agli anni**: usare "anno precedente" non "2024"
5. **Dati inconsistenti**: totale attivo deve = totale passivo
6. **Ordinamento errato**: elementi critici (negative/rossi) devono SEMPRE essere elencati prima di quelli positivi/neutri in Executive Summary e Risk Priorities

---

## File di Riferimento

- **data-schema.json** - Il file da compilare
- **assets/css/styles.css** - Le classi CSS disponibili
- **index.php** - Il template neutrale che legge il JSON
