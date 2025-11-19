<!DOCTYPE html>
<?php
    // Carica il data schema JSON
    $json_file = __DIR__ . '/data-schema.json';

    if (!file_exists($json_file)) {
        die('Errore: data-schema.json non trovato');
    }

    $json_content = file_get_contents($json_file);
    $data = json_decode($json_content, true);

    if ($data === null) {
        die('Errore: JSON non valido');
    }

    // Estrai le sezioni principali
    $metadata = $data['metadata'] ?? [];
    $income = $data['income_statement'] ?? [];
    $balance = $data['balance_sheet'] ?? [];
    $kpi = $data['kpi'] ?? [];
    $executive = $data['executive_summary'] ?? [];
    $risks = $data['risk_priorities'] ?? [];
    $risk_matrix = $data['risk_matrix'] ?? [];
    $z_score = $data['z_score_altman'] ?? [];
    $dupont = $data['dupont_analysis'] ?? [];
    $break_even = $data['break_even'] ?? [];
    $debt = $data['debt_structure'] ?? [];
    $interest_coverage = $data['interest_coverage'] ?? [];
    $productivity = $data['produttivita'] ?? [];
    $capex = $data['capex'] ?? [];
    $documents = $data['documents'] ?? [];
    $ai_insights = $data['ai_insights'] ?? [];

    // Variabili dinamiche per anni e indici
    $fiscal_years = $metadata['fiscal_years'] ?? [];
    $num_years = count($fiscal_years);
    $last_idx = $num_years - 1;
    $prev_idx = $num_years > 1 ? $num_years - 2 : 0;
    $first_idx = 0;
    $last_year = $fiscal_years[$last_idx] ?? '';
    $prev_year = $fiscal_years[$prev_idx] ?? '';
    $first_year = $fiscal_years[$first_idx] ?? '';
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($metadata['company_name'] ?? 'Business Intelligence'); ?> - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/patternomaly@1.3.2/dist/patternomaly.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tablesort@5.3.0/dist/tablesort.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tablesort@5.3.0/dist/sorts/tablesort.number.min.js"></script>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#18181b',
                        secondary: '#27272a',
                        accent: '#8b5cf6',
                        positive: '#22c55e',
                        success: '#22c55e',
                        'success-light': '#dcfce7',
                        'success-dark': '#16a34a',
                        danger: '#ef4444',
                        'danger-light': '#fee2e2',
                        'danger-dark': '#dc2626',
                        purple: '#8b5cf6',
                        'purple-light': '#ede9fe',
                        'purple-dark': '#7c3aed',
                        warning: '#f59e0b',
                        'warning-light': '#fef3c7',
                        negative: '#ef4444',
                    }
                }
            }
        }
    </script>
