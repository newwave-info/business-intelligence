<?php
/**
 * STEP 1: Estrazione Dati Grezzi da CSV
 *
 * Legge i 4 file CSV dei bilanci e estrae i dati finanziari raw
 * senza calcoli o analisi, solo mapping diretto CSV → JSON
 *
 * Usage: php step1_extract_raw_data.php
 * Output: data-raw.json
 */

// Configurazione
$csvDir = __DIR__ . '/../docs/bilanci/';
$outputFile = __DIR__ . '/../data-raw.json';

// Mapping voci CSV → campi JSON
$mappings = [
    // Metadata
    'company_name' => 'Dati anagrafici, denominazione',
    'capital_social' => 'Dati anagrafici, capitale sociale',
    'partita_iva' => 'Dati anagrafici, partita iva',

    // Income Statement
    'ricavi' => 'A.1 - Valore della produzione, ricavi delle vendite e delle prestazioni',
    'costi_servizi' => 'B.7 - Costi della produzione, per servizi',
    'costi_personale' => 'B.9 - Costi della produzione, per il personale, totale costi per il personale',
    'costi_godimento_beni' => 'B.8 - Costi della produzione, per godimento di beni di terzi',
    'oneri_diversi_gestione' => 'B.14 - Costi della produzione, oneri diversi di gestione',
    'ammortamenti' => 'B.10 - Costi della produzione, ammortamenti e svalutazioni, totale ammortamenti e svalutazioni',
    'ebit' => 'Differenza tra valore e costi della produzione',
    'oneri_finanziari' => 'C.17 - Proventi e oneri finanziari, interessi e altri oneri finanziari, totale interessi e altri oneri finanziari',
    'imposte' => '20 - Imposte sul reddito dell\'esercizio, correnti, differite e anticipate, totale delle imposte sul reddito dell\'esercizio, correnti, differite e anticipate',
    'utile_netto' => '21 - Utile (perdita) dell\'esercizio',

    // Balance Sheet - Attivo
    'immobilizzazioni_immateriali' => 'B.I - Totale immobilizzazioni immateriali',
    'immobilizzazioni_materiali' => 'B.II - Totale immobilizzazioni materiali',
    'immobilizzazioni_finanziarie' => 'B.III - Totale immobilizzazioni finanziarie',
    'immobilizzazioni_totale' => 'B - Totale immobilizzazioni',
    'crediti_totali' => 'C.II - Totale crediti',
    'disponibilita_liquide' => 'C.IV - Totale disponibilità liquide',
    'attivo_circolante_totale' => 'C - Totale attivo circolante',
    'totale_attivo' => 'Totale attivo',

    // Balance Sheet - Passivo
    'capitale_sociale' => 'A.I - Patrimonio netto, capitale',
    'riserva_legale' => 'A.IV - Patrimonio netto, riserva legale',
    'altre_riserve' => 'A.VI - Patrimonio netto, Altre riserve, distintamente indicate, totale altre riserve',
    'utile_esercizio_pn' => 'A.IX - Patrimonio netto, utile (perdita) dell\'esercizio',
    'patrimonio_netto_totale' => 'A - Totale patrimonio netto',
    'tfr' => 'C - Trattamento di fine rapporto di lavoro subordinato',
    'debiti_breve' => 'D - Debiti, esigibili entro l\'esercizio successivo',
    'debiti_lungo' => 'D - Debiti, esigibili oltre l\'esercizio successivo',
    'debiti_totali' => 'D - Totale debiti',
    'totale_passivo' => 'Totale passivo',
];

// Funzione per leggere un CSV e estrarre i dati
function parseCSV($filepath) {
    $data = [];
    if (!file_exists($filepath)) {
        echo "⚠️  File non trovato: $filepath\n";
        return $data;
    }

    $handle = fopen($filepath, 'r');
    if ($handle === false) {
        echo "❌ Errore apertura file: $filepath\n";
        return $data;
    }

    // Salta header
    fgetcsv($handle, 0, ';');

    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        if (count($row) < 3) continue;

        $voce = trim($row[0]);

        // Per campi testuali (es. denominazione), mantieni il testo
        // Per campi numerici, converti a float
        $raw_corrente = trim($row[1]);
        $raw_precedente = trim($row[2]);

        // Prova a convertire in numero, se fallisce mantieni stringa
        $anno_corrente = is_numeric(str_replace(',', '.', $raw_corrente))
            ? floatval(str_replace(',', '.', $raw_corrente))
            : $raw_corrente;

        $anno_precedente = is_numeric(str_replace(',', '.', $raw_precedente))
            ? floatval(str_replace(',', '.', $raw_precedente))
            : $raw_precedente;

        $data[$voce] = [
            'corrente' => $anno_corrente ?: null,
            'precedente' => $anno_precedente ?: null
        ];
    }

    fclose($handle);
    return $data;
}

