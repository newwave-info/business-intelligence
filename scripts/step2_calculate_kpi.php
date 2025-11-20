<?php
/**
 * STEP 2: Calcolo KPI e Metriche
 *
 * Legge data-raw.json e calcola tutti i KPI finanziari applicando formule standard
 * Output: data-kpi.json (pronto per analisi LLM in Step 3)
 *
 * Usage: php step2_calculate_kpi.php
 * Input: data-raw.json
 * Output: data-kpi.json
 */

// Configurazione
$inputFile = __DIR__ . '/../data-raw.json';
$outputFile = __DIR__ . '/../data-kpi.json';

// Leggi input
if (!file_exists($inputFile)) {
    die("❌ File non trovato: $inputFile\nEsegui prima: php step1_extract_raw_data.php\n");
}

$rawData = json_decode(file_get_contents($inputFile), true);
if (!$rawData) {
    die("❌ Errore parsing JSON: $inputFile\n");
}

echo "📊 Calcolo KPI per " . $rawData['metadata']['company_name'] . "\n";
echo "📅 Anni: " . implode(', ', $rawData['metadata']['fiscal_years']) . "\n\n";

$fiscalYears = $rawData['metadata']['fiscal_years'];
$numYears = count($fiscalYears);
$lastIdx = $numYears - 1;
$prevIdx = $numYears - 2;

// Shorthand per accesso dati
$income = $rawData['income_statement'];
$attivo = $rawData['balance_sheet']['attivo'];
$passivo = $rawData['balance_sheet']['passivo'];

// ===========================================
// KPI - REDDITIVITA
// ===========================================
echo "💰 Calcolo KPI Redditività...\n";

$kpi = [
    'redditivita' => [
        'roe' => [],  // (utile_netto / patrimonio_netto) * 100
        'roa' => [],  // (utile_netto / totale_attivo) * 100
        'roi' => [],  // (ebit / totale_attivo) * 100
        'ros' => [],  // (ebit / ricavi) * 100
        'ebitda_margin' => [],  // (ebitda / ricavi) * 100
        'dscr' => []  // Debt Service Coverage Ratio (calcolato dopo cash flow)
    ],
    'liquidita' => [],
    'solidita' => [],
    'efficienza' => [],
    'rischio' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $roe = $passivo['patrimonio_netto_totale'][$i] > 0
        ? ($income['utile_netto'][$i] / $passivo['patrimonio_netto_totale'][$i]) * 100
        : 0;

    $roa = $attivo['totale_attivo'][$i] > 0
        ? ($income['utile_netto'][$i] / $attivo['totale_attivo'][$i]) * 100
        : 0;

    $roi = $attivo['totale_attivo'][$i] > 0
        ? ($income['ebit'][$i] / $attivo['totale_attivo'][$i]) * 100
        : 0;

    $ros = $income['ricavi'][$i] > 0
        ? ($income['ebit'][$i] / $income['ricavi'][$i]) * 100
        : 0;

    $ebitda_margin = $income['ricavi'][$i] > 0
        ? ($income['ebitda'][$i] / $income['ricavi'][$i]) * 100
        : 0;

    $kpi['redditivita']['roe'][] = round($roe, 2);
    $kpi['redditivita']['roa'][] = round($roa, 2);
    $kpi['redditivita']['roi'][] = round($roi, 2);
    $kpi['redditivita']['ros'][] = round($ros, 2);
    $kpi['redditivita']['ebitda_margin'][] = round($ebitda_margin, 2);
}

// ===========================================
// KPI - LIQUIDITA
// ===========================================
echo "💧 Calcolo KPI Liquidità...\n";

$kpi['liquidita'] = [
    'current_ratio' => [],
    'quick_ratio' => [],
    'cash_ratio' => [],
    'margine_tesoreria' => []
];

