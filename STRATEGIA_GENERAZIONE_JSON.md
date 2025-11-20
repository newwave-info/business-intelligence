# Strategia di Generazione JSON Multi-Step

## 🎯 Obiettivo
Dividere il processo di generazione del `data-schema.json` in più step sequenziali per evitare timeout e blocchi dell'LLM, ottimizzando efficienza e affidabilità.

---

## 📊 Analisi Dati CSV

### Struttura CSV
Ogni CSV contiene:
- **Colonna 1**: Nome voce di bilancio
- **Colonna 2**: Valore anno N (es. 2024)
- **Colonna 3**: Valore anno N-1 (es. 2023)
- **Colonna 4**: Note (solitamente vuota)
- **Colonna 5**: Tag XBRL

### File Disponibili
```
docs/bilanci/IT03731686-2024-Esercizio-1-101239144.csv  → Dati 2024 e 2023
docs/bilanci/IT03731686-2023-Esercizio-2-101239144.csv  → Dati 2023 e 2022
docs/bilanci/IT03731686-2022-Esercizio-3-101239144.csv  → Dati 2022 e 2021
docs/bilanci/IT03731686-2021-Esercizio-4-101239144.csv  → Dati 2021 e 2020
```

**Risultato**: 4 anni fiscali completi (2021, 2022, 2023, 2024)

---

## 🔄 Strategia Multi-Step

### **STEP 1: Estrazione Dati Grezzi** ⚙️
**Tipo**: Operazione MECCANICA (può essere script Python o LLM leggero)
**Input**: 4 file CSV
**Output**: `data-raw.json` (dati grezzi estratti)
**Tempo stimato**: 30-60 secondi

#### Dati da Estrarre (mapping diretto CSV → JSON):

**1. Metadata**
- `company_name` ← "Dati anagrafici, denominazione"
- `fiscal_years` ← [2021, 2022, 2023, 2024]
- `currency` ← "EUR"
- `last_update` ← Data corrente
- `notes` ← Combinazione dati anagrafici

**2. Income Statement** (tutti i campi)
| Campo JSON | Voce CSV |
|------------|----------|
| `ricavi` | "A.1 - Valore della produzione, ricavi delle vendite e delle prestazioni" |
| `costi_servizi` | "B.7 - Costi della produzione, per servizi" |
| `costi_personale` | "B.9 - Costi della produzione, per il personale, totale costi per il personale" |
| `altri_costi_operativi` | "B.14 - Costi della produzione, oneri diversi di gestione" + "B.8 - Costi della produzione, per godimento di beni di terzi" |
| `ammortamenti` | "B.10 - Costi della produzione, ammortamenti e svalutazioni, totale ammortamenti e svalutazioni" |
| `ebit` | "Differenza tra valore e costi della produzione" |
| `oneri_finanziari` | "C.17 - Proventi e oneri finanziari, interessi e altri oneri finanziari, totale interessi e altri oneri finanziari" |
| `imposte` | "20 - Imposte sul reddito dell'esercizio, correnti, differite e anticipate, totale delle imposte..." |
| `utile_netto` | "21 - Utile (perdita) dell'esercizio" |

**3. Balance Sheet - Attivo**
| Campo JSON | Voce CSV |
|------------|----------|
| `immobilizzazioni_immateriali` | "B.I - Totale immobilizzazioni immateriali" |
| `immobilizzazioni_materiali` | "B.II - Totale immobilizzazioni materiali" |
| `immobilizzazioni_finanziarie` | "B.III - Totale immobilizzazioni finanziarie" |
| `immobilizzazioni_totale` | "B - Totale immobilizzazioni" |
| `crediti_commerciali` | "C.II - Totale crediti" |
| `disponibilita_liquide` | "C.IV - Totale disponibilità liquide" |
| `altre_attivita_correnti` | Calcolo: Attivo Circolante - Crediti - Liquidità |
| `attivo_circolante_totale` | "C - Totale attivo circolante" |
| `totale_attivo` | "Totale attivo" |

