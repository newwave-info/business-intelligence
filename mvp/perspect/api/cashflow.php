<?php
header('Content-Type: application/json; charset=utf-8');

// Read the CSV file
$csvFile = __DIR__ . '/../docs/2025-Movimenti_Cassa_Pulito.csv';

if (!file_exists($csvFile)) {
    echo json_encode(['error' => 'CSV file not found']);
    exit;
}

$transactions = [];
$handle = fopen($csvFile, 'r');

// Skip header
fgetcsv($handle);

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) >= 6) {
        $transactions[] = [
            'data' => $row[0],
            'descrizione' => $row[1],
            'categoria' => $row[2],
            'tipo' => $row[3],
            'importo' => floatval(str_replace(',', '.', $row[4])),
            'note' => $row[5]
        ];
    }
}
fclose($handle);

// Process data for different views
$cumulativeData = processCumulativeFlow($transactions);
$monthlyCheckpoints = generateMonthlyCheckpoints($transactions);
$monthlyCategories = generateMonthlyCategoryData($transactions);

function processCumulativeFlow($transactions) {
    $data = [
        'dates' => [],
        'entrate' => [],
        'uscite' => [],
        'saldo' => []
    ];

    $cumulativeEntrate = 0;
    $cumulativeUscite = 0;
    $currentMonth = null;

    // Sort by date
    usort($transactions, function($a, $b) {
        if ($a['data'] === 'N/D') return 1;
        if ($b['data'] === 'N/D') return -1;
        return strcmp($a['data'], $b['data']);
    });

    foreach ($transactions as $t) {
        if ($t['data'] === 'N/D') continue;

        $month = substr($t['data'], 0, 7); // YYYY-MM

        // Group by month
        if ($month !== $currentMonth) {
            if ($currentMonth !== null) {
                // Add month boundary
                $data['dates'][] = $month;
                $data['entrate'][] = $cumulativeEntrate;
                $data['uscite'][] = $cumulativeUscite;
                $data['saldo'][] = $cumulativeEntrate - $cumulativeUscite;
            }
            $currentMonth = $month;
        }

        // Accumulate
        if ($t['tipo'] === 'Entrata') {
            $cumulativeEntrate += $t['importo'];
        } elseif ($t['tipo'] === 'Uscita') {
            $cumulativeUscite += $t['importo'];
        }
    }

    // Add final values
    if ($currentMonth !== null) {
        $data['dates'][] = $currentMonth;
        $data['entrate'][] = $cumulativeEntrate;
        $data['uscite'][] = $cumulativeUscite;
        $data['saldo'][] = $cumulativeEntrate - $cumulativeUscite;
    }

    return $data;
}

function generateMonthlyCheckpoints($transactions) {
    $months = [];

    foreach ($transactions as $t) {
        if ($t['data'] === 'N/D') continue;

        $month = substr($t['data'], 0, 7);

        if (!isset($months[$month])) {
            $months[$month] = [
                'entrate' => 0,
                'uscite' => 0,
                'altro' => 0,
                'transazioni' => 0
            ];
        }

        if ($t['tipo'] === 'Entrata') {
            $months[$month]['entrate'] += $t['importo'];
        } elseif ($t['tipo'] === 'Uscita') {
            $months[$month]['uscite'] += $t['importo'];
        } else {
            $months[$month]['altro'] += $t['importo'];
        }

        $months[$month]['transazioni']++;
    }

    // Format for output
    $checkpoints = [];
    foreach ($months as $month => $data) {
        $monthNum = intval(substr($month, 5, 2));
        $monthNames = [
            'gen', 'feb', 'mar', 'apr', 'mag', 'giu',
            'lug', 'ago', 'set', 'ott', 'nov', 'dic'
        ];

        $saldo = $data['entrate'] - $data['uscite'];
        $checkpoints[$month] = [
            'mese' => $monthNames[$monthNum - 1] . ' 2025',
            'entrate' => round($data['entrate'], 2),
            'uscite' => round($data['uscite'], 2),
            'saldo' => round($saldo, 2),
            'transazioni' => $data['transazioni'],
            'trend' => $saldo > 0 ? 'positivo' : 'negativo'
        ];
    }

    return $checkpoints;
}

function generateMonthlyCategoryData($transactions) {
    $monthlyCategories = [];

    foreach ($transactions as $t) {
        if ($t['data'] === 'N/D') continue;

        $month = substr($t['data'], 0, 7);

        if (!isset($monthlyCategories[$month])) {
            $monthlyCategories[$month] = [];
        }

        if (!isset($monthlyCategories[$month][$t['categoria']])) {
            $monthlyCategories[$month][$t['categoria']] = 0;
        }

        $monthlyCategories[$month][$t['categoria']] += $t['importo'];
    }

    // Sort by month and get top categories per month
    ksort($monthlyCategories);

    $monthNames = [
        '2025-01' => 'Gen',
        '2025-02' => 'Feb',
        '2025-03' => 'Mar',
        '2025-04' => 'Apr',
        '2025-05' => 'Mag',
        '2025-06' => 'Giu',
        '2025-07' => 'Lug',
        '2025-08' => 'Ago',
        '2025-09' => 'Set',
        '2025-10' => 'Ott',
        '2025-11' => 'Nov',
        '2025-12' => 'Dic'
    ];

    $result = [
        'months' => [],
        'categories' => [],
        'data' => []
    ];

    $allCategories = [];

    foreach ($monthlyCategories as $month => $categories) {
        $result['months'][] = $monthNames[$month] ?? $month;

        foreach ($categories as $cat => $amount) {
            if (!in_array($cat, $allCategories)) {
                $allCategories[] = $cat;
            }
        }
    }

    sort($allCategories);
    $result['categories'] = $allCategories;

    // Build data matrix
    foreach ($allCategories as $cat) {
        $catData = [];
        foreach ($monthlyCategories as $month => $categories) {
            $catData[] = isset($categories[$cat]) ? round($categories[$cat], 2) : 0;
        }
        $result['data'][] = $catData;
    }

    return $result;
}

// Return JSON response
echo json_encode([
    'cumulativeFlow' => $cumulativeData,
    'monthlyCheckpoints' => $monthlyCheckpoints,
    'monthlyCategories' => $monthlyCategories
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