for ($i = 0; $i < $numYears; $i++) {
    // Debiti a breve = debiti_commerciali + altri_debiti_correnti
    $debitiBreve = $passivo['debiti_commerciali'][$i] + $passivo['altri_debiti_correnti'][$i];

    $current_ratio = $debitiBreve > 0
        ? $attivo['attivo_circolante_totale'][$i] / $debitiBreve
        : 0;

    // Quick ratio = (attivo circolante - rimanenze) / debiti breve
    // Assumiamo rimanenze = 0 per società di servizi
    $quick_ratio = $current_ratio;

    $cash_ratio = $debitiBreve > 0
        ? $attivo['disponibilita_liquide'][$i] / $debitiBreve
        : 0;

    $margine_tesoreria = $attivo['disponibilita_liquide'][$i] - $debitiBreve;

    $kpi['liquidita']['current_ratio'][] = round($current_ratio, 2);
    $kpi['liquidita']['quick_ratio'][] = round($quick_ratio, 2);
    $kpi['liquidita']['cash_ratio'][] = round($cash_ratio, 2);
    $kpi['liquidita']['margine_tesoreria'][] = round($margine_tesoreria, 0);
}

// ===========================================
// KPI - SOLIDITA
// ===========================================
echo "🏛️  Calcolo KPI Solidità...\n";

$kpi['solidita'] = [
    'debt_equity' => [],
    'leverage' => [],
    'icr' => [],
    'autonomia_finanziaria' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $debt_equity = $passivo['patrimonio_netto_totale'][$i] > 0
        ? $passivo['debiti_totali'][$i] / $passivo['patrimonio_netto_totale'][$i]
        : 0;

    $leverage = $passivo['patrimonio_netto_totale'][$i] > 0
        ? $attivo['totale_attivo'][$i] / $passivo['patrimonio_netto_totale'][$i]
        : 0;

    $icr = $income['oneri_finanziari'][$i] > 0
        ? $income['ebit'][$i] / $income['oneri_finanziari'][$i]
        : 0;

    $autonomia = $attivo['totale_attivo'][$i] > 0
        ? ($passivo['patrimonio_netto_totale'][$i] / $attivo['totale_attivo'][$i]) * 100
        : 0;

    $kpi['solidita']['debt_equity'][] = round($debt_equity, 2);
    $kpi['solidita']['leverage'][] = round($leverage, 2);
    $kpi['solidita']['icr'][] = round($icr, 2);
    $kpi['solidita']['autonomia_finanziaria'][] = round($autonomia, 2);
}

// ===========================================
// KPI - EFFICIENZA
// ===========================================
echo "⚡ Calcolo KPI Efficienza...\n";

$kpi['efficienza'] = [
    'dso' => [],
    'dpo' => [],
    'ciclo_finanziario' => [],
    'asset_turnover' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $dso = $income['ricavi'][$i] > 0
        ? ($attivo['crediti_commerciali'][$i] / $income['ricavi'][$i]) * 365
        : 0;

    $dpo = $income['costi_servizi'][$i] > 0
        ? ($passivo['debiti_commerciali'][$i] / $income['costi_servizi'][$i]) * 365
        : 0;

    $ciclo = $dso - $dpo;

    $asset_turnover = $attivo['totale_attivo'][$i] > 0
        ? $income['ricavi'][$i] / $attivo['totale_attivo'][$i]
        : 0;

    $kpi['efficienza']['dso'][] = round($dso, 0);
    $kpi['efficienza']['dpo'][] = round($dpo, 0);
    $kpi['efficienza']['ciclo_finanziario'][] = round($ciclo, 0);
    $kpi['efficienza']['asset_turnover'][] = round($asset_turnover, 2);
}

// ===========================================
// KPI - RISCHIO (Z-Score Altman)
// ===========================================
echo "⚠️  Calcolo Z-Score Altman...\n";

$kpi['rischio'] = [
    'z_score' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $ta = $attivo['totale_attivo'][$i];

    // X1 = Working Capital / Total Assets
    $wc = $attivo['attivo_circolante_totale'][$i] - ($passivo['debiti_commerciali'][$i] + $passivo['altri_debiti_correnti'][$i]);
    $x1 = $ta > 0 ? $wc / $ta : 0;

    // X2 = Retained Earnings / Total Assets
    $retained = $passivo['riserve'][$i];
    $x2 = $ta > 0 ? $retained / $ta : 0;

    // X3 = EBIT / Total Assets
    $x3 = $ta > 0 ? $income['ebit'][$i] / $ta : 0;

    // X4 = Market Value of Equity / Total Liabilities (usiamo book value)
    $x4 = $passivo['debiti_totali'][$i] > 0 ? $passivo['patrimonio_netto_totale'][$i] / $passivo['debiti_totali'][$i] : 0;

    // X5 = Sales / Total Assets
    $x5 = $ta > 0 ? $income['ricavi'][$i] / $ta : 0;

    // Z-Score = 1.2*X1 + 1.4*X2 + 3.3*X3 + 0.6*X4 + 1.0*X5
    $z_score = (1.2 * $x1) + (1.4 * $x2) + (3.3 * $x3) + (0.6 * $x4) + (1.0 * $x5);

    $kpi['rischio']['z_score'][] = round($z_score, 2);
}