**4. Balance Sheet - Passivo**
| Campo JSON | Voce CSV |
|------------|----------|
| `capitale_sociale` | "A.I - Patrimonio netto, capitale" |
| `riserve` | Somma tutte le riserve (A.II, A.III, A.IV, A.V, A.VI) |
| `utile_esercizio` | "A.IX - Patrimonio netto, utile (perdita) dell'esercizio" |
| `patrimonio_netto_totale` | "A - Totale patrimonio netto" |
| `debiti_finanziari` | "D - Debiti, esigibili oltre l'esercizio successivo" |
| `debiti_commerciali` | Parte di "D - Debiti, esigibili entro l'esercizio successivo" |
| `altri_debiti_correnti` | Resto debiti a breve |
| `tfr` | "C - Trattamento di fine rapporto di lavoro subordinato" |
| `debiti_totali` | "D - Totale debiti" |
| `totale_passivo` | "Totale passivo" |

**5. Calcoli Semplici da Fare in Step 1**
- `ebitda` = `ricavi` - `costi_servizi` - `costi_personale` - `altri_costi_operativi`
- `altre_attivita_correnti` = `attivo_circolante_totale` - `crediti_commerciali` - `disponibilita_liquide`

#### Output Step 1 (`data-raw.json`):
```json
{
  "metadata": { ... },
  "income_statement": { ... },  // Array di 4 elementi
  "balance_sheet": {
    "attivo": { ... },
    "passivo": { ... }
  }
}
```

---

### **STEP 2: Calcolo KPI e Metriche** 🧮
**Tipo**: Applicazione FORMULE (può essere script Python o LLM)
**Input**: `data-raw.json`
**Output**: `data-kpi.json` (dati + KPI calcolati)
**Tempo stimato**: 30-60 secondi

#### Calcoli da Eseguire:

**1. KPI Redditività**
- ROE = (utile_netto / patrimonio_netto) × 100
- ROA = (utile_netto / totale_attivo) × 100
- ROS = (ebit / ricavi) × 100
- EBITDA Margin = (ebitda / ricavi) × 100
- DSCR = cash_flow_operativo / debiti_finanziari (se > 0)

**2. KPI Liquidità**
- Current Ratio = attivo_circolante / debiti_breve_termine
- Quick Ratio = (attivo_circolante - rimanenze) / debiti_breve_termine
- Cash Ratio = disponibilita_liquide / debiti_breve_termine
- Margine Tesoreria = disponibilita_liquide - debiti_breve_termine

**3. KPI Solidità**
- Debt to Equity = debiti_totali / patrimonio_netto
- Leverage = totale_attivo / patrimonio_netto
- Interest Coverage Ratio (ICR) = ebit / oneri_finanziari
- Autonomia Finanziaria = (patrimonio_netto / totale_attivo) × 100

**4. KPI Efficienza**
- DSO (Days Sales Outstanding) = (crediti_commerciali / ricavi) × 365
- DPO (Days Payable Outstanding) = (debiti_commerciali / costi_servizi) × 365
- Ciclo Finanziario = DSO - DPO
- Asset Turnover = ricavi / totale_attivo

**5. KPI Rischio**
- Z-Score Altman (formula completa con 5 componenti)

**6. Altre Metriche**
- DuPont Analysis (scomposizione ROE)
- Debt Structure (% breve vs lungo termine)
- Interest Coverage
- Structure Margin = patrimonio_netto - immobilizzazioni_totale
- CAPEX = variazione immobilizzazioni + ammortamenti
- Cash Flow Operativo (formula indiretta)

#### Output Step 2 (`data-kpi.json`):
```json
{
  "metadata": { ... },
  "income_statement": { ... },
  "balance_sheet": { ... },
  "kpi": {
    "redditivita": { ... },
    "liquidita": { ... },
    "solidita": { ... },
    "efficienza": { ... },
    "rischio": { ... }
  },
  "cash_flow": { ... },
  "structure_margin": { ... },
  "debt_structure": { ... },
  "interest_coverage": { ... },
  "dupont_analysis": { ... },
  "break_even": { ... },
  "z_score_altman": { ... },
  "produttivita": { ... },
  "capex": { ... }
}
```

---

