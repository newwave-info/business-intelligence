# Prompt LLM - STEP 3: Analisi e Insights

## 📥 Input

File `data-kpi.json` contenente:

- Metadata azienda
- Dati finanziari storici (income statement, balance sheet)
- KPI già calcolati (redditività, liquidità, solidità, efficienza, rischio)
- Cash flow, structure margin, debt structure, break even, ecc.

## 📤 Output Richiesto

File `data-schema.json` completo con:

- Tutti i dati e KPI da `data-kpi.json`
- **+ Analisi, giudizi e insights generati dall'AI**

---

## 🎯 Tuo Compito

Devi completare il JSON aggiungendo le sezioni che richiedono **analisi e ragionamento**:

### 1. **Executive Summary** (CRITICO!)

**STRUTTURA OBBLIGATORIA**:

```json
{
  "executive_summary": {
    "salute_generale": "Buona|Ottima|Attenzione|Critica",
    "aree": {
      "liquidita": {
        "stato": "Attenzione|Migliorabile|Critico|Ottima|Forte|Buona|Basso|Accettabile",
        "valore": "Descrizione breve con valore (es. 'Cash 0.06x')",
        "css_class": "subwidget-negative|subwidget-neutral"
      },
      "efficienza": { ... },
      "redditivita": { ... },
      "crescita": { ... },
      "solidita": { ... },
      "rischio": { ... }
    }
  }
}
```

**REGOLE CRITICHE**:

- ✅ DEVE essere oggetto con campo `aree`, **NON array diretto**
- ✅ Elementi con `css_class: "subwidget-negative"` DEVONO essere elencati **PRIMA** di quelli con `subwidget-neutral`
- ✅ Mapping stato → css_class:
  - "Attenzione", "Migliorabile", "Critico" → `subwidget-negative`
  - "Ottima", "Forte", "Buona", "Basso", "Accettabile" → `subwidget-neutral`

### 2. **Risk Priorities** (array di 3-5 elementi)

```json
{
  "risk_priorities": [
    {
      "priority": "P1",
      "criticita": "alta|media|bassa",
      "titolo": "Titolo breve del rischio",
      "target": "Target specifico (es. '>0.2x (attuale 0.06x)')",
      "azioni": "Azioni concrete da intraprendere",
      "cassa_liberabile": 75000, // opzionale
      "miglioramento": "27%", // opzionale
      "css_class": "widget-negative|widget-purple|widget-positive"
    }
  ]
}
```

**REGOLE**:

- ✅ Ordinamento: `widget-negative` (criticità alta) **PRIMA** di `widget-purple` o `widget-positive`
- ✅ Mapping criticità → css_class:
  - "alta" → `widget-negative`
  - "media" → `widget-purple`
  - "bassa" → `widget-positive`

### 3. **Risk Matrix** (array di 3-4 categorie)

```json
{
  "risk_matrix": [
    {
      "categoria": "Rischio Operativo|Rischio Finanziario|Rischio Liquidità|Rischio Concentrazione",
      "livello": "Basso|Medio|Alto",
      "indicatori": [
        "Indicatore 1 con valore",
        "Indicatore 2 con valore",
        "Indicatore 3 con valore"
      ],
      "css_class": "widget-positive|widget-negative|widget-purple"
    }
  ]
}
```

### 4. **AI Insights** (CRITICO - 17 campi obbligatori!)

Tutti i campi sotto devono contenere testo significativo (2-4 frasi).

**IMPORTANTE**: Usa riferimenti RELATIVI ("anno precedente", "ultimo anno") **NON** assoluti ("2024", "2023").

```json
{
  "ai_insights": {
    "executive_summary": {
      "negativi": [
        "Criticità 1 identificata dall'analisi",
        "Criticità 2 identificata dall'analisi"
      ],
      "positivi": ["Punto di forza 1", "Punto di forza 2", "Punto di forza 3"]
    },
    "leva_finanziaria": "Analisi D/E e leverage con valori concreti",
    "efficienza_costi_dso": "Analisi costi e DSO con percentuali",
    "crescita_ricavi": "Trend ricavi con CAGR e drivers",
    "profittabilita": "Analisi margini (EBITDA, EBIT, utile netto)",
    "ciclo_capitale": "Analisi gestione capitale circolante",
    "liquidita_ratios": "Analisi current ratio, quick ratio, cash ratio",
    "cash_flow_waterfall": "Composizione cash flow operativo",
    "cash_flow_trend": "Evoluzione cash flow nel tempo",
    "struttura_debiti": "Composizione debiti breve vs lungo termine",
    "sostenibilita_debito": "Analisi ICR, DSCR, capacità di servizio",
    "margine_struttura": "Equilibrio patrimoniale PN vs Immobilizzazioni",
    "performance_economica": "Sintesi performance economica complessiva",
    "solidita_patrimoniale": "Analisi robustezza struttura patrimoniale",
    "break_even": "Analisi punto di pareggio e margini di sicurezza",
    "produttivita": "Analisi produttività per dipendente",
    "capex": "Analisi investimenti e strategie CAPEX",
    "dupont": "Analisi drivers ROE (margine, turnover, leverage)",
    "z_score": "Valutazione Z-Score e rischio insolvenza"
  }
}
```