// ===========================================
// CASH FLOW (Metodo Indiretto)
// ===========================================
echo "💸 Calcolo Cash Flow...\n";

$cash_flow = [
    'utile_netto' => $income['utile_netto'],
    'ammortamenti' => $income['ammortamenti'],
    'variazione_tfr' => [],
    'variazione_crediti' => [],
    'variazione_debiti' => [],
    'investimenti' => [],
    'cash_flow_operativo' => [],
    'autofinanziamento' => [],
    'delta_capitale_circolante' => [],
    'delta_debiti_finanziari' => []
];

for ($i = 1; $i < $numYears; $i++) {
    // Variazioni anno su anno
    $varTfr = $passivo['tfr'][$i] - $passivo['tfr'][$i-1];
    $varCrediti = $attivo['crediti_commerciali'][$i] - $attivo['crediti_commerciali'][$i-1];
    $varDebiti = $passivo['debiti_commerciali'][$i] - $passivo['debiti_commerciali'][$i-1];
    $varImmob = $attivo['immobilizzazioni_totale'][$i] - $attivo['immobilizzazioni_totale'][$i-1];
    $investimenti = $varImmob + $income['ammortamenti'][$i];

    $autofinanz = $income['utile_netto'][$i] + $income['ammortamenti'][$i] + $varTfr;
    $deltaCCN = -$varCrediti + $varDebiti; // Negativo crediti perché aumento crediti assorbe cassa
    $cfo = $autofinanz + $deltaCCN;

    $varDebitiFin = $passivo['debiti_finanziari'][$i] - $passivo['debiti_finanziari'][$i-1];

    $cash_flow['variazione_tfr'][] = round($varTfr, 0);
    $cash_flow['variazione_crediti'][] = round($varCrediti, 0);
    $cash_flow['variazione_debiti'][] = round($varDebiti, 0);
    $cash_flow['investimenti'][] = round($investimenti, 0);
    $cash_flow['cash_flow_operativo'][] = round($cfo, 0);
    $cash_flow['autofinanziamento'][] = round($autofinanz, 0);
    $cash_flow['delta_capitale_circolante'][] = round($deltaCCN, 0);
    $cash_flow['delta_debiti_finanziari'][] = round($varDebitiFin, 0);
}

// Anno 0 non ha variazioni
array_unshift($cash_flow['variazione_tfr'], 0);
array_unshift($cash_flow['variazione_crediti'], 0);
array_unshift($cash_flow['variazione_debiti'], 0);
array_unshift($cash_flow['investimenti'], 0);
array_unshift($cash_flow['cash_flow_operativo'], 0);
array_unshift($cash_flow['autofinanziamento'], 0);
array_unshift($cash_flow['delta_capitale_circolante'], 0);
array_unshift($cash_flow['delta_debiti_finanziari'], 0);

// Calcola DSCR (Debt Service Coverage Ratio) = CFO / Debiti Finanziari
for ($i = 0; $i < $numYears; $i++) {
    $dscr = $passivo['debiti_finanziari'][$i] > 0
        ? $cash_flow['cash_flow_operativo'][$i] / $passivo['debiti_finanziari'][$i]
        : 0;
    $kpi['redditivita']['dscr'][] = round($dscr, 2);
}

// ===========================================
// STRUCTURE MARGIN
// ===========================================
echo "🏗️  Calcolo Margine di Struttura...\n";

$structure_margin = [
    'patrimonio_netto' => $passivo['patrimonio_netto_totale'],
    'immobilizzazioni' => $attivo['immobilizzazioni_totale'],
    'margine' => [],
    'stato' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $margine = $passivo['patrimonio_netto_totale'][$i] - $attivo['immobilizzazioni_totale'][$i];
    $structure_margin['margine'][] = round($margine, 0);

    if ($margine >= 0) {
        $structure_margin['stato'][] = "Equilibrato";
    } else if ($margine >= -100000) {
        $structure_margin['stato'][] = "Equilibrato in miglioramento";
    } else {
        $structure_margin['stato'][] = "Investimenti";
    }
}