### **STEP 3: Analisi, Giudizio e Insights** 🧠
**Tipo**: RAGIONAMENTO PROFONDO (LLM potente - GPT-4, Claude Opus)
**Input**: `data-kpi.json`
**Output**: `data-schema.json` (JSON finale completo)
**Tempo stimato**: 2-5 minuti

#### Compiti dell'LLM:

**1. Executive Summary** (CRITICO)
- Analizzare TUTTI i KPI
- Identificare punti critici (negativi) e punti di forza (positivi)
- Assegnare `css_class` appropriate (`subwidget-neutral` o `subwidget-negative`)
- Ordinare: negativi PRIMA dei neutri
- Struttura: oggetto `aree` con sottosezioni (liquidita, efficienza, redditivita, ecc.)

**2. Risk Priorities**
- Identificare 3-5 rischi principali
- Per ogni rischio: priority, criticità, titolo, target, azioni
- Assegnare `css_class` (`widget-negative` per alta, `widget-purple` per media, `widget-positive` per bassa)
- Ordinare: criticità alta PRIMA delle altre

**3. Risk Matrix**
- Categorizzare rischi: Operativo, Finanziario, Liquidità, Concentrazione
- Assegnare livello: Basso, Medio, Alto
- Indicatori specifici per categoria
- `css_class` appropriata

**4. AI Insights** (CRITICO - 17 campi obbligatori)
- `executive_summary.negativi` - array di criticità
- `executive_summary.positivi` - array di punti di forza
- `leva_finanziaria` - analisi D/E e leverage
- `efficienza_costi_dso` - analisi costi e incassi
- `crescita_ricavi` - trend e drivers
- `profittabilita` - analisi margini
- `ciclo_capitale` - gestione capitale circolante
- `liquidita_ratios` - analisi liquidità
- `cash_flow_waterfall` - composizione cash flow
- `cash_flow_trend` - evoluzione nel tempo
- `struttura_debiti` - composizione debiti
- `sostenibilita_debito` - capacità di servizio debito
- `margine_struttura` - equilibrio patrimoniale
- `performance_economica` - sintesi economica
- `solidita_patrimoniale` - robustezza struttura
- `break_even` - punto di pareggio e margini
- `produttivita` - efficienza risorse
- `capex` - investimenti e strategie
- `dupont` - drivers ROE
- `z_score` - valutazione rischio complessivo

**5. Break-Even Analysis**
- Stimare costi fissi (personale, ammortamenti, altri costi strutturali)
- Stimare costi variabili (servizi, materie prime, variabili con ricavi)
- Calcolare margine di contribuzione
- Calcolare punto di pareggio
- Margini di sicurezza

**6. Produttività**
- Stimare numero dipendenti da costo_personale (media settore ~45K€/FTE)
- Calcolare ricavi per dipendente
- Calcolare EBITDA per dipendente
- Valore aggiunto per dipendente

**7. Documents**
- Elencare i 4 file CSV come bilanci depositati

#### Regole Critiche per Step 3:
- ✅ Usare riferimenti RELATIVI ("anno precedente") non assoluti ("2024")
- ✅ Tutti i campi `ai_insights` DEVONO essere popolati
- ✅ `executive_summary` DEVE essere oggetto con `aree`, NON array
- ✅ `capex.dettagli` DEVE essere array con oggetti completi
- ✅ `break_even` TUTTI i campi DEVONO essere numeri singoli (non array)
- ✅ Ordinamento: negativi PRIMA di neutri in executive_summary
- ✅ Ordinamento: criticità alta PRIMA in risk_priorities

---

## 📝 Implementazione Pratica

### Opzione A: 3 Script Python Separati

```bash
# Step 1: Estrazione
python extract_raw_data.py --input docs/bilanci/*.csv --output data-raw.json

# Step 2: Calcolo KPI
python calculate_kpi.py --input data-raw.json --output data-kpi.json

# Step 3: Analisi LLM
python analyze_and_insights.py --input data-kpi.json --output data-schema.json --llm openai
```

**Vantaggi**:
- Massima velocità (Step 1 e 2 istantanei)
- Determinismo (Step 1 e 2 sempre uguali)
- Costo ridotto (solo Step 3 usa LLM)
- Facile debugging