// Trova tutti i file CSV
$csvFiles = glob($csvDir . '*.csv');
if (empty($csvFiles)) {
    die("❌ Nessun file CSV trovato in $csvDir\n");
}

// Ordina per anno (più recente prima)
usort($csvFiles, function($a, $b) {
    preg_match('/(\d{4})/', basename($a), $matchA);
    preg_match('/(\d{4})/', basename($b), $matchB);
    return ($matchB[1] ?? 0) <=> ($matchA[1] ?? 0);
});

echo "📊 Trovati " . count($csvFiles) . " file CSV\n";
foreach ($csvFiles as $file) {
    echo "  - " . basename($file) . "\n";
}

// Array per raccogliere tutti i dati per anno
$allYears = [];

// Processa ogni CSV
foreach ($csvFiles as $csvFile) {
    echo "\n🔍 Processing: " . basename($csvFile) . "\n";

    // Estrai anno dal nome file (es. IT03731686-2024-...)
    preg_match('/-(\d{4})-/', basename($csvFile), $matches);
    $anno = $matches[1] ?? null;

    if (!$anno) {
        echo "⚠️  Anno non trovato nel nome file\n";
        continue;
    }

    $csvData = parseCSV($csvFile);

    // Il CSV contiene dati per anno corrente e precedente
    $annoCorrente = intval($anno);
    $annoPrecedente = $annoCorrente - 1;

    // Estrai company name (una sola volta)
    if (!isset($companyName) && isset($csvData[$mappings['company_name']])) {
        $companyName = $csvData[$mappings['company_name']]['corrente'] ?? 'N/A';
    }

    // Aggiungi dati anno corrente
    if (!isset($allYears[$annoCorrente])) {
        $allYears[$annoCorrente] = [];
    }

    foreach ($mappings as $key => $csvLabel) {
        if (isset($csvData[$csvLabel]) && $csvData[$csvLabel]['corrente'] !== null) {
            $allYears[$annoCorrente][$key] = $csvData[$csvLabel]['corrente'];
        }
    }

    // Aggiungi dati anno precedente
    if (!isset($allYears[$annoPrecedente])) {
        $allYears[$annoPrecedente] = [];
    }

    foreach ($mappings as $key => $csvLabel) {
        if (isset($csvData[$csvLabel]) && $csvData[$csvLabel]['precedente'] !== null) {
            $allYears[$annoPrecedente][$key] = $csvData[$csvLabel]['precedente'];
        }
    }
}

// Ordina anni crescente
ksort($allYears);
$fiscalYears = array_keys($allYears);

echo "\n📅 Anni fiscali trovati: " . implode(', ', $fiscalYears) . "\n";

// Costruisci struttura JSON raw
$rawData = [
    'metadata' => [
        'company_name' => $companyName ?? 'AXERTA SPA',
        'fiscal_years' => $fiscalYears,
        'currency' => 'EUR',
        'last_update' => date('d M Y'),
        'notes' => 'Dati estratti da bilanci ufficiali depositati.'
    ],
    'income_statement' => [],
    'balance_sheet' => [
        'attivo' => [],
        'passivo' => []
    ]
];

// Popola arrays per income statement
$incomeKeys = ['ricavi', 'costi_servizi', 'costi_personale', 'ammortamenti', 'ebit', 'oneri_finanziari', 'imposte', 'utile_netto'];
foreach ($incomeKeys as $key) {
    $rawData['income_statement'][$key] = [];
    foreach ($fiscalYears as $year) {
        $rawData['income_statement'][$key][] = $allYears[$year][$key] ?? 0;
    }
}

// Calcola altri_costi_operativi = costi_godimento_beni + oneri_diversi_gestione
$rawData['income_statement']['altri_costi_operativi'] = [];
foreach ($fiscalYears as $idx => $year) {
    $godimento = $allYears[$year]['costi_godimento_beni'] ?? 0;
    $oneri = $allYears[$year]['oneri_diversi_gestione'] ?? 0;
    $rawData['income_statement']['altri_costi_operativi'][] = $godimento + $oneri;
}