</head>
<body class="flex flex-col h-screen overflow-hidden bg-gray-50">
    <!-- Top Header - Full Width -->
    <div class="h-[60px] bg-white border-b border-gray-200 px-6 flex items-center justify-between z-50 shrink-0">
        <div>
            <h2 class="text-[15px] font-medium text-primary"><?php echo htmlspecialchars($metadata['company_name'] ?? 'Azienda'); ?></h2>
            <p class="text-[11px] text-gray-500">Business Intelligence Suite</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-[11px] text-gray-500 hidden sm:block">
                Ultimo aggiornamento: <strong class="text-gray-700"><?php echo htmlspecialchars($metadata['last_update'] ?? ''); ?></strong>
            </div>
            <button id="themeToggle" class="text-gray-500 hover:text-purple text-lg transition-colors" onclick="toggleTheme()" title="Cambia tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <button id="mobileMenuBtn" class="md:hidden text-primary text-xl" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Overlay Mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="w-[242px] bg-white border-r border-gray-200 fixed h-screen left-0 top-[60px] z-40 overflow-y-auto flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300">
        <div class="px-4 py-6 flex-1">
            <button class="w-full flex items-center text-[13px] font-semibold text-gray-400 px-2 py-2 pb-4 cursor-not-allowed transition-colors duration-200 border-b border-gray-200 btn-reset" disabled>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-building text-[12px]"></i>
                    <span class="text-left">Company Overview</span>
                </div>
            </button>
            <button class="w-full flex items-center text-[13px] font-semibold text-purple-600 px-2 py-2 pt-4 group transition-colors duration-200 btn-reset" data-accordion-toggle="cgMenu" onclick="toggleAccordion('cgMenu')">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-[12px] text-purple-600 transition-colors duration-200"></i>
                    <span class="text-purple-600 transition-colors duration-200 text-left">Controllo di Gestione</span>
                </div>
                <i class="accordion-icon fa-solid fa-chevron-down text-[10px] text-purple-600 transition-transform duration-200 rotate-180 ml-auto"></i>
            </button>
            <div id="cgMenu" class="accordion-content mt-2">
                <div class="ml-3 border-l border-gray-200 space-y-1">
                    <div class="nav-item active flex items-center gap-2.5 pl-4 py-1.5 cursor-pointer text-[13px] text-purple-600 font-medium transition-colors duration-200 hover:text-purple-600" onclick="showView('dashboard'); toggleSidebar()">
                        <i class="fa-solid fa-gauge text-[11px] text-current"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="nav-item flex items-center gap-2.5 pl-4 py-1.5 cursor-pointer text-[13px] text-gray-600 transition-colors duration-200 hover:text-purple-600" onclick="showView('liquidity'); toggleSidebar()">
                        <i class="fa-solid fa-droplet text-[11px] text-current"></i>
                        <span>Liquidità e Flussi</span>
                    </div>
                    <div class="nav-item flex items-center gap-2.5 pl-4 py-1.5 cursor-pointer text-[13px] text-gray-600 transition-colors duration-200 hover:text-purple-600" onclick="showView('reports'); toggleSidebar()">
                        <i class="fa-solid fa-file text-[11px] text-current"></i>
                        <span>Bilanci</span>
                    </div>
                    <div class="nav-item flex items-center gap-2.5 pl-4 py-1.5 cursor-pointer text-[13px] text-gray-600 transition-colors duration-200 hover:text-purple-600" onclick="showView('analysis'); toggleSidebar()">
                        <i class="fa-solid fa-magnifying-glass text-[11px] text-current"></i>
                        <span>Analisi Avanzata</span>
                    </div>
                    <div class="nav-item flex items-center gap-2.5 pl-4 py-1.5 cursor-pointer text-[13px] text-gray-600 transition-colors duration-200 hover:text-purple-600" onclick="showView('risk'); toggleSidebar()">
                        <i class="fa-solid fa-shield text-[11px] text-current"></i>
                        <span>Rischio e Scoring</span>
                    </div>
                    <div class="nav-item flex items-center gap-2.5 pl-4 py-1.5 cursor-pointer text-[13px] text-gray-600 transition-colors duration-200 hover:text-purple-600" onclick="showView('documents'); toggleSidebar()">
                        <i class="fa-solid fa-folder text-[11px] text-current"></i>
                        <span>Archivio Documenti</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-4 pt-4">
                <button class="w-full flex items-center text-[13px] font-semibold text-gray-600 px-2 py-2 group transition-colors duration-200 btn-reset" data-accordion-toggle="availableMenu" onclick="toggleAccordion('availableMenu')">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-[12px] text-gray-400 transition-colors duration-200"></i>
                        <span class="transition-colors duration-200 text-left">Analisi Disponibili</span>
                    </div>
                    <i class="accordion-icon fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200 ml-auto"></i>
                </button>
                <div id="availableMenu" class="accordion-content hidden mt-2">
                <div class="ml-3 border-l border-gray-200 space-y-1">
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>Brand</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>Digital</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>Social</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>Web</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>ESG</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>EAA</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] item-disabled">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        <span>Innovation</span>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="ml-0 md:ml-[242px] flex-1 flex flex-col overflow-hidden">
        <!-- Content -->
        <div class="flex-1 overflow-y-auto px-4 md:px-6 py-6 sm:py-10">
            
            <!-- View: Dashboard -->
            <div id="dashboard" class="view">
                <div class="mb-6 sm:mb-10">
                    <h1 class="text-[18px] sm:text-[20px] font-medium text-primary tracking-tight">Dashboard Esecutivo</h1>
                </div>

                <!-- Radar + Growth -->
                <div class="mb-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-6 transition-all duration-300">
                            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Indicatori Chiave</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                        <div class="tooltip-content">Confronto tra performance effettiva <?php echo $last_year; ?> e target ottimali. Valori normalizzati 0-100.</div>
                                    </div>
                                </div>
                                <div id="radarChart-legend" class="flex gap-4"></div>
                            </div>
                            <div class="relative h-[250px] sm:h-[320px] mb-5">
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>

                        <div class="widget-card widget-purple p-6 h-full flex flex-col">
                            <div class="flex items-center gap-2 mb-5 pb-4 border-b border-gray-200">
                                <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Executive Summary <?php echo $last_year; ?></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-auto h-full">
                                <?php
                                    $aree = $executive['aree'] ?? [];
                                    $icone_stati = [
                                        'Ottima' => 'check',
                                        'Forte' => 'check',
                                        'Buona' => 'check',
                                        'Basso' => 'check',
                                        'Attenzione' => 'triangle-exclamation',
                                        'Migliorabile' => 'triangle-exclamation'
                                    ];

                                    foreach ($aree as $key => $area):
                                        $stato = $area['stato'] ?? '';
                                        $valore = $area['valore'] ?? '';
                                        $css_class = $area['css_class'] ?? 'bg-gray-50 border border-gray-200';
                                        $icona = $icone_stati[$stato] ?? 'info';
                                        $color_icon = ($icona === 'triangle-exclamation') ? 'text-danger' : 'text-purple';
                                        $label = ucfirst(str_replace('_', ' ', $key));
                                ?>
                                <div class="p-4 flex flex-col justify-between <?php echo $css_class; ?>">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1"><?php echo $label; ?></div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-<?php echo $icona; ?> <?php echo $color_icon; ?>"></i>
                                            <?php echo htmlspecialchars($stato); ?>
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2"><?php echo htmlspecialchars($valore); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Insight -->
                <div class="mb-12 sm:mb-20">
                    <div class="widget-ai-insight px-6 py-4 transition-all duration-300">
                        <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-blue-200/40">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            <span class="text-xs font-semibold text-primary uppercase tracking-wide">Riepilogo Esecutivo <?php echo $last_year; ?></span>
                        </div>
                        <div class="text-[13px] leading-relaxed text-gray-700">
                            <?php
                                $exec_insights = $ai_insights['executive_summary'] ?? [];
                                $negativi = $exec_insights['negativi'] ?? [];
                                $positivi = $exec_insights['positivi'] ?? [];
                                $total_items = count($negativi) + count($positivi);
                                $current = 0;

                                // Display negative items first (danger color)
                                foreach ($negativi as $item):
                                    $current++;
                                    $border_class = $current < $total_items ? 'border-b border-dashed border-gray-300' : '';
                            ?>
                            <div class="pl-4 relative py-2 <?php echo $border_class; ?>"><span class="absolute left-0 text-danger font-bold">→</span> <?php echo htmlspecialchars($item); ?></div>
                            <?php endforeach; ?>

                            <?php
                                // Display positive items (purple color)
                                foreach ($positivi as $item):
                                    $current++;
                                    $border_class = $current < $total_items ? 'border-b border-dashed border-gray-300' : '';
                            ?>
                            <div class="pl-4 relative py-2 <?php echo $border_class; ?>"><span class="absolute left-0 text-purple font-bold">→</span> <?php echo htmlspecialchars($item); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>


                <!-- Strategic Insights -->
                <div class="mb-12 sm:mb-20">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Insight Strategici</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <?php
                            // ROE calculation
                            $roe_last = $kpi['redditivita']['roe'][$last_idx] ?? 0;
                            $roe_prev = $kpi['redditivita']['roe'][$prev_idx] ?? 0;
                            $roe_diff = $roe_last - $roe_prev;
                            $utile_netto = $income['utile_netto'][$last_idx] ?? 0;
                            $patrimonio = $balance['passivo']['totale_patrimonio_netto'][$last_idx] ?? 0;

                            // Z-Score calculation
                            $zscore_last = $kpi['rischio']['z_score'][$last_idx] ?? 0;
                            $zscore_prev = $kpi['rischio']['z_score'][$prev_idx] ?? 0;
                            $zscore_var = $zscore_prev > 0 ? (($zscore_last - $zscore_prev) / $zscore_prev) * 100 : 0;

                            // Leva Operativa (EBIT growth / Revenue growth)
                            $ebit_growth = $income['ebit'][$prev_idx] > 0 ? (($income['ebit'][$last_idx] - $income['ebit'][$prev_idx]) / $income['ebit'][$prev_idx]) * 100 : 0;
                            $rev_growth = $income['ricavi'][$prev_idx] > 0 ? (($income['ricavi'][$last_idx] - $income['ricavi'][$prev_idx]) / $income['ricavi'][$prev_idx]) * 100 : 0;
                            $leva_op = $rev_growth > 0 ? $ebit_growth / $rev_growth : 0;

                            // Break-even
                            $be_punto = $break_even['punto_pareggio'] ?? 0;
                            $be_margine_pct = $break_even['margine_sicurezza_pct'] ?? 0;
                            $be_margine_eur = $break_even['margine_sicurezza_eur'] ?? 0;

                            // Produttività
                            $costo_pers_prev = $income['costi_personale'][$prev_idx] ?? 0;
                            $costo_pers_last = $income['costi_personale'][$last_idx] ?? 0;
                            $prod_var = $costo_pers_prev > 0 ? (($costo_pers_last - $costo_pers_prev) / $costo_pers_prev) * 100 : 0;
                            $ebitda_last = $income['ebitda'][$last_idx] ?? 0;
                            $ebitda_prev = $income['ebitda'][$prev_idx] ?? 0;
                            $ebitda_var = $ebitda_prev > 0 ? (($ebitda_last - $ebitda_prev) / $ebitda_prev) * 100 : 0;

                            // DSO
                            $dso_last = $kpi['efficienza']['dso'][$last_idx] ?? 0;
                            $dso_prev = $kpi['efficienza']['dso'][$prev_idx] ?? 0;
                            $dso_diff = $dso_last - $dso_prev;
                            $cassa_lib = $risks[0]['cassa_liberabile'] ?? 0;
                        ?>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">ROE <?php echo number_format($roe_last, 1, '.', ''); ?>% (<?php echo ($roe_diff >= 0 ? '+' : ''); ?><?php echo number_format($roe_diff, 1, '.', ''); ?>pp)</div>
                            <div class="widget-text">Rendimento eccezionale del capitale proprio. Utile netto €<?php echo number_format($utile_netto / 1000, 0, '.', ''); ?>k su patrimonio €<?php echo number_format($patrimonio / 1000, 0, '.', ''); ?>k.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Z-Score <?php echo number_format($zscore_last, 2, '.', ''); ?> (<?php echo ($zscore_var >= 0 ? '+' : ''); ?><?php echo number_format($zscore_var, 0, '.', ''); ?>%)</div>
                            <div class="widget-text">Zona sicura raggiunta (>2.9). Rischio insolvenza azzerato vs <?php echo number_format($zscore_prev, 2, '.', ''); ?> nel <?php echo $prev_year; ?>.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Leva Operativa <?php echo number_format($leva_op, 1, '.', ''); ?>x</div>
                            <div class="widget-text">EBIT cresce <?php echo number_format($leva_op, 1, '.', ''); ?>x più veloce dei ricavi vs <?php echo $prev_year; ?>. Modello ad alta sensibilità al volume.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Break-Even €<?php echo number_format($be_punto / 1000, 0, '.', ''); ?>k</div>
                            <div class="widget-text">Margine di sicurezza <?php echo $be_margine_pct; ?>% vs <?php echo $prev_year; ?>. Ricavi possono scendere di €<?php echo number_format($be_margine_eur / 1000, 0, '.', ''); ?>k prima di perdite.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Produttività +<?php echo number_format($prod_var, 0, '.', ''); ?>%</div>
                            <div class="widget-text">EBITDA €<?php echo number_format($ebitda_last / 1000, 0, '.', ''); ?>k (+<?php echo number_format($ebitda_var, 0, '.', ''); ?>%) vs <?php echo $prev_year; ?>. Team scalabile.</div>
                        </div>
                        <div class="widget-card widget-negative p-6">
                            <div class="widget-title"><i class="fa-solid fa-triangle-exclamation text-negative"></i> DSO <?php echo $dso_last; ?>gg</div>
                            <div class="widget-text"><?php echo $dso_diff; ?>gg vs <?php echo $prev_year; ?> ma target 120gg. Riduzione libera €<?php echo number_format($cassa_lib / 1000, 0, '.', ''); ?>k cassa.</div>
                        </div>
                    </div>
                </div>

                <!-- KPI Strategici Aggiunti -->
                <div class="mb-5">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Grafici Storici</div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-6">
                            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Andamento D/E e ROE</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                        <div class="tooltip-content">Evoluzione storica del rapporto debiti/equity e del rendimento sul capitale proprio.</div>
                                    </div>
                                </div>
                                <div id="debtROEChart-legend" class="flex gap-4"></div>
                            </div>
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="debtROEChart"></canvas></div>
                            <div class="ai-insight-box">
                                <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                    <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                                </div>
                                <strong><i class="fa-solid fa-scale-balanced text-purple"></i> Leva Finanziaria Ottimale:</strong> <?php echo htmlspecialchars($ai_insights['leva_finanziaria'] ?? ''); ?>
                            </div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Andamento Costi e DSO</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                        <div class="tooltip-content">Evoluzione storica dell'incidenza costi sui ricavi e dei giorni di incasso.</div>
                                    </div>
                                </div>
                                <div id="costDSOChart-legend" class="flex gap-4"></div>
                            </div>
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="costDSOChart"></canvas></div>
                            <div class="ai-insight-box">
                                <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                    <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                                </div>
                                <strong><i class="fa-solid fa-chart-line text-purple"></i> Efficienza Operativa:</strong> <?php echo htmlspecialchars($ai_insights['efficienza_costi_dso'] ?? ''); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="mb-12 sm:mb-20">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-6">
                            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Andamento Storico Ricavi e EBITDA</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                        <div class="tooltip-content">Evoluzione triennale dei ricavi e dell'EBITDA. Valori in migliaia di euro.</div>
                                    </div>
                                </div>
                                <div id="revenueChart-legend" class="flex gap-4"></div>
                            </div>
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="revenueChart"></canvas></div>
                            <div class="ai-insight-box">
                                <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                    <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                                </div>
                                <strong><i class="fa-solid fa-chart-line text-purple"></i> Crescita Accelerata:</strong> <?php echo htmlspecialchars($ai_insights['crescita_ricavi'] ?? ''); ?>
                            </div>
                        </div>

                        <div class="widget-card widget-purple p-6">
                            <div class="flex justify-between items-center mb-5 pb-4 border-b border-gray-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Andamento Storico EBIT e Utile</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                        <div class="tooltip-content">Evoluzione triennale dell'utile operativo (EBIT) e dell'utile netto.</div>
                                    </div>
                                </div>
                                <div id="profitChart-legend" class="flex gap-4"></div>
                            </div>
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="profitChart"></canvas></div>
                            <div class="ai-insight-box">
                                <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                    <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                                </div>
                                <strong><i class="fa-solid fa-coins text-purple"></i> Profittabilità Esplosa:</strong> <?php echo htmlspecialchars($ai_insights['profittabilita'] ?? ''); ?>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div id="liquidity" class="view hidden">
                <div class="mb-6 sm:mb-10">
                    <h1 class="text-[18px] sm:text-[20px] font-medium text-primary tracking-tight">Liquidità e Flussi di Cassa</h1>
                </div>

                <!-- Working Capital Cycle -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Ciclo del Capitale Circolante</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">DSO - DPO = giorni di finanziamento necessario. Minore è meglio.</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                        <?php
                            // DSO data
                            $dso_last = $kpi['efficienza']['dso'][$last_idx] ?? 0;
                            $dso_prev = $kpi['efficienza']['dso'][$prev_idx] ?? 0;
                            $dso_var = $dso_prev > 0 ? (($dso_last - $dso_prev) / $dso_prev) * 100 : 0;

                            // DPO data
                            $dpo_last = $kpi['efficienza']['dpo'][$last_idx] ?? 0;
                            $dpo_prev = $kpi['efficienza']['dpo'][$prev_idx] ?? 0;
                            $dpo_var = $dpo_prev > 0 ? (($dpo_last - $dpo_prev) / $dpo_prev) * 100 : 0;

                            // Ciclo Finanziario data
                            $ciclo_last = $kpi['efficienza']['ciclo_finanziario'][$last_idx] ?? 0;
                            $ciclo_prev = $kpi['efficienza']['ciclo_finanziario'][$prev_idx] ?? 0;
                            $ciclo_var = $ciclo_prev > 0 ? (($ciclo_last - $ciclo_prev) / $ciclo_prev) * 100 : 0;
                        ?>
                        <div class="widget-card widget-negative p-6 text-left">
                            <div class="widget-label mb-2">Giorni Incasso (DSO)</div>
                            <div class="widget-metric-large text-negative"><?php echo number_format($dso_var, 0, '.', ''); ?>%</div>
                            <div class="text-sm text-gray-500"><?php echo $dso_last; ?> giorni</div>
                            <div class="text-xs text-gray-400 mt-1">Target: 120 giorni</div>
                            <div class="widget-status-badge mt-2 bg-red-100 text-red-700 text-[9px]"><i class="fa-solid fa-triangle-exclamation"></i> Sopra target</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">Giorni Pagamento (DPO)</div>
                            <div class="widget-metric-large text-gray-500"><?php echo number_format($dpo_var, 0, '.', ''); ?>%</div>
                            <div class="text-sm text-gray-500"><?php echo $dpo_last; ?> giorni</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">Ciclo Finanziario</div>
                            <div class="widget-metric-large text-positive"><?php echo number_format($ciclo_var, 0, '.', ''); ?>%</div>
                            <div class="text-sm text-gray-500"><?php echo $ciclo_last; ?> giorni</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">Cassa Liberabile</div>
                            <?php
                                $cassa_lib = $risks[0]['cassa_liberabile'] ?? 0;
                                $cassa_lib_k = number_format($cassa_lib / 1000, 0, '.', '');
                            ?>
                            <div class="widget-metric-large">€<?php echo $cassa_lib_k; ?>k</div>
                            <div class="text-xs text-gray-500 mt-1">Con DSO a 120gg</div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-clock text-purple"></i> Efficienza Operativa:</strong> <?php echo htmlspecialchars($ai_insights['ciclo_capitale'] ?? ''); ?>
                    </div>
                </div>

                <!-- Liquidity Ratios -->
                <div class="mb-12 sm:mb-20">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Indici di Liquidità - Andamento Storico</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Current Ratio -->
                        <div class="widget-card widget-purple p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Current Ratio</span>
                                <div class="tooltip-container">
                                    <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                    <div class="tooltip-content">Attivo Circolante / Debiti a Breve. Misura la capacità di coprire i debiti a breve con attività correnti. Ottimale >1.5x.</div>
                                </div>
                            </div>
                            <div class="relative h-[180px] sm:h-[240px] mb-3"><canvas id="currentRatioChart"></canvas></div>
                            <div class="text-left">
                                <?php
                                    $cr_last = $kpi['liquidita']['current_ratio'][$last_idx] ?? 0;
                                    $cr_prev = $kpi['liquidita']['current_ratio'][$prev_idx] ?? 0;
                                    $cr_var = $cr_prev > 0 ? (($cr_last - $cr_prev) / $cr_prev) * 100 : 0;
                                ?>
                                <div class="widget-metric-large"><?php echo number_format($cr_last, 2, '.', ''); ?>x</div>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="widget-change-positive"><?php echo ($cr_var >= 0 ? '+' : ''); ?><?php echo number_format($cr_var, 0, '.', ''); ?>% vs <?php echo $prev_year; ?></span>
                                    <span class="widget-status-badge bg-positive/10 text-positive"><i class="fa-solid fa-check"></i> Accettabile</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cash Ratio -->
                        <div class="widget-card widget-negative p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Cash Ratio</span>
                                <div class="tooltip-container">
                                    <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                    <div class="tooltip-content">Disponibilità Liquide / Debiti a Breve. Misura la copertura immediata con cassa. Ottimale >0.2x.</div>
                                </div>
                            </div>
                            <div class="relative h-[180px] sm:h-[240px] mb-3"><canvas id="cashRatioChart"></canvas></div>
                            <div class="text-left">
                                <?php
                                    $cash_r_last = $kpi['liquidita']['cash_ratio'][$last_idx] ?? 0;
                                    $cash_r_prev = $kpi['liquidita']['cash_ratio'][$prev_idx] ?? 0;
                                    $cash_r_var = $cash_r_prev > 0 ? (($cash_r_last - $cash_r_prev) / $cash_r_prev) * 100 : 0;
                                ?>
                                <div class="widget-metric-large text-negative"><?php echo number_format($cash_r_last, 2, '.', ''); ?>x</div>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="widget-change-positive"><?php echo ($cash_r_var >= 0 ? '+' : ''); ?><?php echo number_format($cash_r_var, 0, '.', ''); ?>% vs <?php echo $prev_year; ?></span>
                                    <span class="widget-status-badge bg-negative/10 text-negative"><i class="fa-solid fa-triangle-exclamation"></i> Critico</span>
                                </div>
                            </div>
                        </div>

                        <!-- Treasury Margin -->
                        <div class="widget-card widget-purple p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Margine di Tesoreria</span>
                                <div class="tooltip-container">
                                    <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                    <div class="tooltip-content">(Liquidità + Crediti) - Debiti a Breve. Misura stringente della capacità di pagamento. Positivo = solvibile.</div>
                                </div>
                            </div>
                            <div class="relative h-[180px] sm:h-[240px] mb-3"><canvas id="treasuryMarginChart"></canvas></div>
                            <div class="text-left">
                                <?php
                                    $tm = $kpi['liquidita']['margine_tesoreria'] ?? [];
                                    $tm_last = $tm[$last_idx] ?? 0;
                                    $tm_prev = $tm[$prev_idx] ?? 0;
                                    $tm_status = $tm_last >= 0 ? 'Positivo' : 'Negativo';
                                    $tm_color = $tm_last >= 0 ? 'text-positive' : 'text-negative';
                                    $tm_badge_color = $tm_last >= 0 ? 'bg-positive/10 text-positive' : 'bg-negative/10 text-negative';
                                    $tm_icon = $tm_last >= 0 ? 'fa-check' : 'fa-triangle-exclamation';
                                ?>
                                <div class="widget-metric-large <?php echo $tm_color; ?>"><?php echo ($tm_last >= 0 ? '' : '-'); ?>€<?php echo number_format(abs($tm_last) / 1000, 0, '.', ''); ?>k</div>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="widget-change-positive">da <?php echo ($tm_prev >= 0 ? '+' : ''); ?>€<?php echo number_format(abs($tm_prev) / 1000, 0, '.', ''); ?>k</span>
                                    <span class="widget-status-badge <?php echo $tm_badge_color; ?>"><i class="fa-solid <?php echo $tm_icon; ?>"></i> <?php echo $tm_status; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-droplet text-purple"></i> Liquidità:</strong> <?php echo htmlspecialchars($ai_insights['liquidita_ratios'] ?? ''); ?>
                    </div>
                </div>

                <!-- Cash Flow Waterfall -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Flusso di Cassa <?php echo $last_year; ?></div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Scomposizione del flusso di cassa: da utile netto a variazione liquidità attraverso gestione operativa, investimenti e finanziamenti.</div>
                        </div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="relative h-[280px] sm:h-[350px]"><canvas id="cashFlowWaterfallChart"></canvas></div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-left">
                                <?php
                                    $cf_data = $data['cash_flow'] ?? [];
                                    $items = [
                                        ['label' => 'Autofinanziamento', 'key' => 'autofinanziamento', 'color' => 'text-positive'],
                                        ['label' => 'Δ Capitale Circolante', 'key' => 'delta_capitale_circolante', 'color' => 'text-positive'],
                                        ['label' => 'Investimenti', 'key' => 'investimenti', 'color' => 'text-negative'],
                                        ['label' => 'Δ Debiti Finanziari', 'key' => 'delta_debiti_finanziari', 'color' => 'text-negative'],
                                    ];
                                    foreach ($items as $item):
                                        $val = $cf_data[$item['key']][$last_idx] ?? 0;
                                        $formatted = number_format(abs($val) / 1000, 1, '.', '');
                                        $prefix = ($val >= 0 ? '+' : '-');
                                ?>
                                <div>
                                    <div class="widget-label"><?php echo $item['label']; ?></div>
                                    <div class="widget-metric-medium <?php echo $item['color']; ?>"><?php echo ($val < 0 ? '-' : '+'); ?>€<?php echo $formatted; ?>k</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-arrow-trend-up text-purple"></i> Generazione Cassa:</strong> <?php echo htmlspecialchars($ai_insights['cash_flow_waterfall'] ?? ''); ?>
                    </div>
                </div>

                <!-- Cash Flow Trend -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Andamento Flusso di Cassa</div>
                            <div class="tooltip-container">
                                <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                <div class="tooltip-content">Cash Flow Operativo = Utile + Ammortamenti + Δ TFR. Mostra la capacità di generare liquidità dalla gestione corrente.</div>
                            </div>
                        </div>
                        <div id="cashFlowTrendChart-legend" class="flex gap-4"></div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="relative h-[220px] sm:h-[280px]"><canvas id="cashFlowTrendChart"></canvas></div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-money-bill-trend-up text-purple"></i> Trend Cash Flow:</strong> <?php echo htmlspecialchars($ai_insights['cash_flow_trend'] ?? ''); ?>
                    </div>
                </div>

                <!-- Debt Structure -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Struttura Debiti - Breve vs Lungo Termine</div>
                            <div class="tooltip-container">
                                <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                <div class="tooltip-content">Ripartizione dei debiti per scadenza. Troppi debiti a breve termine creano pressione sulla liquidità.</div>
                            </div>
                        </div>
                        <div id="debtStructureChart-legend" class="flex gap-4"></div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-4 sm:p-6 lg:col-span-2">
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="debtStructureChart"></canvas></div>
                        </div>
                        <div class="widget-card widget-purple p-4 sm:p-6 lg:col-span-3">
                            <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-6">Dettaglio per Anno</div>
                            <div class="space-y-8">
                                <?php
                                    $breve = $debt['breve_termine'] ?? [];
                                    $lungo = $debt['lungo_termine'] ?? [];
                                    $breve_pct = $debt['breve_pct'] ?? [];
                                    $lungo_pct = $debt['lungo_pct'] ?? [];

                                    foreach ($fiscal_years as $idx => $year):
                                        $b_val = $breve[$idx] ?? 0;
                                        $l_val = $lungo[$idx] ?? 0;
                                        $total = $b_val + $l_val;
                                        $b_pct = $breve_pct[$idx] ?? 0;
                                        $l_pct = $lungo_pct[$idx] ?? 0;
                                        $icon = '';
                                        // Show check for last year if breve_pct improved
                                        if ($idx === $last_idx && $num_years > 1) {
                                            $prev_b_pct = $breve_pct[$prev_idx] ?? 0;
                                            $icon = ($b_pct < $prev_b_pct) ? '<i class="fa-solid fa-check mr-1 text-[8px]"></i>' : '<i class="fa-solid fa-triangle-exclamation mr-1 text-[8px]"></i>';
                                            $prev_total = ($breve[$prev_idx] ?? 0) + ($lungo[$prev_idx] ?? 0);
                                            $var_pct = $prev_total > 0 ? round((($total - $prev_total) / $prev_total) * 100) : 0;
                                            $variazione = ' (' . ($var_pct >= 0 ? '+' : '') . $var_pct . '%)';
                                        } else {
                                            $variazione = '';
                                        }
                                ?>
                                <div>
                                    <div class="flex justify-between mb-1.5">
                                        <span class="text-[10px] font-medium text-gray-700"><?php echo $year; ?></span>
                                        <span class="text-[10px] text-gray-500">€<?php echo round($total/1000); ?>k totali<?php echo $variazione; ?></span>
                                    </div>
                                    <div class="flex h-6 overflow-hidden rounded">
                                        <div class="bg-purple flex items-center justify-center text-white text-[10px] font-medium" style="width: <?php echo $b_pct; ?>%"><?php echo $icon; ?><?php echo $b_pct; ?>% Breve</div>
                                        <div class="bg-zinc-500 flex items-center justify-center text-white text-[10px] font-medium" style="width: <?php echo $l_pct; ?>%"><?php echo $l_pct; ?>% Lungo</div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-scale-balanced text-purple"></i> Struttura Debiti:</strong> <?php echo htmlspecialchars($ai_insights['struttura_debiti'] ?? ''); ?>
                    </div>
                </div>

                <!-- Interest Coverage -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Copertura Oneri Finanziari (ICR)</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">EBIT / Oneri Finanziari. Misura quante volte l'utile operativo copre gli interessi. Ottimale >3x.</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-4 sm:p-6 lg:col-span-2">
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="icrChart"></canvas></div>
                        </div>
                        <div class="widget-card widget-purple p-4 sm:p-6 lg:col-span-3">
                            <div class="widget-detail-header">Analisi Sostenibilità Debito</div>
                            <div>
                                <?php
                                    $ebit_last = $interest_coverage['ebit'][$last_idx] ?? 0;
                                    $oneri_last = $interest_coverage['oneri_finanziari'][$last_idx] ?? 0;
                                    $oneri_prev = $interest_coverage['oneri_finanziari'][$prev_idx] ?? 0;
                                    $icr_last = $interest_coverage['icr'][$last_idx] ?? 0;
                                    $icr_prev = $interest_coverage['icr'][$prev_idx] ?? 0;
                                    $costo_debito = $interest_coverage['costo_medio_debito'] ?? 0;
                                    $dscr = $kpi['redditivita']['dscr'][$last_idx] ?? 0;

                                    $oneri_var = $oneri_prev > 0 ? (($oneri_last - $oneri_prev) / $oneri_prev) * 100 : 0;
                                    $icr_var = $icr_prev > 0 ? (($icr_last - $icr_prev) / $icr_prev) * 100 : 0;
                                ?>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">EBIT <?php echo $last_year; ?></span>
                                    <span class="widget-detail-value">€<?php echo number_format($ebit_last / 1000, 1, '.', ''); ?>k</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Oneri Finanziari <?php echo $last_year; ?></span>
                                    <span class="widget-detail-value">€<?php echo number_format($oneri_last / 1000, 1, '.', ''); ?>k (<?php echo number_format($oneri_var, 0, '.', ''); ?>% vs <?php echo $prev_year; ?>)</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Interest Coverage Ratio</span>
                                    <span class="widget-detail-value"><?php echo number_format($icr_last, 1, '.', ''); ?>x (<?php echo number_format($icr_var, 0, '.', ''); ?>% vs <?php echo $prev_year; ?>)</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Costo Medio Debito</span>
                                    <span class="widget-detail-value"><?php echo number_format($costo_debito, 2, '.', ''); ?>%</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">DSCR</span>
                                    <span class="widget-detail-value"><?php echo number_format($dscr, 2, '.', ''); ?>x</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-shield-halved text-purple"></i> Sostenibilità Debito:</strong> <?php echo htmlspecialchars($ai_insights['sostenibilita_debito'] ?? ''); ?>
                    </div>
                </div>


            </div>

            <!-- View: Reports (Bilanci) -->
            <div id="reports" class="view hidden">
                <div class="mb-6 sm:mb-10">
                    <h1 class="text-[18px] sm:text-[20px] font-medium text-primary tracking-tight">Bilanci e Conto Economico</h1>
                </div>

                <!-- Margine di Struttura -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Margine di Struttura</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Patrimonio Netto - Immobilizzazioni. Se positivo, le immobilizzazioni sono coperte da mezzi propri. Se negativo, sono finanziate anche con debiti.</div>
                        </div>
                    </div>
                    <?php
                        $sm = $data['structure_margin'] ?? [];
                        $pn = $sm['patrimonio_netto'] ?? [];
                        $immob = $sm['immobilizzazioni'] ?? [];
                        $margine_vals = $sm['margine'] ?? [];
                        $stati = $sm['stato'] ?? [];

                        // Dynamic grid classes based on number of years
                        $grid_cols = 'md:grid-cols-' . min($num_years, 4);
                        $container_class = $num_years > 4 ? 'flex overflow-x-auto gap-6 pb-4' : 'grid grid-cols-1 ' . $grid_cols . ' gap-6';
                        $item_class = $num_years > 4 ? 'min-w-[280px] flex-shrink-0' : '';
                    ?>
                    <div class="<?php echo $container_class; ?>">
                        <?php
                            foreach ($fiscal_years as $idx => $year):
                                $pn_val = $pn[$idx] ?? 0;
                                $immob_val = $immob[$idx] ?? 0;
                                $margine_val = $margine_vals[$idx] ?? 0;
                                $stato = $stati[$idx] ?? 'Neutrale';
                                $comparison = $pn_val >= $immob_val ? '>' : '<';
                                $badge_color = ($margine_val >= 0) ? 'bg-positive/10 text-positive' : 'bg-negative/10 text-negative';
                                $badge_icon = ($margine_val >= 0) ? 'fa-check' : 'fa-triangle-exclamation';
                                $variation = '';
                                if ($idx === $last_idx && $num_years > 1) {
                                    $prev_margine = $margine_vals[$prev_idx] ?? 0;
                                    if ($prev_margine != 0) {
                                        $pct_change = (($margine_val - $prev_margine) / abs($prev_margine)) * 100;
                                        $variation = '<div class="widget-change-positive mt-1">' . ($pct_change >= 0 ? '+' : '') . number_format($pct_change, 0, '.', '') . '% vs ' . $prev_year . '</div>';
                                    }
                                }
                        ?>
                        <div class="widget-card widget-purple p-6 text-left <?php echo $item_class; ?>">
                            <div class="widget-label mb-2"><?php echo $year; ?></div>
                            <div class="widget-metric-large"><?php echo ($margine_val >= 0 ? '+' : ''); ?>€<?php echo number_format(abs($margine_val) / 1000, 0, '.', ''); ?>k</div>
                            <div class="text-xs text-gray-500 mt-1">PN €<?php echo number_format($pn_val / 1000, 0, '.', ''); ?>k <?php echo $comparison; ?> Immob. €<?php echo number_format($immob_val / 1000, 0, '.', ''); ?>k</div>
                            <div class="widget-status-badge mt-2 <?php echo $badge_color; ?>"><i class="fa-solid <?php echo $badge_icon; ?>"></i> <?php echo $stato; ?></div>
                            <?php echo $variation; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-scale-balanced text-purple"></i> Analisi Strutturale:</strong> <?php echo htmlspecialchars($ai_insights['margine_struttura'] ?? ''); ?>
                    </div>
                </div>

                <!-- Conto Economico -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Conto Economico Comparativo</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Clicca sulle intestazioni delle colonne per ordinare la tabella. La variazione % è calcolata rispetto all'esercizio <?php echo $prev_year; ?>.</div>
                        </div>
                    </div>
                    <div class="widget-card p-6 overflow-x-auto">
                        <table class="w-full text-sm border-collapse sortable-table" id="incomeTable">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="string">Voce</th>
                                    <?php foreach ($fiscal_years as $year): ?>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="number"><?php echo $year; ?></th>
                                    <?php endforeach; ?>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="number">Var % vs <?php echo $prev_year; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Define table structure
                                    $table_rows = [
                                        ['label' => 'Ricavi', 'key' => 'ricavi', 'is_main' => true, 'is_negative' => false],
                                        ['label' => 'Costi per Servizi', 'key' => 'costi_servizi', 'is_main' => false, 'is_negative' => true],
                                        ['label' => 'Costi del Personale', 'key' => 'costi_personale', 'is_main' => false, 'is_negative' => true],
                                        ['label' => 'Altri Costi Operativi', 'key' => 'altri_costi_operativi', 'is_main' => false, 'is_negative' => true],
                                        ['label' => 'EBITDA', 'key' => 'ebitda', 'is_main' => true, 'is_negative' => false],
                                        ['label' => 'Ammortamenti', 'key' => 'ammortamenti', 'is_main' => false, 'is_negative' => true],
                                        ['label' => 'EBIT (Utile Operativo)', 'key' => 'ebit', 'is_main' => true, 'is_negative' => false],
                                        ['label' => 'Oneri Finanziari Netti', 'key' => 'oneri_finanziari', 'is_main' => false, 'is_negative' => true],
                                        ['label' => 'Imposte', 'key' => 'imposte', 'is_main' => false, 'is_negative' => true],
                                        ['label' => 'Utile Netto', 'key' => 'utile_netto', 'is_main' => true, 'is_negative' => false],
                                    ];

                                    foreach ($table_rows as $row):
                                        // Get values for all years
                                        $values = [];
                                        foreach ($fiscal_years as $idx => $year) {
                                            $values[$idx] = $income[$row['key']][$idx] ?? 0;
                                        }
                                        $val_last = $values[$last_idx] ?? 0;
                                        $val_prev = $values[$prev_idx] ?? 0;
                                        $variation = $val_prev != 0 ? (($val_last - $val_prev) / abs($val_prev)) * 100 : 0;

                                        $is_main = $row['is_main'] ? 'font-semibold' : '';
                                        $bg_class = $row['is_main'] ? 'bg-purple/5 border-b border-gray-200' : 'border-b border-gray-200 hover:bg-purple-50';
                                        $pl_class = $row['is_main'] ? '' : 'pl-6';

                                        if ($row['is_negative']) {
                                            $var_color = $variation > 0 ? 'text-negative' : 'text-positive';
                                        } else {
                                            $var_color = $variation > 0 ? 'text-positive' : 'text-negative';
                                        }

                                        $var_bold = $row['is_main'] ? 'font-bold' : '';
                                ?>
                                <tr class="<?php echo $is_main . ' ' . $bg_class; ?>">
                                    <td class="px-4 py-3 <?php echo $pl_class; ?>"><?php echo $row['label']; ?></td>
                                    <?php foreach ($fiscal_years as $idx => $year):
                                        $val = $values[$idx];
                                    ?>
                                    <td class="px-4 py-3 text-right" data-sort-value="<?php echo $val * ($row['is_negative'] ? -1 : 1); ?>"><?php echo $row['is_negative'] ? '(' : ''; ?>€<?php echo number_format($val, 0, '.', '.'); ?><?php echo $row['is_negative'] ? ')' : ''; ?></td>
                                    <?php endforeach; ?>
                                    <td class="px-4 py-3 text-right <?php echo $var_color . ' ' . $var_bold; ?>" data-sort-value="<?php echo round($variation, 1); ?>"><?php echo ($variation >= 0 ? '+' : '') . number_format($variation, 1, '.', ''); ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="widget-card widget-purple p-6 mt-6">
                        <div class="h-[220px] sm:h-[280px]">
                            <canvas id="patrimonioLineChart"></canvas>
                        </div>
                        <div id="patrimonioLineChart-legend" class="flex flex-wrap justify-center gap-6 mt-4"></div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-chart-column text-purple"></i> Performance Economica:</strong> <?php echo htmlspecialchars($ai_insights['performance_economica'] ?? ''); ?>
                    </div>
                </div>

                <!-- Stato Patrimoniale -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Stato Patrimoniale Sintetico</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Situazione patrimoniale al termine di ciascun esercizio. Variazioni calcolate vs <?php echo $prev_year; ?>.</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-4 sm:p-6">
                            <div class="widget-detail-header">Attivo</div>
                            <div>
                                <?php
                                    // Assets data structure
                                    $assets_items = [
                                        ['label' => 'Immobilizzazioni', 'key' => 'immobilizzazioni_totale'],
                                        ['label' => 'Crediti Commerciali', 'key' => 'crediti_commerciali'],
                                        ['label' => 'Attività Finanziarie', 'key' => 'attivita_finanziarie'],
                                        ['label' => 'Disponibilità Liquide', 'key' => 'disponibilita_liquide'],
                                    ];

                                    foreach ($assets_items as $item):
                                        $attivo_data = $balance['attivo'] ?? [];
                                        $val_prev = $attivo_data[$item['key']][$prev_idx] ?? 0;
                                        $val_last = $attivo_data[$item['key']][$last_idx] ?? 0;
                                        $variation = $val_prev > 0 ? (($val_last - $val_prev) / $val_prev) * 100 : 0;
                                        $var_color = $variation >= 0 ? 'text-positive' : 'text-negative';
                                        $var_text = ($variation >= 0 ? '+' : '') . number_format($variation, 0, '.', '') . '%';
                                ?>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label"><?php echo $item['label']; ?></span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€<?php echo number_format($val_last / 1000, 1, '.', ''); ?>k</span>
                                        <span class="ml-2 widget-text <?php echo $var_color; ?>"><?php echo $var_text; ?> vs <?php echo $prev_year; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="flex justify-between items-center font-bold">
                                    <span class="widget-label">Totale Attivo</span>
                                    <div class="text-right">
                                        <?php
                                            $attivo_data = $balance['attivo'] ?? [];
                                            $total_prev = $attivo_data['totale_attivo'][$prev_idx] ?? 0;
                                            $total_last = $attivo_data['totale_attivo'][$last_idx] ?? 0;
                                            $total_var = $total_prev > 0 ? (($total_last - $total_prev) / $total_prev) * 100 : 0;
                                            $total_var_color = $total_var >= 0 ? 'text-positive' : 'text-negative';
                                            $total_var_text = ($total_var >= 0 ? '+' : '') . number_format($total_var, 0, '.', '') . '%';
                                        ?>
                                        <span class="widget-detail-value">€<?php echo number_format($total_last / 1000, 1, '.', ''); ?>k</span>
                                        <span class="ml-2 widget-text <?php echo $total_var_color; ?>"><?php echo $total_var_text; ?> vs <?php echo $prev_year; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-detail-header">Passivo</div>
                            <div>
                                <?php
                                    // Liabilities data structure
                                    $liabilities_items = [
                                        ['label' => 'Patrimonio Netto', 'key' => 'totale_patrimonio_netto'],
                                        ['label' => 'TFR', 'key' => 'tfr'],
                                        ['label' => 'Debiti a Breve', 'key' => 'debiti_breve_termine'],
                                        ['label' => 'Debiti a Lungo', 'key' => 'debiti_lungo_termine'],
                                    ];

                                    foreach ($liabilities_items as $item):
                                        $passivo_data = $balance['passivo'] ?? [];
                                        $val_prev = $passivo_data[$item['key']][$prev_idx] ?? 0;
                                        $val_last = $passivo_data[$item['key']][$last_idx] ?? 0;
                                        $variation = $val_prev > 0 ? (($val_last - $val_prev) / $val_prev) * 100 : 0;
                                        $var_color = $variation >= 0 ? 'text-positive' : 'text-negative';
                                        $var_text = ($variation >= 0 ? '+' : '') . number_format($variation, 0, '.', '') . '%';
                                ?>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label"><?php echo $item['label']; ?></span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€<?php echo number_format($val_last / 1000, 1, '.', ''); ?>k</span>
                                        <span class="ml-2 widget-text <?php echo $var_color; ?>"><?php echo $var_text; ?> vs <?php echo $prev_year; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="flex justify-between items-center font-bold">
                                    <span class="widget-label">Totale Passivo</span>
                                    <div class="text-right">
                                        <?php
                                            $passivo_data = $balance['passivo'] ?? [];
                                            $total_prev = $passivo_data['totale_passivo'][$prev_idx] ?? 0;
                                            $total_last = $passivo_data['totale_passivo'][$last_idx] ?? 0;
                                            $total_var = $total_prev > 0 ? (($total_last - $total_prev) / $total_prev) * 100 : 0;
                                            $total_var_color = $total_var >= 0 ? 'text-positive' : 'text-negative';
                                            $total_var_text = ($total_var >= 0 ? '+' : '') . number_format($total_var, 0, '.', '') . '%';
                                        ?>
                                        <span class="widget-detail-value">€<?php echo number_format($total_last / 1000, 1, '.', ''); ?>k</span>
                                        <span class="ml-2 widget-text <?php echo $total_var_color; ?>"><?php echo $total_var_text; ?> vs <?php echo $prev_year; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-detail-header text-left">Attivo</div>
                            <div class="h-[200px]">
                                <canvas id="attivoDonutChart"></canvas>
                            </div>
                            <div id="attivoDonutChart-legend" class="flex flex-wrap justify-center gap-3 mt-4"></div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-detail-header text-left">Passivo</div>
                            <div class="h-[200px]">
                                <canvas id="passivoDonutChart"></canvas>
                            </div>
                            <div id="passivoDonutChart-legend" class="flex flex-wrap justify-center gap-3 mt-4"></div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-detail-header text-left">Costi di Produzione</div>
                            <div class="h-[200px]">
                                <canvas id="costiDonutChart"></canvas>
                            </div>
                            <div id="costiDonutChart-legend" class="flex flex-wrap justify-center gap-3 mt-4"></div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-building-columns text-purple"></i> Solidità Patrimoniale:</strong> <?php echo htmlspecialchars($ai_insights['solidita_patrimoniale'] ?? ''); ?>
                    </div>
                </div>
            </div>

            <!-- View: Analysis -->
            <div id="analysis" class="view hidden">
                <div class="mb-6 sm:mb-10">
                    <h1 class="text-[18px] sm:text-[20px] font-medium text-primary tracking-tight">Analisi Avanzata</h1>
                </div>

                <!-- Break-Even Analysis -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Analisi Punto di Pareggio</div>
                            <div class="tooltip-container">
                                <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                <div class="tooltip-content">Break-Even Point = Costi Fissi / Margine di Contribuzione %. Indica i ricavi minimi per coprire tutti i costi.</div>
                            </div>
                        </div>
                        <div id="breakEvenChart-legend" class="flex gap-4"></div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6">
                            <div class="relative h-[220px] sm:h-[280px] lg:col-span-3"><canvas id="breakEvenChart"></canvas></div>
                            <div class="lg:col-span-2">
                                <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-4">Struttura Costi e BEP</div>
                                <div class="space-y-3">
                                    <?php
                                        $be = $data['break_even'] ?? [];
                                        $costi_fissi = $be['costi_fissi'] ?? 0;
                                        $costi_variabili = $be['costi_variabili'] ?? 0;
                                        $margine_pct = ($be['margine_contribuzione'] ?? 0) * 100;
                                        $punto_pareggio = $be['punto_pareggio'] ?? 0;
                                        $margine_sicurezza_pct = $be['margine_sicurezza_pct'] ?? 0;
                                    ?>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Costi Fissi Stimati</span>
                                        <span class="text-sm font-bold text-gray-700">€<?php echo number_format($costi_fissi / 1000, 0, '.', ''); ?>k</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Costi Variabili</span>
                                        <span class="text-sm font-bold text-gray-700">€<?php echo number_format($costi_variabili / 1000, 0, '.', ''); ?>k</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Margine di Contribuzione %</span>
                                        <span class="text-sm font-bold text-gray-700"><?php echo number_format($margine_pct, 1, '.', ''); ?>%</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Punto di Pareggio</span>
                                        <span class="font-bold text-xl text-primary">€<?php echo number_format($punto_pareggio / 1000, 0, '.', ''); ?>k</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-600">Margine di Sicurezza</span>
                                        <span class="font-semibold text-positive"><?php echo $margine_sicurezza_pct; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-check text-purple"></i> Solidità:</strong> <?php echo htmlspecialchars($ai_insights['break_even'] ?? ''); ?>
                        </div>
                    </div>
                </div>

                <!-- Produttività Personale -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Produttività del Personale</div>
                            <div class="tooltip-container">
                                <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                <div class="tooltip-content">Metriche di efficienza per dipendente. Valori più alti indicano maggiore produttività.</div>
                            </div>
                        </div>
                        <div id="productivityChart-legend" class="flex gap-4"></div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-6 mb-4">
                            <div class="lg:col-span-3">
                                <div class="relative h-[220px] sm:h-[280px]"><canvas id="productivityChart"></canvas></div>
                            </div>
                            <div class="lg:col-span-2">
                                <div class="widget-detail-header">Metriche di Efficienza</div>
                                <div class="space-y-3">
                                    <?php
                                        $ricavi_per_dip_last = $productivity['ricavi_per_dipendente'][$last_idx] ?? 0;
                                        $ricavi_per_dip_prev = $productivity['ricavi_per_dipendente'][$prev_idx] ?? 0;
                                        $va_per_dip_last = $productivity['valore_aggiunto_per_dipendente_euro'][$last_idx] ?? 0;
                                        $va_per_dip_prev = $productivity['valore_aggiunto_per_dipendente_euro'][$prev_idx] ?? 0;
                                        $dip_last = $productivity['dipendenti'][$last_idx] ?? 1;
                                        $dip_prev = $productivity['dipendenti'][$prev_idx] ?? 1;
                                        $ebitda_per_dip_last = ($kpi['redditivita']['ebitda_margin'][$last_idx] ?? 0) * ($income['ricavi'][$last_idx] ?? 0) / $dip_last / 1000;
                                        $ebitda_per_dip_prev = ($kpi['redditivita']['ebitda_margin'][$prev_idx] ?? 0) * ($income['ricavi'][$prev_idx] ?? 0) / $dip_prev / 1000;

                                        $metrics = [
                                            ['label' => 'Ricavi / Costo Personale', 'val_last' => $ricavi_per_dip_last, 'val_prev' => $ricavi_per_dip_prev, 'unit' => 'x'],
                                            ['label' => 'Valore Aggiunto / Personale', 'val_last' => $va_per_dip_last, 'val_prev' => $va_per_dip_prev, 'unit' => '€'],
                                            ['label' => 'EBITDA / Personale', 'val_last' => $ebitda_per_dip_last, 'val_prev' => $ebitda_per_dip_prev, 'unit' => '€'],
                                        ];
                                        foreach ($metrics as $m):
                                            $unit = ($m['unit'] === 'x') ? 'x' : 'k';
                                            $formatted = ($m['unit'] === 'x') ? number_format($m['val_last'], 2, '.', '') : round($m['val_last']);
                                            $var = $m['val_prev'] > 0 ? (($m['val_last'] - $m['val_prev']) / $m['val_prev']) * 100 : 0;
                                    ?>
                                    <div class="widget-detail-row">
                                        <span class="widget-detail-label"><?php echo $m['label']; ?></span>
                                        <div class="text-right">
                                            <span class="widget-detail-value"><?php echo ($m['unit'] === '€' ? '€' : ''); ?><?php echo $formatted; ?><?php echo $unit; ?></span>
                                            <span class="ml-2 widget-text text-positive"><?php echo ($var >= 0 ? '+' : ''); ?><?php echo number_format($var, 0, '.', ''); ?>% vs <?php echo $prev_year; ?></span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php
                                        $costo_medio_dip_last = ($income['costi_personale'][$last_idx] ?? 0) / $dip_last / 1000;
                                        $costo_medio_dip_prev = ($income['costi_personale'][$prev_idx] ?? 0) / $dip_prev / 1000;
                                        $costo_var = $costo_medio_dip_prev > 0 ? (($costo_medio_dip_last - $costo_medio_dip_prev) / $costo_medio_dip_prev) * 100 : 0;
                                    ?>
                                    <div class="widget-detail-row">
                                        <span class="widget-detail-label">Costo Medio Dipendente</span>
                                        <div class="text-right">
                                            <span class="widget-detail-value">€<?php echo round($costo_medio_dip_last); ?>k</span>
                                            <span class="ml-2 widget-text text-gray-600"><?php echo ($costo_var >= 0 ? '+' : ''); ?><?php echo number_format($costo_var, 0, '.', ''); ?>% vs <?php echo $prev_year; ?></span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="widget-detail-label">N. Dipendenti (stimato)</span>
                                        <span class="widget-detail-value">~<?php echo $productivity['dipendenti_stimati'] ?? 12; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-chart-line text-purple"></i> Performance:</strong> <?php echo htmlspecialchars($ai_insights['produttivita'] ?? ''); ?>
                        </div>
                    </div>
                </div>

                <!-- CAPEX Analysis -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Analisi Investimenti (CAPEX)</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Capital Expenditure. Investimenti in immobilizzazioni = Δ Immobilizzazioni + Ammortamenti.</div>
                        </div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <div class="relative h-[220px] sm:h-[280px]"><canvas id="capexChart"></canvas></div>
                            <div>
                                <div class="widget-label-medium mb-4">Dettaglio Investimenti</div>
                                <div class="space-y-4">
                                    <?php foreach ($capex['dettagli'] as $capex_item):
                                        $periodo = $capex_item['periodo'] ?? '';
                                        $totale = $capex_item['totale'] ?? 0;
                                        $delta = $capex_item['delta_immobilizzazioni'] ?? 0;
                                        $ammort = $capex_item['ammortamenti'] ?? 0;
                                        $nota = $capex_item['nota'] ?? '';
                                        // Determine if negative or positive based on nota
                                        $class = (strpos($nota, 'pesante') !== false) ? 'widget-change-negative' : 'widget-change-positive';
                                        $totale_k = round($totale / 1000);
                                        $delta_k = round($delta / 1000);
                                        $ammort_k = round($ammort / 1000);
                                    ?>
                                    <div class="p-3 bg-gray-50 border border-gray-200">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="widget-label-medium"><?php echo htmlspecialchars($periodo); ?></span>
                                            <span class="widget-metric-small">€<?php echo $totale_k; ?>k</span>
                                        </div>
                                        <div class="widget-text text-gray-500">Δ Immob. €<?php echo $delta_k; ?>k + Ammort. €<?php echo $ammort_k; ?>k</div>
                                        <div class="<?php echo $class; ?> mt-1"><?php echo htmlspecialchars($nota); ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-check text-purple"></i> Strategia:</strong> <?php echo htmlspecialchars($ai_insights['capex'] ?? ''); ?>
                        </div>
                    </div>
                </div>

                <!-- DuPont Analysis -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Scomposizione DuPont del ROE</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">ROE = Margine Netto × Rotazione Attivo × Leva Finanziaria. Permette di capire da quale leva proviene il rendimento.</div>
                        </div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php
                                $dupont_data = [
                                    ['label' => 'Margine Netto', 'key' => 'margine_netto', 'unit' => '%', 'info' => 'Utile Netto / Ricavi. Quanto profitto rimane per ogni euro di vendita.', 'chart' => 'dupontMarginChart'],
                                    ['label' => 'Rotazione Attivo', 'key' => 'rotazione_attivo', 'unit' => 'x', 'info' => 'Ricavi / Totale Attivo. Efficienza nell\'utilizzo delle risorse.', 'chart' => 'dupontTurnoverChart'],
                                    ['label' => 'Leva Finanziaria', 'key' => 'leva_finanziaria', 'unit' => 'x', 'info' => 'Totale Attivo / Patrimonio Netto. Grado di indebitamento.', 'chart' => 'dupontLeverageChart'],
                                    ['label' => 'ROA', 'key' => 'roa', 'unit' => '%', 'info' => 'Return on Assets = Utile Netto / Totale Attivo. Redditività del capitale investito.', 'chart' => 'roaChart']
                                ];

                                foreach ($dupont_data as $item):
                                    $val_last = $dupont[$item['key']][$last_idx] ?? 0;
                                    $val_prev = $dupont[$item['key']][$prev_idx] ?? 0;
                                    $variazione = $val_prev > 0 ? (($val_last - $val_prev) / abs($val_prev)) * 100 : 0;
                                    $formatted = ($item['unit'] === '%') ? number_format($val_last, 1, '.', '') . '%' : number_format($val_last, 2, '.', '') . 'x';
                                    $var_text = $variazione > 0 ? '+' . number_format($variazione, 1, '.', '') : number_format($variazione, 1, '.', '');
                                    $var_unit = ($item['unit'] === '%') ? 'pp' : '%';
                            ?>
                            <div class="bg-gray-50 border border-gray-200 p-6">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <span class="text-[11px] text-gray-500 uppercase font-semibold"><?php echo $item['label']; ?></span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-[10px] cursor-help"></i>
                                        <div class="tooltip-content"><?php echo $item['info']; ?></div>
                                    </div>
                                </div>
                                <div class="relative h-[200px]"><canvas id="<?php echo $item['chart']; ?>"></canvas></div>
                                <div class="text-left mt-2">
                                    <div class="text-xl font-semibold text-primary"><?php echo $formatted; ?></div>
                                    <div class="text-xs text-positive"><?php echo $var_text . $var_unit; ?> vs <?php echo $prev_year; ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong>Insight:</strong> <?php echo htmlspecialchars($ai_insights['dupont'] ?? ''); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View: Risk & Scoring -->
            <div id="risk" class="view hidden">
                <div class="mb-10">
                    <h1 class="text-[18px] sm:text-[20px] font-medium text-primary tracking-tight">Analisi Rischio e Scoring</h1>
                </div>

                <!-- Action Items -->
                <div class="mb-12 sm:mb-20">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Azioni Prioritarie</div>
                    <div class="widget-card widget-purple p-6">
                        <div class="space-y-4">
                            <?php foreach ($risks as $risk):
                                $priority = $risk['priority'] ?? '';
                                $titolo = $risk['titolo'] ?? '';
                                $target = $risk['target'] ?? '';
                                $azioni = $risk['azioni'] ?? '';
                                $criticita = $risk['criticita'] ?? '';
                                $css_class = $risk['css_class'] ?? 'widget-positive';

                                // Determina il colore di fondo e border basato su criticita
                                if ($criticita === 'alta') {
                                    $bg_class = 'bg-red-50';
                                    $border_class = 'border-red-300';
                                    $badge_bg = 'bg-red-500';
                                } else {
                                    $bg_class = 'bg-purple/3';
                                    $border_class = 'border-purple/15';
                                    $badge_bg = 'bg-purple/60';
                                }
                            ?>
                            <div class="flex items-center gap-6 p-4 <?php echo $bg_class; ?> border <?php echo $border_class; ?>">
                                <div class="<?php echo $badge_bg; ?> text-white text-xs font-bold px-2 py-1"><?php echo htmlspecialchars($priority); ?></div>
                                <div>
                                    <div class="font-semibold text-primary"><?php echo htmlspecialchars($titolo); ?></div>
                                    <div class="text-xs text-gray-600"><?php echo htmlspecialchars($target); ?>. Azioni: <?php echo htmlspecialchars($azioni); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Risk Matrix -->
                <div class="mb-12 sm:mb-20">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Matrice dei Rischi</div>
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <?php foreach ($risk_matrix as $matrix):
                            $categoria = $matrix['categoria'] ?? '';
                            $livello = $matrix['livello'] ?? '';
                            $indicatori = $matrix['indicatori'] ?? [];
                        ?>
                        <div class="widget-card widget-purple p-6">
                            <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-200">
                                <span class="risk-badge text-[9px] font-bold px-1.5 py-0.5 uppercase"><?php echo htmlspecialchars($livello); ?></span>
                                <span class="text-xs font-semibold text-primary uppercase"><?php echo htmlspecialchars($categoria); ?></span>
                            </div>
                            <div class="text-[13px] leading-relaxed text-gray-700">
                                <ul class="space-y-1.5">
                                    <?php foreach ($indicatori as $indicatore): ?>
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple"><?php echo htmlspecialchars($indicatore); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Z-Score Altman -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Z-Score Altman (Rischio Insolvenza)</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Modello predittivo di insolvenza. Formula per PMI private: Z = 0.717X1 + 0.847X2 + 3.107X3 + 0.420X4 + 0.998X5.</div>
                        </div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <!-- Legend Section -->
                        <div class="mb-10 p-6 bg-gray-50  border border-gray-200">
                            <div class="widget-label mb-3">Zone di Rischio</div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 border border-gray-300">
                                    <span class="w-2.5 h-2.5 bg-gray-700"></span>
                                    <span class="widget-label text-gray-700">Zona Sicura (>2.9)</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 border border-gray-300">
                                    <span class="w-2.5 h-2.5 bg-gray-500"></span>
                                    <span class="widget-label text-gray-700">Zona Grigia (1.23-2.9)</span>
                                </div>
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 border border-gray-300">
                                    <span class="w-2.5 h-2.5 bg-gray-400"></span>
                                    <span class="widget-label text-gray-700">Zona Rischio (<1.23)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Main Z-Score Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-10">
                            <!-- Chart - larger -->
                            <div class="lg:col-span-2">
                                <div class="relative h-[250px] sm:h-[320px] bg-gray-50 p-4 sm:p-6"><canvas id="zscoreChart"></canvas></div>
                            </div>

                            <!-- Z-Score Value - compact -->
                            <div>
                                <?php
                                    $zscore_last = $z_score['punteggio'][$last_idx] ?? 0;
                                    $zscore_prev = $z_score['punteggio'][$prev_idx] ?? 0;
                                    $zscore_variazione = $zscore_prev > 0 ? round((($zscore_last - $zscore_prev) / $zscore_prev) * 100) : 0;
                                    $zone_last = $z_score['zone'][$last_idx] ?? 'grey';
                                    $zone_label = $zone_last === 'safe' ? 'ZONA SICURA' : ($zone_last === 'grey' ? 'ZONA GRIGIA' : 'ZONA RISCHIO');
                                    $components = $z_score['components_' . $last_year] ?? $z_score['components_2025'] ?? [];
                                ?>
                                <div class="border border-gray-200 p-5 mb-3">
                                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Z-Score <?php echo $last_year; ?></div>
                                    <div class="text-2xl font-semibold text-primary mb-1"><?php echo number_format($zscore_last, 2, '.', ''); ?></div>
                                    <div class="text-xs text-positive font-semibold mb-3"><?php echo ($zscore_variazione >= 0 ? '+' : ''); ?><?php echo $zscore_variazione; ?>% vs <?php echo $prev_year; ?></div>
                                    <div class="inline-block px-2 py-1 bg-positive/10 text-positive font-semibold text-[10px] border border-positive/30"><?php echo $zone_label; ?></div>
                                </div>

                                <!-- Components compact -->
                                <div class="text-[10px] font-semibold text-gray-700 uppercase tracking-wide mb-2">Componenti</div>
                                <div class="space-y-0">
                                    <?php
                                        $component_labels = ['X1', 'X2', 'X3', 'X4', 'X5'];
                                        $component_keys = ['x1', 'x2', 'x3', 'x4', 'x5'];
                                        foreach ($component_keys as $idx => $key):
                                            $value = $components[$key] ?? 0;
                                            $is_last = ($idx === count($component_keys) - 1);
                                    ?>
                                    <div class="flex justify-between items-center py-1.5 <?php echo !$is_last ? 'border-b border-gray-100' : ''; ?>">
                                        <span class="text-[10px] text-gray-600"><?php echo $component_labels[$idx]; ?></span>
                                        <span class="font-semibold text-[10px]"><?php echo number_format($value, 2, '.', ''); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-shield-check text-purple"></i> Solidità Finanziaria:</strong> <?php echo htmlspecialchars($ai_insights['z_score'] ?? ''); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View: Documents -->
            <div id="documents" class="view hidden">
                <div class="mb-10">
                    <h1 class="text-[18px] sm:text-[20px] font-medium text-primary tracking-tight">Archivio Documenti</h1>
                </div>

                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Bilanci Utilizzati per l'Analisi</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Documenti finanziari ufficiali depositati. Clicca sulle intestazioni per ordinare la tabella.</div>
                        </div>
                    </div>
                    <div class="widget-card p-6 overflow-x-auto">
                        <table class="w-full text-sm border-collapse sortable-table" id="documentsTable">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="string">Nome File</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="string">Tipo</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="string">Esercizio</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="string">Data Chiusura</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 text-[11px] uppercase tracking-wide">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc):
                                    $nome = $doc['nome_file'] ?? '';
                                    $tipo = $doc['tipo'] ?? '';
                                    $esercizio = $doc['esercizio'] ?? '';
                                    $data = $doc['data_chiusura'] ?? '';
                                    $url = $doc['url'] ?? '';
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-file-pdf text-purple"></i>
                                            <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="font-medium text-purple hover:underline"><?php echo htmlspecialchars($nome); ?></a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($tipo); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($esercizio); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($data); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="<?php echo htmlspecialchars($url); ?>" download class="action-btn inline-flex items-center justify-center"><i class="fa-solid fa-download"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-12 sm:mb-20">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-50 border border-gray-300  p-6">
                        <div class="flex items-center gap-2.5 mb-3">
                            <i class="fa-solid fa-circle-info text-gray-500 text-lg"></i>
                            <span class="text-xs font-medium text-primary">Informazioni sull'Archivio</span>
                        </div>
                        <div class="text-sm leading-relaxed text-gray-700">
                            <p class="mb-2">I bilanci sono estratti dal sistema informativo aziendale e rappresentano le dichiarazioni finanziarie ufficiali depositate.</p>
                            <p><strong>Periodo di Copertura:</strong> Esercizi <?php echo implode(', ', $fiscal_years); ?></p>
                            <p><strong>Formato:</strong> PDF conformi alla tassonomia XBRL itcc-ci-2018-11-04</p>
                            <?php if (!empty($metadata['notes'])): ?>
                            <p><strong>Note:</strong> <?php echo htmlspecialchars($metadata['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