**Svantaggi**:
- Richiede sviluppo 3 script
- Meno flessibile

### Opzione B: 2 Prompt LLM Separati

```bash
# Step 1+2: Estrazione e Calcoli (LLM leggero - Haiku/GPT-3.5)
llm --prompt PROMPT_STEP1_EXTRACTION.md --input docs/bilanci/*.csv --output data-kpi.json

# Step 3: Analisi e Insights (LLM potente - Opus/GPT-4)
llm --prompt PROMPT_STEP2_ANALYSIS.md --input data-kpi.json --output data-schema.json
```

**Vantaggi**:
- Implementazione rapida
- Flessibile (LLM può gestire variazioni nei CSV)
- Step 1+2 possono usare LLM economico

**Svantaggi**:
- Costo maggiore rispetto a script Python
- Possibili errori anche in Step 1

### Opzione C: Ibrida (Script + LLM)

```bash
# Step 1: Script Python (istantaneo, deterministico)
python extract_raw_data.py --input docs/bilanci/*.csv --output data-raw.json

# Step 2: Script Python (istantaneo, formule standard)
python calculate_kpi.py --input data-raw.json --output data-kpi.json

# Step 3: LLM potente (solo analisi e insights)
llm --prompt PROMPT_ANALYSIS_ONLY.md --input data-kpi.json --output data-schema.json
```

**Vantaggi**:
- Velocità massima (Step 1-2 istantanei)
- Costo minimo (solo Step 3 usa LLM)
- Affidabilità (dati ed KPI corretti al 100%)
- LLM si concentra solo su ciò che sa fare meglio (analisi)

**Svantaggi**:
- Richiede sviluppo 2 script Python

---

## 🎯 Raccomandazione

**Opzione C - Ibrida** è la MIGLIORE per questo caso d'uso:

### Perché?
1. **Step 1-2 sono MECCANICI**: leggere CSV e applicare formule non richiede AI
2. **Step 3 richiede RAGIONAMENTO**: valutazioni, insights, giudizi sono dove l'AI eccelle
3. **Massima efficienza**: Step 1-2 completano in <1 secondo ciascuno
4. **Costo ottimizzato**: Paghi LLM solo per 1 step (analisi)
5. **Affidabilità**: Dati e KPI sono corretti al 100%, LLM non può sbagliare formule
6. **Debugging facile**: Se ci sono problemi, sai esattamente dove guardare

### Flusso Completo
```
CSV files (4)
    ↓
[extract_raw_data.py] → data-raw.json (metadata + bilanci grezzi)
    ↓
[calculate_kpi.py] → data-kpi.json (+ tutti i KPI calcolati)
    ↓
[LLM Potente] → data-schema.json (+ analisi, insights, css_class)
```

---

## ✅ Vantaggi della Strategia Multi-Step

1. **NO Timeout**: Ogni step è breve e focalizzato
2. **NO Blocchi**: Se uno step fallisce, riparti da lì
3. **Debugging Facile**: Ogni step ha input/output chiari
4. **Costo Ottimizzato**: LLM potente solo dove serve
5. **Velocità**: Step 1-2 istantanei con Python
6. **Qualità**: Dati e KPI corretti al 100%, LLM si concentra su analisi
7. **Riutilizzo**: `data-kpi.json` può essere usato per altri report
8. **Manutenibilità**: Ogni componente è piccolo e testabile

---

## 📦 Output Intermedi

### `data-raw.json` (Step 1)
~5KB, solo dati grezzi estratti dai CSV

### `data-kpi.json` (Step 2)
~15KB, dati + tutti i KPI calcolati

### `data-schema.json` (Step 3 - FINALE)
~80KB, JSON completo con analisi e insights

---

## 🔧 Prossimi Passi

1. ✅ Scegliere Opzione C (Ibrida)
2. 📝 Scrivere `extract_raw_data.py` (mappare CSV → JSON raw)
3. 🧮 Scrivere `calculate_kpi.py` (applicare tutte le formule)
4. 🧠 Scrivere `PROMPT_ANALYSIS_ONLY.md` (prompt per Step 3)
5. 🧪 Testare pipeline completa su bilanci Axerta
6. 🚀 Deploy e automazione