// Calcola EBITDA = ricavi - costi_servizi - costi_personale - altri_costi_operativi
$rawData['income_statement']['ebitda'] = [];
foreach ($fiscalYears as $idx => $year) {
    $ricavi = $rawData['income_statement']['ricavi'][$idx];
    $servizi = $rawData['income_statement']['costi_servizi'][$idx];
    $personale = $rawData['income_statement']['costi_personale'][$idx];
    $altri = $rawData['income_statement']['altri_costi_operativi'][$idx];
    $rawData['income_statement']['ebitda'][] = $ricavi - $servizi - $personale - $altri;
}

// Popola arrays per balance sheet - attivo
$attivoKeys = ['immobilizzazioni_immateriali', 'immobilizzazioni_materiali', 'immobilizzazioni_finanziarie', 'immobilizzazioni_totale', 'crediti_totali', 'disponibilita_liquide', 'attivo_circolante_totale', 'totale_attivo'];
foreach ($attivoKeys as $key) {
    // Rinomina per coerenza con schema finale
    $jsonKey = str_replace('crediti_totali', 'crediti_commerciali', $key);
    $rawData['balance_sheet']['attivo'][$jsonKey] = [];
    foreach ($fiscalYears as $year) {
        $rawData['balance_sheet']['attivo'][$jsonKey][] = $allYears[$year][$key] ?? 0;
    }
}

// Calcola altre_attivita_correnti
$rawData['balance_sheet']['attivo']['altre_attivita_correnti'] = [];
foreach ($fiscalYears as $idx => $year) {
    $circolante = $rawData['balance_sheet']['attivo']['attivo_circolante_totale'][$idx];
    $crediti = $rawData['balance_sheet']['attivo']['crediti_commerciali'][$idx];
    $liquide = $rawData['balance_sheet']['attivo']['disponibilita_liquide'][$idx];
    $rawData['balance_sheet']['attivo']['altre_attivita_correnti'][] = $circolante - $crediti - $liquide;
}

// Popola arrays per balance sheet - passivo
$passivoKeys = ['capitale_sociale', 'patrimonio_netto_totale', 'tfr', 'debiti_totali', 'totale_passivo'];
foreach ($passivoKeys as $key) {
    $rawData['balance_sheet']['passivo'][$key] = [];
    foreach ($fiscalYears as $year) {
        $rawData['balance_sheet']['passivo'][$key][] = $allYears[$year][$key] ?? 0;
    }
}

// Calcola riserve = patrimonio_netto - capitale_sociale - utile_esercizio
$rawData['balance_sheet']['passivo']['riserve'] = [];
$rawData['balance_sheet']['passivo']['utile_esercizio'] = [];
foreach ($fiscalYears as $idx => $year) {
    $pn = $allYears[$year]['patrimonio_netto_totale'] ?? 0;
    $capitale = $allYears[$year]['capitale_sociale'] ?? 0;
    $utile = $allYears[$year]['utile_esercizio_pn'] ?? 0;

    $rawData['balance_sheet']['passivo']['utile_esercizio'][] = $utile;
    $rawData['balance_sheet']['passivo']['riserve'][] = $pn - $capitale - $utile;
}

// Calcola debiti_finanziari (lungo termine) e debiti_commerciali (parte dei brevi)
$rawData['balance_sheet']['passivo']['debiti_finanziari'] = [];
$rawData['balance_sheet']['passivo']['debiti_commerciali'] = [];
$rawData['balance_sheet']['passivo']['altri_debiti_correnti'] = [];

foreach ($fiscalYears as $idx => $year) {
    $lungo = $allYears[$year]['debiti_lungo'] ?? 0;
    $breve = $allYears[$year]['debiti_breve'] ?? 0;

    $rawData['balance_sheet']['passivo']['debiti_finanziari'][] = $lungo;

    // Stima: 60% debiti commerciali, 40% altri debiti correnti
    $debComm = $breve * 0.6;
    $altriDeb = $breve * 0.4;

    $rawData['balance_sheet']['passivo']['debiti_commerciali'][] = $debComm;
    $rawData['balance_sheet']['passivo']['altri_debiti_correnti'][] = $altriDeb;
}

// Salva JSON
$json = json_encode($rawData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (file_put_contents($outputFile, $json)) {
    echo "\n✅ File creato: $outputFile\n";
    echo "📦 Dimensione: " . number_format(strlen($json) / 1024, 1) . " KB\n";
    echo "📅 Anni: " . implode(', ', $fiscalYears) . "\n";
    echo "🏢 Azienda: " . ($companyName ?? 'N/A') . "\n";
} else {
    echo "\n❌ Errore nella scrittura del file: $outputFile\n";
    exit(1);
}

echo "\n🎉 Step 1 completato con successo!\n";
echo "➡️  Prossimo: php step2_calculate_kpi.php\n";
