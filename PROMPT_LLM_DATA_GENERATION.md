# Istruzioni per LLM - Generazione Data Schema JSON

## Obiettivo
Compilare il file `data-schema.json` con dati finanziari e valutazioni, assegnando classi CSS appropriate che comunicano il giudizio della salute aziendale.

## Principio Fondamentale
Il **JSON è il bridge** tra:
- **Upstream**: Analisi AI dei dati finanziari (generazione dati + valutazione)
- **Frontend**: Visualizzazione neutra basata su classi CSS nel JSON

Il PHP rimane **completamente neutrale** e non prende decisioni su colori/stili. Tutte le decisioni sono nel JSON.

---

## Struttura delle Classi CSS Disponibili

Le seguenti classi CSS sono disponibili per segnalare lo stato dei dati:

| Classe CSS | Uso | Colore | Quando usare |
|-----------|-----|--------|--------------|
| `widget-positive` | Dati positivi/eccellenti | Verde gradiente | ROE 45.3%, ricavi +49%, liquidità buona |
| `widget-negative` | Dati negativi/critici | Rosso gradiente | Cash Ratio 0.06x, DSO sopra target |
| `widget-purple` | Dati neutrali/informativi | Viola gradiente | Rischi concentrazione, informazioni generali |

---

## Campi nel JSON con `css_class`

Ogni struttura dati che ha una **valutazione qualitativa** deve includere `css_class`:

### 1. Executive Summary
```json
{
  "aree": {
    "redditivita": {
      "stato": "Ottima",
      "valore": "ROE 45.3%",
      "css_class": "widget-positive"  // ← Sempre presente
    }
  }
}
```

**Regola di mapping per `stato` → `css_class`:**
- "Ottima", "Forte", "Buona", "Basso", "Accettabile" → `widget-positive`
- "Attenzione", "Migliorabile", "Medio", "Critico" → `widget-negative`
- "Info", "Neutrale" → `widget-purple`

### 2. Risk Priorities
```json
{
  "priority": "P1",
  "criticita": "alta",
  "titolo": "Migliorare Cash Ratio",
  "css_class": "widget-negative"  // ← Basato su criticità
}
```

**Mapping `criticita` → `css_class`:**
- "alta" → `widget-negative`
- "media" → `widget-positive`
- "bassa" → `widget-positive`

### 3. Risk Matrix
```json
{
  "categoria": "Rischio Liquidità",
  "livello": "Medio",
  "indicatori": [...],
  "css_class": "widget-negative"  // ← Basato su livello
}
```

**Mapping `livello` → `css_class`:**
- "Basso" → `widget-positive`
- "Medio", "Alto" → `widget-negative`
- "Info" → `widget-purple`

### 4. Z-Score Altman
```json
{
  "punteggio": [1.85, 1.42, 3.18],
  "zone": ["grey", "grey", "safe"],
  "css_class_2025": "widget-positive"  // ← Basato su zona 2025
}
```

**Mapping `zone` → `css_class`:**
- "safe" (>2.9) → `widget-positive`
- "grey" (1.23-2.9) → `widget-purple`
- "risk" (<1.23) → `widget-negative`

---

## Processo di Compilazione

### Step 1: Estrarre dati finanziari
- Leggere bilanci, conto economico, cash flow
- Popolare le sezioni: `income_statement`, `balance_sheet`, `cash_flow`, `kpi`
- Usare valori reali da documenti finanziari

### Step 2: Calcolare metriche derivate
- Calcolare KPI dai dati: ROE, ROA, ROS, ecc.
- Eseguire analisi DuPont, break-even, Z-Score
- Popolare: `dupont_analysis`, `break_even`, `z_score_altman`, `interest_coverage`

### Step 3: Valutare lo stato (AI judgment)
- Per ogni metrica, assegnare uno `stato` (es: "Ottima", "Critica")
- Basarsi su:
  - Valori storici (trend)
  - Benchmarks settoriali
  - Target aziendali
  - Regole finanziarie (es: cash ratio <0.1 = critico)

### Step 4: Assegnare css_class automaticamente
- **Non scrivere manualmente** la classe CSS
- **Usare il mapping fornito sopra** per tradurre `stato` → `css_class`
- Se lo stato è "Attenzione" → automaticamente `css_class: "widget-negative"`

### Step 5: Generare insight AI
- Popolare `ai_insights` con:
  - Spiegazioni dei numeri
  - Contesto storico
  - Raccomandazioni
  - Non includere giudizi di colore qui (il colore viene dal `css_class`)

---

## Esempio Completo

```json
{
  "metadata": {
    "company_name": "Perspect srl",
    "fiscal_years": ["2023", "2024", "2025"],
    "currency": "EUR",
    "last_update": "2025-01-15"
  },
  "executive_summary": {
    "salute_generale": "Buona",
    "aree": {
      "liquidita": {
        "stato": "Attenzione",
        "valore": "Cash 0.06x",
        "css_class": "widget-negative"  // ← Automatico: stato=Attenzione → negative
      },
      "redditivita": {
        "stato": "Ottima",
        "valore": "ROE 45.3%",
        "css_class": "widget-positive"  // ← Automatico: stato=Ottima → positive
      }
    }
  },
  "risk_priorities": [
    {
      "priority": "P1",
      "criticita": "alta",
      "titolo": "Migliorare Cash Ratio",
      "css_class": "widget-negative"  // ← Automatico: criticita=alta → negative
    }
  ]
}
```

---

## Best Practices

1. **Coerenza**: Se lo stato è "Critico", deve essere `widget-negative`
2. **Spiegare il giudizio**: Aggiungere un campo `ragione` opzionale per spiegare come è stato assegnato lo stato
3. **Dati vs Giudizio**: Separare chiaramente:
   - Dati = numeri grezzi (`value`, `icr`, `roe`)
   - Giudizio = stato qualitativo (`stato`, `criticita`, `css_class`)
4. **Coerenza storica**: Se nel 2024 lo stato era "Attenzione", nel 2025 se peggiora dovrebbe rimanere/peggiorare
5. **Non inventare CSS class**: Usare SOLO le 3 classi disponibili (`widget-positive`, `widget-negative`, `widget-purple`)

---

## Validazione del JSON

Prima di consegnare il JSON, verificare:
- ✅ Ogni elemento con `stato` ha `css_class` corrispondente
- ✅ CSS class è sempre una delle 3 opzioni valide
- ✅ Mapping stato → css_class segue le regole definite
- ✅ Nessuno stile inline nel PHP (tutto nel JSON + CSS)
- ✅ JSON è valido (controllare con `jq` o validatore online)

---

## Domande Frequenti

### D: E se uno stato non è nella lista di mapping?
**R**: Aggiungere il nuovo stato a questa documentazione e definire il mapping prima di usarlo.

### D: Posso usare colori personalizzati?
**R**: No. PHP e UI non hanno stile inline. Tutte le decisioni estetiche passano per `css_class`.

### D: Chi decide se uno stato è "Ottima" o "Buona"?
**R**: L'LLM/AI, basandosi su:
- Dati storici
- Benchmarks
- Contesto aziendale
- Regole di finanza

### D: Che succede se cambiano i dati?
**R**: Il JSON viene rigenerato con nuovi dati e nuove valutazioni, che generano nuove classi CSS.

---

## File di Riferimento

- **data-schema.json** - Il file da compilare
- **assets/css/styles.css** - Le classi CSS disponibili
- **index.php** - Il template neutrale che legge il JSON