// ===========================================
// DEBT STRUCTURE
// ===========================================
echo "📊 Calcolo Struttura Debito...\n";

$debt_structure = [
    'breve_termine' => [],
    'lungo_termine' => $passivo['debiti_finanziari'],
    'totale' => [],
    'breve_pct' => [],
    'lungo_pct' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $breve = $passivo['debiti_commerciali'][$i] + $passivo['altri_debiti_correnti'][$i];
    $lungo = $passivo['debiti_finanziari'][$i];
    $totale = $breve + $lungo;

    $debt_structure['breve_termine'][] = round($breve, 0);
    $debt_structure['totale'][] = round($totale, 0);

    if ($totale > 0) {
        $debt_structure['breve_pct'][] = round(($breve / $totale) * 100, 0);
        $debt_structure['lungo_pct'][] = round(($lungo / $totale) * 100, 0);
    } else {
        $debt_structure['breve_pct'][] = 0;
        $debt_structure['lungo_pct'][] = 0;
    }
}

// Costo medio debito (stimato da oneri finanziari / debiti totali medi)
$costoMedioDebito = 0;
$debitiMedi = 0;
for ($i = 0; $i < $numYears; $i++) {
    $debitiMedi += $debt_structure['totale'][$i];
}
$debitiMedi = $debitiMedi / $numYears;

$oneriMedi = array_sum($income['oneri_finanziari']) / $numYears;
if ($debitiMedi > 0) {
    $costoMedioDebito = ($oneriMedi / $debitiMedi) * 100;
}
$debt_structure['costo_medio_debito'] = round($costoMedioDebito, 2);

// ===========================================
// INTEREST COVERAGE
// ===========================================
echo "🔢 Calcolo Interest Coverage...\n";

$interest_coverage = [
    'ebit' => $income['ebit'],
    'oneri_finanziari' => $income['oneri_finanziari'],
    'icr' => $kpi['solidita']['icr'],
    'costo_medio_debito' => $debt_structure['costo_medio_debito']
];

// ===========================================
// DUPONT ANALYSIS
// ===========================================
echo "📈 Calcolo DuPont Analysis...\n";

$dupont = [
    'margine_netto' => [],
    'rotazione_attivo' => $kpi['efficienza']['asset_turnover'],
    'leva_finanziaria' => $kpi['solidita']['leverage'],
    'roa' => $kpi['redditivita']['roa']
];

for ($i = 0; $i < $numYears; $i++) {
    $margineNetto = $income['ricavi'][$i] > 0
        ? ($income['utile_netto'][$i] / $income['ricavi'][$i]) * 100
        : 0;
    $dupont['margine_netto'][] = round($margineNetto, 2);
}

// ===========================================
// BREAK EVEN (solo ultimo anno)
// ===========================================
echo "🎯 Calcolo Break Even...\n";

// Stima costi fissi = personale + ammortamenti + parte di altri costi
$costiFissi = $income['costi_personale'][$lastIdx]
            + $income['ammortamenti'][$lastIdx]
            + ($income['altri_costi_operativi'][$lastIdx] * 0.5);  // 50% altri costi fissi

// Costi variabili = servizi + parte altri costi
$costiVariabili = $income['costi_servizi'][$lastIdx]
                + ($income['altri_costi_operativi'][$lastIdx] * 0.5);

$ricaviUltimoAnno = $income['ricavi'][$lastIdx];

// Margine di contribuzione = (Ricavi - Costi Variabili) / Ricavi
$margineContribuzione = $ricaviUltimoAnno > 0
    ? ($ricaviUltimoAnno - $costiVariabili) / $ricaviUltimoAnno
    : 0;

// Punto di pareggio = Costi Fissi / Margine di Contribuzione
$puntoPareggio = $margineContribuzione > 0
    ? $costiFissi / $margineContribuzione
    : 0;

// Margine di sicurezza = (Ricavi - Punto Pareggio) / Ricavi * 100
$margineSicurezzaPct = $ricaviUltimoAnno > 0
    ? (($ricaviUltimoAnno - $puntoPareggio) / $ricaviUltimoAnno) * 100
    : 0;

$margineSicurezzaEur = $ricaviUltimoAnno - $puntoPareggio;