### 5. **Documents** (array bilanci)

```json
{
  "documents": [
    {
      "nome_file": "IT03731686-2024-Esercizio-1-101239144.csv",
      "tipo": "Bilancio CSV",
      "esercizio": "2024",
      "data_chiusura": "31/12/2024",
      "url": "docs/bilanci/IT03731686-2024-Esercizio-1-101239144.csv"
    }
  ]
}
```

---

## ⚠️ Errori da EVITARE ASSOLUTAMENTE

### ❌ ERRORE FATALE #1: Executive Summary come array

```json
// SBAGLIATO
"executive_summary": [
  { "title": "...", "value": "..." }
]

// CORRETTO
"executive_summary": {
  "salute_generale": "Buona",
  "aree": {
    "liquidita": { ... }
  }
}
```

### ❌ ERRORE FATALE #2: Ordinamento errato

- Executive Summary: elementi `subwidget-negative` DEVONO stare PRIMA
- Risk Priorities: elementi `widget-negative` DEVONO stare PRIMA

### ❌ ERRORE FATALE #3: AI Insights incompleti

TUTTI i 17 campi di `ai_insights` DEVONO essere popolati con testo significativo.

### ❌ ERRORE FATALE #4: Riferimenti assoluti

```json
// SBAGLIATO
"crescita_ricavi": "I ricavi 2024 sono cresciuti del 9% rispetto al 2023"

// CORRETTO
"crescita_ricavi": "I ricavi dell'ultimo anno sono cresciuti del 9% rispetto all'anno precedente"
```

### ❌ ERRORE FATALE #5: Modificare dati già calcolati

- **NON modificare** i campi già presenti in `data-kpi.json`
- **NON ricalcolare** KPI o metriche
- **SOLO aggiungere** le sezioni di analisi

---

## ✅ Checklist Validazione Output

Prima di consegnare `data-schema.json`, verifica:

- [ ] `executive_summary` è oggetto con `aree` (NON array)
- [ ] `executive_summary.aree`: negativi PRIMA di neutri
- [ ] `risk_priorities` array con 3-5 elementi
- [ ] `risk_priorities`: criticità alta PRIMA delle altre
- [ ] `risk_matrix` array con 3-4 categorie
- [ ] `ai_insights` con TUTTI i 17 campi popolati
- [ ] `ai_insights`: riferimenti relativi, NON assoluti
- [ ] `documents` array presente (anche se vuoto va bene)
- [ ] Nessun dato/KPI modificato rispetto a `data-kpi.json`
- [ ] JSON sintatticamente valido (no virgole finali, parentesi bilanciate)

---

## 🎯 Processo Raccomandato

1. **Leggi** `data-kpi.json` completamente
2. **Analizza** tutti i KPI per identificare:
   - Trend positivi e negativi
   - Punti critici che richiedono attenzione
   - Punti di forza da evidenziare
3. **Crea** `executive_summary` ordinando criticità prima
4. **Identifica** 3-5 rischi principali per `risk_priorities`
5. **Categorizza** rischi in `risk_matrix`
6. **Genera** insights dettagliati per TUTTI i 17 campi `ai_insights`
7. **Aggiungi** elenco bilanci in `documents`
8. **Copia** tutti i dati da `data-kpi.json` + aggiungi le tue sezioni
9. **Valida** con la checklist sopra
10. **Output** JSON finale completo

---

## 📋 Template Output Minimo

```json
{
  // COPIA TUTTO DA data-kpi.json
  "metadata": { ... },
  "income_statement": { ... },
  "balance_sheet": { ... },
  "kpi": { ... },
  "cash_flow": { ... },
  "structure_margin": { ... },
  "debt_structure": { ... },
  "interest_coverage": { ... },
  "dupont_analysis": { ... },
  "break_even": { ... },
  "z_score_altman": { ... },
  "produttivita": { ... },
  "capex": { ... },
  "composizione_attivo": { ... },
  "composizione_passivo": { ... },

  // AGGIUNGI QUESTE SEZIONI (il tuo lavoro)
  "executive_summary": {
    "salute_generale": "...",
    "aree": { ... }
  },
  "risk_priorities": [ ... ],
  "risk_matrix": [ ... ],
  "ai_insights": {
    "executive_summary": { "negativi": [...], "positivi": [...] },
    "leva_finanziaria": "...",
    ... // tutti i 17 campi
  },
  "documents": [ ... ]
}
```

---

## 🚀 Ora Procedi

Leggi `data-kpi.json`, analizza i dati, e genera `data-schema.json` seguendo ESATTAMENTE le istruzioni sopra.

Buona analisi! 🧠