$break_even = [
    'costi_fissi' => round($costiFissi, 0),
    'costi_variabili' => round($costiVariabili, 0),
    'margine_contribuzione' => round($margineContribuzione, 4),  // Decimale!
    'punto_pareggio' => round($puntoPareggio, 0),
    'margine_sicurezza_pct' => round($margineSicurezzaPct, 0),
    'margine_sicurezza_eur' => round($margineSicurezzaEur, 0),
    'ricavi_' . $fiscalYears[$lastIdx] => $ricaviUltimoAnno
];

// ===========================================
// Z-SCORE ALTMAN (Components per ultimo anno)
// ===========================================
$ta = $attivo['totale_attivo'][$lastIdx];
$wc = $attivo['attivo_circolante_totale'][$lastIdx]
    - ($passivo['debiti_commerciali'][$lastIdx] + $passivo['altri_debiti_correnti'][$lastIdx]);

$z_components = [
    'x1' => $ta > 0 ? round($wc / $ta, 2) : 0,
    'x2' => $ta > 0 ? round($passivo['riserve'][$lastIdx] / $ta, 2) : 0,
    'x3' => $ta > 0 ? round($income['ebit'][$lastIdx] / $ta, 2) : 0,
    'x4' => $passivo['debiti_totali'][$lastIdx] > 0 ? round($passivo['patrimonio_netto_totale'][$lastIdx] / $passivo['debiti_totali'][$lastIdx], 2) : 0,
    'x5' => $ta > 0 ? round($income['ricavi'][$lastIdx] / $ta, 2) : 0
];

$z_score_altman = [
    'punteggio' => $kpi['rischio']['z_score'],
    'zone' => [],
    'components_' . $fiscalYears[$lastIdx] => $z_components
];

// Determina zona per ogni anno
foreach ($kpi['rischio']['z_score'] as $z) {
    if ($z >= 2.9) {
        $z_score_altman['zone'][] = "safe";
    } else if ($z >= 1.23) {
        $z_score_altman['zone'][] = "grey";
    } else {
        $z_score_altman['zone'][] = "risk";
    }
}

// ===========================================
// PRODUTTIVITA
// ===========================================
echo "👥 Calcolo Produttività...\n";

// Stima dipendenti da costo personale (media 45K€ per FTE)
$costoMedioFTE = 45000;
$dipendentiStimati = round($income['costi_personale'][$lastIdx] / $costoMedioFTE, 0);

$produttivita = [
    'dipendenti' => [],
    'dipendenti_stimati' => $dipendentiStimati,  // NUMERO SINGOLO
    'ricavi_per_dipendente' => [],
    'valore_aggiunto_per_dipendente_euro' => []
];

for ($i = 0; $i < $numYears; $i++) {
    $dipStima = $income['costi_personale'][$i] / $costoMedioFTE;
    $produttivita['dipendenti'][] = round($dipStima, 0);

    $ricaviPerDip = $dipStima > 0 ? $income['ricavi'][$i] / $dipStima : 0;
    $produttivita['ricavi_per_dipendente'][] = round($ricaviPerDip, 0);

    // Valore aggiunto = EBITDA + Costi Personale
    $valoreAggiunto = $income['ebitda'][$i] + $income['costi_personale'][$i];
    $vaPerDip = $dipStima > 0 ? $valoreAggiunto / $dipStima : 0;
    $produttivita['valore_aggiunto_per_dipendente_euro'][] = round($vaPerDip, 0);
}

// ===========================================
// CAPEX
// ===========================================
echo "🏗️  Calcolo CAPEX...\n";

$capex = [
    'periodi' => [],
    'valori' => [],
    'dettagli' => []
];

for ($i = 1; $i < $numYears; $i++) {
    $periodo = $fiscalYears[$i-1] . '-' . $fiscalYears[$i];
    $deltaImmob = $attivo['immobilizzazioni_totale'][$i] - $attivo['immobilizzazioni_totale'][$i-1];
    $ammort = $income['ammortamenti'][$i];
    $totaleCapex = $deltaImmob + $ammort;

    $capex['periodi'][] = $periodo;
    $capex['valori'][] = round($totaleCapex, 0);
    $capex['dettagli'][] = [
        'periodo' => $periodo,
        'totale' => round($totaleCapex, 0),
        'delta_immobilizzazioni' => round($deltaImmob, 0),
        'ammortamenti' => round($ammort, 0),
        'nota' => $totaleCapex > 100000 ? "Ciclo investimento significativo" : "Investimenti ordinari"
    ];
}

// ===========================================
// COMPOSIZIONE (per grafici)
// ===========================================
$ultimoAnno = $fiscalYears[$lastIdx];

$composizione_attivo = [
    'labels' => ['Immob. Materiali', 'Immob. Immateriali', 'Immob. Finanziarie', 'Crediti', 'Liquidità', 'Altre Attività'],
    'valori_' . $ultimoAnno => [
        $attivo['immobilizzazioni_materiali'][$lastIdx],
        $attivo['immobilizzazioni_immateriali'][$lastIdx],
        $attivo['immobilizzazioni_finanziarie'][$lastIdx],
        $attivo['crediti_commerciali'][$lastIdx],
        $attivo['disponibilita_liquide'][$lastIdx],
        $attivo['altre_attivita_correnti'][$lastIdx]
    ]
];

$totaleAttivoLast = $attivo['totale_attivo'][$lastIdx];
$composizione_attivo['percentuali_' . $ultimoAnno] = [];
foreach ($composizione_attivo['valori_' . $ultimoAnno] as $val) {
    $pct = $totaleAttivoLast > 0 ? ($val / $totaleAttivoLast) * 100 : 0;
    $composizione_attivo['percentuali_' . $ultimoAnno][] = round($pct, 1);
}

$composizione_passivo = [
    'labels' => ['Patrimonio Netto', 'Debiti Breve', 'Debiti Lungo', 'TFR'],
    'valori_' . $ultimoAnno => [
        $passivo['patrimonio_netto_totale'][$lastIdx],
        $passivo['debiti_commerciali'][$lastIdx] + $passivo['altri_debiti_correnti'][$lastIdx],
        $passivo['debiti_finanziari'][$lastIdx],
        $passivo['tfr'][$lastIdx]
    ]
];

$totalePassivoLast = $passivo['totale_passivo'][$lastIdx];
$composizione_passivo['percentuali_' . $ultimoAnno] = [];
foreach ($composizione_passivo['valori_' . $ultimoAnno] as $val) {
    $pct = $totalePassivoLast > 0 ? ($val / $totalePassivoLast) * 100 : 0;
    $composizione_passivo['percentuali_' . $ultimoAnno][] = round($pct, 1);
}

// ===========================================
// ASSEMBLA OUTPUT
// ===========================================
$output = [
    'metadata' => $rawData['metadata'],
    'income_statement' => $rawData['income_statement'],
    'balance_sheet' => $rawData['balance_sheet'],
    'kpi' => $kpi,
    'cash_flow' => $cash_flow,
    'structure_margin' => $structure_margin,
    'debt_structure' => $debt_structure,
    'interest_coverage' => $interest_coverage,
    'dupont_analysis' => $dupont,
    'break_even' => $break_even,
    'z_score_altman' => $z_score_altman,
    'produttivita' => $produttivita,
    'capex' => $capex,
    'composizione_attivo' => $composizione_attivo,
    'composizione_passivo' => $composizione_passivo
];

// Salva JSON
$json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (file_put_contents($outputFile, $json)) {
    echo "\n✅ File creato: $outputFile\n";
    echo "📦 Dimensione: " . number_format(strlen($json) / 1024, 1) . " KB\n";
    echo "\n📊 Riepilogo KPI calcolati:\n";
    echo "  - ROE ultimo anno: " . $kpi['redditivita']['roe'][$lastIdx] . "%\n";
    echo "  - Current Ratio: " . $kpi['liquidita']['current_ratio'][$lastIdx] . "x\n";
    echo "  - Z-Score: " . $kpi['rischio']['z_score'][$lastIdx] . " (" . $z_score_altman['zone'][$lastIdx] . ")\n";
    echo "  - DSO: " . $kpi['efficienza']['dso'][$lastIdx] . " giorni\n";
} else {
    echo "\n❌ Errore nella scrittura del file: $outputFile\n";
    exit(1);
}

echo "\n🎉 Step 2 completato con successo!\n";
echo "➡️  Prossimo: Usa LLM per Step 3 (analisi e insights)\n";
echo "     Input: data-kpi.json\n";
echo "     Output: data-schema.json\n";
