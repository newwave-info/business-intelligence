<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Intelligence - Fabbrica del Valore SRL</title>
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
            <h2 class="text-[15px] font-medium text-primary">Fabbrica del Valore srl</h2>
            <p class="text-[11px] text-gray-500">Business Intelligence Suite</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-[11px] text-gray-500 hidden sm:block">
                Ultimo aggiornamento: <strong class="text-gray-700">19 nov 2024</strong>
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
            <button class="w-full flex items-center text-[13px] font-semibold text-gray-400 px-2 py-2 pb-4 cursor-not-allowed transition-colors duration-200 border-b border-gray-200" style="outline: none; -webkit-tap-highlight-color: transparent; border-radius: 6px;" disabled>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-building text-[12px]"></i>
                    <span class="text-left">Company Overview</span>
                </div>
            </button>
            <button class="w-full flex items-center text-[13px] font-semibold text-purple-600 px-2 py-2 pt-4 group transition-colors duration-200" style="outline: none; -webkit-tap-highlight-color: transparent; border-radius: 6px;" data-accordion-toggle="cgMenu" onclick="toggleAccordion('cgMenu')">
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
                <button class="w-full flex items-center text-[13px] font-semibold text-gray-600 px-2 py-2 group transition-colors duration-200" style="outline: none; -webkit-tap-highlight-color: transparent; border-radius: 6px;" data-accordion-toggle="availableMenu" onclick="toggleAccordion('availableMenu')">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-[12px] text-gray-400 transition-colors duration-200"></i>
                        <span class="transition-colors duration-200 text-left">Analisi Disponibili</span>
                    </div>
                    <i class="accordion-icon fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200 ml-auto"></i>
                </button>
                <div id="availableMenu" class="accordion-content hidden mt-2">
                <div class="ml-3 border-l border-gray-200 space-y-1">
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
                        <span>Brand</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
                        <span>Digital</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
                        <span>Social</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
                        <span>Web</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
                        <span>ESG</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
                        <span>EAA</span>
                    </div>
                    <div class="flex items-center gap-2 pl-4 py-1 text-[12px] cursor-not-allowed" style="color: #cbd5e1; opacity: 0.6;">
                        <i class="fa-solid fa-circle text-[6px]" style="color: #cbd5e1;"></i>
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
                                        <div class="tooltip-content">Confronto tra performance effettiva 2024 e target ottimali. Valori normalizzati 0-100.</div>
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
                                <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Executive Summary 2024</div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-auto h-full">
                                <div class="p-4 bg-gray-50 border border-gray-200 flex flex-col justify-between">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1">Redditività</div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-check text-purple"></i>
                                            Solida
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2">ROE 33.7%</div>
                                </div>
                                <div class="p-4 bg-gray-50 border border-gray-200 flex flex-col justify-between">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1">Crescita</div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-check text-purple"></i>
                                            Moderata
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2">+12.6% Ricavi</div>
                                </div>
                                <div class="p-4 bg-gray-50 border border-gray-200 flex flex-col justify-between">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1">Solidità</div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-check text-purple"></i>
                                            Robusta
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2">D/E 1.13x</div>
                                </div>
                                <div class="p-4 bg-gray-50 border border-gray-200 flex flex-col justify-between">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1">Liquidità</div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-check text-purple"></i>
                                            Adeguata
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2">Cash 0.61x</div>
                                </div>
                                <div class="p-4 border flex flex-col justify-between" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(255, 255, 255, 0) 100%); border-color: rgba(239, 68, 68, 0.3);">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1">Efficienza</div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                            Da presidiare
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2">DSO 150gg</div>
                                </div>
                                <div class="p-4 bg-gray-50 border border-gray-200 flex flex-col justify-between">
                                    <div>
                                        <div class="text-[10px] text-gray-500 uppercase mb-1">Rischio</div>
                                        <div class="flex items-center gap-1 text-sm font-semibold text-gray-800">
                                            <i class="fa-solid fa-check text-purple"></i>
                                            Basso
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-2">Z-Score 3.23</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Insight -->
                <div class="mb-12 sm:mb-20">
                    <div class="widget-ai-insight px-6 py-4 transition-all duration-300">
                        <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-blue-200/40">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            <span class="text-xs font-semibold text-primary uppercase tracking-wide">Riepilogo Esecutivo 2024</span>
                        </div>
                        <div class="text-[13px] leading-relaxed text-gray-700">
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-danger font-bold">→</span> DSO risale a 150gg (+5gg): ~€91k di capitale bloccato nei crediti, focus su credit management.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-danger font-bold">→</span> Costi operativi all’84.5% dei ricavi: margini sensibili all’incremento dei servizi esterni, serve efficienza.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-danger font-bold">→</span> Debiti a breve €126k (71% del totale): opportuno pianificare rifinanziamenti a medio termine per ridurre il rischio di rifinanziamento.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-purple font-bold">→</span> Ricavi €536k (+12.6% YoY) dopo il balzo del 57% del 2023: CAGR triennale 33%.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-purple font-bold">→</span> EBITDA €106k (19.8%): +31% vs 2023 grazie al contenimento dei costi fissi.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-purple font-bold">→</span> Utile netto €52k (+29.8%): margine al 9.8% con EBIT a €83k.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-purple font-bold">→</span> Liquidità molto ampia: Current ratio 2.30x e Cash ratio 0.61x.</div>
                            <div class="pl-4 relative py-2 border-b border-dashed border-gray-300"><span class="absolute left-0 text-purple font-bold">→</span> Struttura patrimoniale snella: D/E 1.13x (da 1.75x) e leverage 2.30x.</div>
                            <div class="pl-4 relative py-2"><span class="absolute left-0 text-purple font-bold">→</span> Copertura debito elevata: ICR 16.6x e DSCR 2.8x, nessuna pressione sul servizio.</div>
                        </div>
                    </div>
                </div>


                <!-- Strategic Insights -->
                <div class="mb-12 sm:mb-20">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Insight Strategici</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">EBITDA Margin 19.8%</div>
                            <div class="widget-text">€106k di EBITDA (+31% YoY). Il margine operativo cresce di 2.9pp rispetto al 2023.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Ricavi €536k</div>
                            <div class="widget-text">+€60k YoY e +€234k vs 2022. Il mix consulenziale mantiene ticket medio elevato.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Leverage 2.30x</div>
                            <div class="widget-text">D/E 1.13x e patrimonio netto €156k: la leva finanziaria scende grazie agli utili reinvestiti.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">Ciclo Finanziario 27gg</div>
                            <div class="widget-text">DSO 150gg e DPO 123gg. Capitale circolante netto €169k interamente coperto da mezzi propri.</div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="widget-title">DSCR 2.8x</div>
                            <div class="widget-text">CFO €70k e quota capitale rimborsata ~€20k: ampia capacità di servizio del debito.</div>
                        </div>
                        <div class="widget-card widget-negative p-6">
                            <div class="widget-title"><i class="fa-solid fa-triangle-exclamation text-negative"></i> DSO 150gg</div>
                            <div class="widget-text">+5gg YoY, oltre il target 120gg. Serve credit management per liberare ~€90k di cassa.</div>
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
                                <strong><i class="fa-solid fa-scale-balanced text-purple"></i> Leva Finanziaria in equilibrio:</strong> il D/E è passato da 1.59x (2022) a 1.13x, con leverage in calo a 2.30x. Nonostante la leva più bassa il ROE resta >33%, segno che la redditività copre ampiamente il costo del capitale.
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
                                <strong><i class="fa-solid fa-chart-line text-purple"></i> Efficienza Operativa:</strong> L’incidenza dei costi oscilla tra l’82% e l’86% dei ricavi, con margini stabili. Il DSO è sceso dai 229gg del 2022 ai 150gg attuali ma resta sopra il target 120gg: servono azioni su incassi e pricing.
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
                                <strong><i class="fa-solid fa-chart-line text-purple"></i> Crescita Selettiva:</strong> Ricavi +12.6% (€536k) con EBITDA €106k (+31% YoY). Il margine EBITDA sale al 19.8% (+2.9pp), grazie a progetti consulenziali a maggior valore e controllo dei costi fissi.
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
                                <strong><i class="fa-solid fa-coins text-purple"></i> Profittabilità in consolidamento:</strong> EBIT €83k (+20% YoY) e utile netto €52k (+29.8%). Il margine netto sale al 9.8% e conferma la tenuta dei prezzi e della base costi.
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
                        <div class="widget-card widget-negative p-6 text-left">
                            <div class="widget-label mb-2">Giorni Incasso (DSO)</div>
                            <div class="widget-metric-large text-negative">+3%</div>
                            <div class="text-sm text-gray-500">150 giorni</div>
                            <div class="text-xs text-gray-400 mt-1">Target: 120 giorni</div>
                            <div class="widget-status-badge mt-2 bg-red-100 text-red-700 text-[9px]"><i class="fa-solid fa-triangle-exclamation"></i> Sopra target</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">Giorni Pagamento (DPO)</div>
                            <div class="widget-metric-large text-gray-700">+4%</div>
                            <div class="text-sm text-gray-500">123 giorni</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">Ciclo Finanziario</div>
                            <div class="widget-metric-large text-gray-700">+1%</div>
                            <div class="text-sm text-gray-500">27 giorni</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">Cassa Liberabile</div>
                            <div class="widget-metric-large">€45k</div>
                            <div class="text-xs text-gray-500 mt-1">Con DSO a 120gg</div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-clock text-purple"></i> Efficienza Operativa:</strong> Il ciclo finanziario netto è di 27 giorni (WC €169k). Il DSO resta lungo a 150gg, 27gg sopra il DPO: priorità ad accelerare incassi per liberare ~€45k di cassa.
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
                                <div class="widget-metric-large">2.30x</div>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="widget-text text-gray-600 text-[11px]">-0.10x vs 2023</span>
                                    <span class="widget-status-badge bg-positive/10 text-positive"><i class="fa-solid fa-check"></i> Solido</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cash Ratio -->
                        <div class="widget-card widget-purple p-4 sm:p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Cash Ratio</span>
                                <div class="tooltip-container">
                                    <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                                    <div class="tooltip-content">Disponibilità Liquide / Debiti a Breve. Misura la copertura immediata con cassa. Ottimale >0.2x.</div>
                                </div>
                            </div>
                            <div class="relative h-[180px] sm:h-[240px] mb-3"><canvas id="cashRatioChart"></canvas></div>
                            <div class="text-left">
                                <div class="widget-metric-large">0.61x</div>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="widget-text text-gray-600 text-[11px]">-0.13x vs 2023</span>
                                    <span class="widget-status-badge bg-positive/10 text-positive"><i class="fa-solid fa-check"></i> Sopra target</span>
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
                                <div class="widget-metric-large text-positive">€169k</div>
                                <div class="flex items-center justify-center gap-2 mt-1">
                                    <span class="widget-change-positive">+€9k vs 2023</span>
                                    <span class="widget-status-badge bg-positive/10 text-positive"><i class="fa-solid fa-check"></i> Ampio buffer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-droplet text-purple"></i> Liquidità:</strong> Current ratio 2.30x e cash ratio 0.61x: la gestione corrente copre ampiamente i €130k di passività a breve. Il buffer di cassa/crediti è pari a €169k.
                    </div>
                </div>

                <!-- Cash Flow Waterfall -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Flusso di Cassa 2024</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Scomposizione del flusso di cassa: da utile netto a variazione liquidità attraverso gestione operativa, investimenti e finanziamenti.</div>
                        </div>
                    </div>
                    <div class="widget-card widget-purple p-6">
                        <div class="relative h-[280px] sm:h-[350px]"><canvas id="cashFlowWaterfallChart"></canvas></div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-left">
                                <div>
                                    <div class="widget-label">Autofinanziamento</div>
                                    <div class="widget-metric-medium text-positive">€76.1k</div>
                                </div>
                                <div>
                                    <div class="widget-label">Δ Capitale Circolante</div>
                                    <div class="widget-metric-medium text-negative">-€35.4k</div>
                                </div>
                                <div>
                                    <div class="widget-label">Investimenti</div>
                                    <div class="widget-metric-medium text-negative">-€46.3k</div>
                                </div>
                                <div>
                                    <div class="widget-label">Δ Debiti Finanziari</div>
                                    <div class="widget-metric-medium text-negative">-€4.5k</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-arrow-trend-up text-purple"></i> Generazione Cassa:</strong> Il CFO 2024 vale €70k (EBITDA 106k - tasse - ΔWC). Capex per €46k e rimborso debiti di ~€20k assorbono gran parte del flusso, spiegando la lieve riduzione di cassa (-€5.7k).
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
                        <strong><i class="fa-solid fa-money-bill-trend-up text-purple"></i> Trend Cash Flow:</strong> Il CFO passa da €46k (2022) a €70k (2024), con liquidità che oscilla tra €43k e €79k. La gestione operativa finanzia capex e rimborsi senza ricorrere a nuova finanza.
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
                                <div>
                                    <div class="flex justify-between mb-1.5">
                                        <span class="text-[10px] font-medium text-gray-700">2022</span>
                                        <span class="text-[10px] text-gray-500">€156k totali</span>
                                    </div>
                                    <div class="flex h-6 overflow-hidden rounded">
                                        <div class="bg-purple flex items-center justify-center text-white text-[10px] font-medium" style="width: 42%">42% Breve</div>
                                        <div class="bg-zinc-500 flex items-center justify-center text-white text-[10px] font-medium" style="width: 58%">58% Lungo</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-1.5">
                                        <span class="text-[10px] font-medium text-gray-700">2023</span>
                                        <span class="text-[10px] text-gray-500">€181k totali</span>
                                    </div>
                                    <div class="flex h-6 overflow-hidden rounded">
                                        <div class="bg-purple flex items-center justify-center text-white text-[10px] font-medium" style="width: 61%">61% Breve</div>
                                        <div class="bg-zinc-500 flex items-center justify-center text-white text-[10px] font-medium" style="width: 39%">39% Lungo</div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-1.5">
                                        <span class="text-[10px] font-medium text-gray-700">2024</span>
                                        <span class="text-[10px] text-gray-500">€176k totali (-3%)</span>
                                    </div>
                                    <div class="flex h-6 overflow-hidden rounded">
                                        <div class="bg-purple flex items-center justify-center text-white text-[10px] font-medium" style="width: 71%"><i class="fa-solid fa-triangle-exclamation mr-1 text-[8px]"></i> 71% Breve</div>
                                        <div class="bg-zinc-500 flex items-center justify-center text-white text-[10px] font-medium" style="width: 29%">29% Lungo</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-scale-balanced text-purple"></i> Struttura Debiti:</strong> Debito complessivo €176k, di cui €126k (71%) a breve. Il calo del debito a medio/lungo (-€20k YoY) riduce gli oneri, ma resta utile allungare ulteriormente le scadenze.
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
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">EBIT 2024</span>
                                    <span class="widget-detail-value">€83.1k</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Oneri Finanziari 2024</span>
                                    <span class="widget-detail-value">€5.0k (-18% vs 2023)</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Interest Coverage Ratio</span>
                                    <span class="widget-detail-value">16.6x</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Costo Medio Debito</span>
                                    <span class="widget-detail-value">2.8%</span>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">DSCR</span>
                                    <span class="widget-detail-value">2.8x</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                        <strong><i class="fa-solid fa-shield-halved text-purple"></i> Sostenibilità Debito:</strong> ICR 16.6x e DSCR 2.8x indicano copertura ampia di interessi e quota capitale. Il costo medio resta contenuto grazie al ridimensionamento del debito M/L.
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">2022</div>
                            <div class="widget-metric-large text-positive">+€58k</div>
                            <div class="text-xs text-gray-500 mt-1">PN €97.9k > Immob. €39.6k</div>
                            <div class="widget-status-badge mt-2 bg-positive/10 text-positive"><i class="fa-solid fa-check"></i> Coperto</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">2023</div>
                            <div class="widget-metric-large text-positive">+€73k</div>
                            <div class="text-xs text-gray-500 mt-1">PN €103k > Immob. €30.5k</div>
                            <div class="widget-status-badge mt-2 bg-positive/10 text-positive"><i class="fa-solid fa-check"></i> Migliorato</div>
                        </div>
                        <div class="widget-card widget-purple p-6 text-left">
                            <div class="widget-label mb-2">2024</div>
                            <div class="widget-metric-large text-positive">+€102k</div>
                            <div class="text-xs text-gray-500 mt-1">PN €156k > Immob. €53.9k</div>
                            <div class="widget-change-positive mt-1">+39% vs 2023</div>
                        </div>
                    </div>
                    <div class="ai-insight-box">
                        <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                            <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                        </div>
                                <strong><i class="fa-solid fa-scale-balanced text-purple"></i> Analisi Strutturale:</strong> Le immobilizzazioni nette (€54k) sono coperte integralmente dal patrimonio netto (€156k). Il capitale circolante netto resta positivo (€169k), segno di autonoma copertura del ciclo operativo.
                    </div>
                </div>

                <!-- Conto Economico -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Conto Economico Comparativo</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Clicca sulle intestazioni delle colonne per ordinare la tabella. La variazione % è calcolata rispetto all'esercizio 2023.</div>
                        </div>
                    </div>
                    <div class="widget-card p-6 overflow-x-auto">
                        <table class="w-full text-sm border-collapse sortable-table" id="incomeTable">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="string">Voce</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="number">2022</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="number">2023</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="number">2024</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 text-[11px] uppercase tracking-wide" data-sort="number">Var % vs 2023</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="font-semibold bg-gray-50 border-b border-gray-200">
                                    <td class="px-4 py-3">Ricavi</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="303036">€303.036</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="476070">€476.070</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="535834">€535.834</td>
                                    <td class="px-4 py-3 text-right text-positive font-bold" data-sort-value="12.6">+12.6%</td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-purple-50">
                                    <td class="px-4 py-3 pl-6">Costi per Servizi</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-163237">(€163.237)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-305123">(€305.123)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-341841">(€341.841)</td>
                                    <td class="px-4 py-3 text-right text-negative" data-sort-value="12.0">+12.0%</td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-purple-50">
                                    <td class="px-4 py-3 pl-6">Costi del Personale</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-38860">(€38.860)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-46821">(€46.821)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-53532">(€53.532)</td>
                                    <td class="px-4 py-3 text-right text-negative" data-sort-value="14.3">+14.3%</td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-purple-50">
                                    <td class="px-4 py-3 pl-6">Altri Costi Operativi</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-3535">(€3.535)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-9606">(€9.606)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-3645">(€3.645)</td>
                                    <td class="px-4 py-3 text-right text-positive" data-sort-value="-62.1">-62.1%</td>
                                </tr>
                                <tr class="font-semibold bg-purple/5 border-b border-gray-200">
                                    <td class="px-4 py-3">EBITDA</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="64832">€64.832</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="80484">€80.484</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="105995">€105.995</td>
                                    <td class="px-4 py-3 text-right text-positive font-bold" data-sort-value="31.7">+31.7%</td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 pl-6">Ammortamenti</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-11062">(€11.062)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-11246">(€11.246)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-22862">(€22.862)</td>
                                    <td class="px-4 py-3 text-right text-negative" data-sort-value="103.3">+103.3%</td>
                                </tr>
                                <tr class="font-semibold bg-purple/5 border-b border-gray-200">
                                    <td class="px-4 py-3">EBIT (Utile Operativo)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="53770">€53.770</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="69238">€69.238</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="83133">€83.133</td>
                                    <td class="px-4 py-3 text-right text-positive font-bold" data-sort-value="20.1">+20.1%</td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 pl-6">Oneri Finanziari Netti</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-2932">(€2.932)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-5478">(€5.478)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-4483">(€4.483)</td>
                                    <td class="px-4 py-3 text-right text-positive" data-sort-value="-18.2">-18.2%</td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3 pl-6">Imposte</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-18802">(€18.802)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-23409">(€23.409)</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="-26280">(€26.280)</td>
                                    <td class="px-4 py-3 text-right text-negative" data-sort-value="12.3">+12.3%</td>
                                </tr>
                                <tr class="font-semibold bg-purple/5">
                                    <td class="px-4 py-3">Utile Netto</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="32036">€32.036</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="40351">€40.351</td>
                                    <td class="px-4 py-3 text-right" data-sort-value="52370">€52.370</td>
                                    <td class="px-4 py-3 text-right text-positive font-bold" data-sort-value="29.8">+29.8%</td>
                                </tr>
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
                        <strong><i class="fa-solid fa-chart-column text-purple"></i> Performance Economica:</strong> I ricavi 2024 toccano €536k (+€60k) e l’utile netto €52k (+€12k). La marginalità resta a doppia cifra nonostante l’aumento dei costi esterni.
                    </div>
                </div>

                <!-- Stato Patrimoniale -->
                <div class="mb-12 sm:mb-20">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider">Stato Patrimoniale Sintetico</div>
                        <div class="tooltip-container">
                            <i class="fa-solid fa-circle-info text-gray-400 text-xs cursor-help"></i>
                            <div class="tooltip-content">Situazione patrimoniale al termine di ciascun esercizio. Variazioni calcolate vs 2023.</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="widget-card widget-purple p-4 sm:p-6">
                            <div class="widget-detail-header">Attivo</div>
                            <div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Immobilizzazioni</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€53.9k</span>
                                        <span class="ml-2 widget-text text-positive">+77% vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Crediti Commerciali</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€220.7k</span>
                                        <span class="ml-2 widget-text text-positive">+16% vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Disponibilità Liquide</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€78.8k</span>
                                        <span class="ml-2 widget-text text-negative">-7% vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Attività Finanziarie</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€0.0k</span>
                                        <span class="ml-2 widget-text text-gray-500">invariato</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Ratei e Risconti</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€3.8k</span>
                                        <span class="ml-2 widget-text text-positive">+11% vs 2023</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center font-bold">
                                    <span class="widget-label">Totale Attivo</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€357.1k</span>
                                        <span class="ml-2 widget-text text-positive">+16% vs 2023</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-card widget-purple p-4 sm:p-6">
                            <div class="widget-detail-header">Passivo</div>
                            <div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Patrimonio Netto</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€155.6k</span>
                                        <span class="ml-2 widget-text text-positive">+51% vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Fondi Rischi</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€19.2k</span>
                                        <span class="ml-2 widget-text text-gray-500">invariato</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">TFR</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€1.6k</span>
                                        <span class="ml-2 widget-text text-positive">+€0.8k vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Debiti a Breve</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€125.7k</span>
                                        <span class="ml-2 widget-text text-negative">+14% vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Debiti a Lungo</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€50.7k</span>
                                        <span class="ml-2 widget-text text-positive">-28% vs 2023</span>
                                    </div>
                                </div>
                                <div class="widget-detail-row">
                                    <span class="widget-detail-label">Ratei e Risconti</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€4.4k</span>
                                        <span class="ml-2 widget-text text-positive">+8% vs 2023</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center font-bold">
                                    <span class="widget-label">Totale Passivo</span>
                                    <div class="text-right">
                                        <span class="widget-detail-value">€357.1k</span>
                                        <span class="ml-2 widget-text text-positive">+16% vs 2023</span>
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
                        <strong><i class="fa-solid fa-building-columns text-purple"></i> Solidità Patrimoniale:</strong> Patrimonio netto €156k (+€53k vs 2023). Crediti €221k e liquidità €79k coprono interamente i debiti a breve (€126k). Le immobilizzazioni (€54k) sono finanziate con mezzi propri.
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
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Costi Fissi Stimati</span>
                                        <span class="text-sm font-bold text-gray-700">€80k</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Costi Variabili (2024)</span>
                                        <span class="text-sm font-bold text-gray-700">€456k</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Margine di Contribuzione %</span>
                                        <span class="text-sm font-bold text-gray-700">65%</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                        <span class="text-xs text-gray-600">Punto di Pareggio</span>
                                        <span class="font-bold text-xl text-primary">€262k</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-600">Margine di Sicurezza</span>
                                        <span class="font-semibold text-positive">51%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-check text-purple"></i> Solidità:</strong> Punto di pareggio stimato a €262k: il margine di sicurezza è del 51% rispetto ai ricavi 2024.
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
                                    <div class="widget-detail-row">
                                        <span class="widget-detail-label">Ricavi / Costo Personale</span>
                                        <div class="text-right">
                                            <span class="widget-detail-value">10.0x</span>
                                            <span class="ml-2 widget-text text-gray-600">-2% vs 2023</span>
                                        </div>
                                    </div>
                                    <div class="widget-detail-row">
                                        <span class="widget-detail-label">Valore Aggiunto / Personale</span>
                                        <div class="text-right">
                                            <span class="widget-detail-value">€107k</span>
                                            <span class="ml-2 widget-text text-positive">+5% vs 2023</span>
                                        </div>
                                    </div>
                                    <div class="widget-detail-row">
                                        <span class="widget-detail-label">EBITDA / Personale</span>
                                        <div class="text-right">
                                            <span class="widget-detail-value">€69k</span>
                                            <span class="ml-2 widget-text text-positive">+€9k vs 2023</span>
                                        </div>
                                    </div>
                                    <div class="widget-detail-row">
                                        <span class="widget-detail-label">Costo Medio Dipendente</span>
                                        <div class="text-right">
                                            <span class="widget-detail-value">€35k</span>
                                            <span class="ml-2 widget-text text-gray-600">+2% vs 2023</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="widget-detail-label">N. Dipendenti (stimato)</span>
                                        <span class="widget-detail-value">~2</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-chart-line text-purple"></i> Performance:</strong> EBITDA per FTE ~€69k (+€9k YoY) e ricavi per FTE ~€350k. Anche con una struttura molto snella la produttività rimane elevata.
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
                                    <div class="p-3 bg-gray-50 border border-gray-200">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="widget-label-medium">2022→2023</span>
                                            <span class="widget-metric-small">€2.1k</span>
                                        </div>
                                        <div class="widget-text text-gray-500">Δ Immob. -€9.1k + Ammort. €11.2k</div>
                                        <div class="widget-text text-gray-600 mt-1">Manutenzione ordinaria</div>
                                    </div>
                                    <div class="p-3 bg-gray-50 border border-gray-200">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="widget-label-medium">2023→2024</span>
                                            <span class="widget-metric-small">€46.3k</span>
                                        </div>
                                        <div class="widget-text text-gray-500">Δ Immob. €23.4k + Ammort. €22.9k</div>
                                        <div class="widget-change-positive mt-1 text-positive">Nuovo ciclo di investimenti</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-check text-purple"></i> Strategia:</strong> Dopo un 2023 di semplice manutenzione (~€2k di capex), nel 2024 sono stati investiti €46k per rafforzare l'infrastruttura digitale. Prossimo step: mettere a regime gli asset potenziati nel 2025.
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
                            <div class="bg-gray-50  border border-gray-200 p-6">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <span class="text-[11px] text-gray-500 uppercase font-semibold">Margine Netto</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-[10px] cursor-help"></i>
                                        <div class="tooltip-content">Utile Netto / Ricavi. Quanto profitto rimane per ogni euro di vendita.</div>
                                    </div>
                                </div>
                                <div class="relative h-[200px]"><canvas id="dupontMarginChart"></canvas></div>
                                <div class="text-left mt-2">
                                    <div class="text-xl font-semibold text-primary">18.3%</div>
                                    <div class="text-xs text-positive">+17.6pp vs 2024</div>
                                </div>
                            </div>
                            <div class="bg-gray-50  border border-gray-200 p-6">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <span class="text-[11px] text-gray-500 uppercase font-semibold">Rotazione Attivo</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-[10px] cursor-help"></i>
                                        <div class="tooltip-content">Ricavi / Totale Attivo. Efficienza nell'utilizzo delle risorse.</div>
                                    </div>
                                </div>
                                <div class="relative h-[200px]"><canvas id="dupontTurnoverChart"></canvas></div>
                                <div class="text-left mt-2">
                                    <div class="text-xl font-semibold text-primary">0.92x</div>
                                    <div class="text-xs text-positive">+33% vs 2024</div>
                                </div>
                            </div>
                            <div class="bg-gray-50  border border-gray-200 p-6">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <span class="text-[11px] text-gray-500 uppercase font-semibold">Leva Finanziaria</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-[10px] cursor-help"></i>
                                        <div class="tooltip-content">Totale Attivo / Patrimonio Netto. Grado di indebitamento.</div>
                                    </div>
                                </div>
                                <div class="relative h-[200px]"><canvas id="dupontLeverageChart"></canvas></div>
                                <div class="text-left mt-2">
                                    <div class="text-xl font-semibold text-primary">2.69x</div>
                                    <div class="text-xs text-positive">-39% vs 2024</div>
                                </div>
                            </div>
                            <div class="bg-gray-50  border border-gray-200 p-6">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <span class="text-[11px] text-gray-500 uppercase font-semibold">ROA</span>
                                    <div class="tooltip-container">
                                        <i class="fa-solid fa-circle-info text-gray-400 text-[10px] cursor-help"></i>
                                        <div class="tooltip-content">Return on Assets = Utile Netto / Totale Attivo. Redditività del capitale investito.</div>
                                    </div>
                                </div>
                                <div class="relative h-[200px]"><canvas id="roaChart"></canvas></div>
                                <div class="text-left mt-2">
                                    <div class="text-xl font-semibold text-primary">16.8%</div>
                                    <div class="text-xs text-positive">+16.4pp vs 2024</div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong>Insight:</strong> Il ROE 2024 al 33.7% è sostenuto da margini in miglioramento (+1.6pp) con leva in calo. Crescita sana e autofinanziata.
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
                            <div class="flex items-center gap-6 p-4 bg-red-50 border border-red-300">
                                <div class="bg-red-500 text-white text-xs font-bold px-2 py-1">P1</div>
                                <div>
                                    <div class="font-semibold text-primary">Ridurre DSO a 120 giorni</div>
                                    <div class="text-xs text-gray-600">DSO 150gg (+3% YoY) blocca ~€45k. Azioni: revisione termini, reminder automatici, incentivi per incassi anticipati.</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 p-4 bg-red-50/70 border border-red-200">
                                <div class="bg-red-400 text-white text-xs font-bold px-2 py-1">P2</div>
                                <div>
                                    <div class="font-semibold text-primary">Ribilanciare il debito a breve</div>
                                    <div class="text-xs text-gray-600">Il 71% del debito è entro 12 mesi (€126k). Azioni: rifinanziare parte a M/L e negoziare linee committed.</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 p-4 bg-purple/3 border border-purple/15">
                                <div class="bg-purple/60 text-white text-xs font-bold px-2 py-1">P3</div>
                                <div>
                                    <div class="font-semibold text-primary">Proteggere i margini</div>
                                    <div class="text-xs text-gray-600">Margine EBITDA 19.8% (+2.9pp) ma costi servizi +12%. Azioni: pricing indicizzato, mix consulenziale a maggior valore, controllo fornitori.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Matrix -->
                <div class="mb-12 sm:mb-20">
                    <div class="text-[11px] font-medium text-gray-600 uppercase tracking-wider mb-3">Matrice dei Rischi</div>
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <div class="widget-card widget-purple p-6">
                            <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-200">
                                <span class="risk-badge text-[9px] font-bold px-1.5 py-0.5 uppercase">Basso</span>
                                <span class="text-xs font-semibold text-primary uppercase">Rischio Operativo</span>
                            </div>
                            <div class="text-[13px] leading-relaxed text-gray-700">
                                <ul class="space-y-1.5">
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">Margine EBITDA 19.8% (+2.9pp vs 2023)</li>
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">Break-even a €262k con 51% di margine sicurezza</li>
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">Leva operativa gestibile (EBIT +20% YoY)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-200">
                                <span class="risk-badge text-[9px] font-bold px-1.5 py-0.5 uppercase">Basso</span>
                                <span class="text-xs font-semibold text-primary uppercase">Rischio Finanziario</span>
                            </div>
                            <div class="text-[13px] leading-relaxed text-gray-700">
                                <ul class="space-y-1.5">
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">D/E 1.13x (-0.62x YoY)</li>
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">ICR 16.6x (>5x soglia)</li>
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">DSCR 2.8x con CFO €70k</li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-200">
                                <span class="risk-badge text-[9px] font-bold px-1.5 py-0.5 uppercase">Medio</span>
                                <span class="text-xs font-semibold text-primary uppercase">Rischio Liquidità</span>
                            </div>
                            <div class="text-[13px] leading-relaxed text-gray-700">
                                <ul class="space-y-1.5">
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">Cash Ratio 0.61x (>0.2 target)</li>
                                    <li class="pl-3.5 relative before:content-['✓'] before:absolute before:left-0 before:text-purple">Current Ratio 2.30x</li>
                                    <li class="pl-3.5 relative before:content-['⚠'] before:absolute before:left-0 before:text-red-500">DSO 150gg richiede presidio crediti</li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-card widget-purple p-6">
                            <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-200">
                                <span class="risk-badge text-[9px] font-bold px-1.5 py-0.5 uppercase">Info</span>
                                <span class="text-xs font-semibold text-primary uppercase">Rischio Concentrazione</span>
                            </div>
                            <div class="text-[13px] leading-relaxed text-gray-700">
                                <ul class="space-y-1.5">
                                    <li class="pl-3.5 relative before:content-['?'] before:absolute before:left-0 before:text-purple">Dipendenza top client da verificare</li>
                                    <li class="pl-3.5 relative before:content-['?'] before:absolute before:left-0 before:text-purple">Mix settoriale da analizzare</li>
                                    <li class="pl-3.5 relative before:content-['→'] before:absolute before:left-0 before:text-purple">Dati non disponibili nei bilanci</li>
                                </ul>
                            </div>
                        </div>
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
                                <div class="border border-gray-200 p-5 mb-3">
                                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-2">Z-Score 2024</div>
                                    <div class="text-2xl font-semibold text-primary mb-1">3.23</div>
                                    <div class="text-xs text-positive font-semibold mb-3">+5% vs 2023</div>
                                    <div class="inline-block px-2 py-1 bg-positive/10 text-positive font-semibold text-[10px] border border-positive/30">ZONA SICURA</div>
                                </div>

                                <!-- Components compact -->
                                <div class="text-[10px] font-semibold text-gray-700 uppercase tracking-wide mb-2">Componenti</div>
                                <div class="space-y-0">
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                        <span class="text-[10px] text-gray-600">X1</span>
                                        <span class="font-semibold text-[10px]">0.47</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                        <span class="text-[10px] text-gray-600">X2</span>
                                        <span class="font-semibold text-[10px]">0.41</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                        <span class="text-[10px] text-gray-600">X3</span>
                                        <span class="font-semibold text-[10px]">0.23</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                        <span class="text-[10px] text-gray-600">X4</span>
                                        <span class="font-semibold text-[10px]">0.77</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1.5">
                                        <span class="text-[10px] text-gray-600">X5</span>
                                        <span class="font-semibold text-[10px]">1.50</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ai-insight-box">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-purple/20">
                                <span class="badge-ai bg-purple text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide">AI Insight</span>
                            </div>
                            <strong><i class="fa-solid fa-shield-check text-purple"></i> Solidità Finanziaria:</strong> Lo Z-Score di 3.23 mantiene Fabbrica del Valore in “zona sicura”. Rischio di default molto basso (<2%) grazie a leverage contenuto e cash flow stabile.
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
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-file-pdf text-purple"></i>
                                            <a href="docs/bilanci/IT09015082-2024-Esercizio-1-101239144.pdf" target="_blank" class="font-medium text-purple hover:underline">IT09015082-2024-Esercizio-1.pdf</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">Bilancio CEE</td>
                                    <td class="px-4 py-3">2024</td>
                                    <td class="px-4 py-3">31/12/2024</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="docs/bilanci/IT09015082-2024-Esercizio-1-101239144.pdf" download class="action-btn inline-flex items-center justify-center"><i class="fa-solid fa-download"></i></a>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-file-pdf text-purple"></i>
                                            <a href="docs/bilanci/IT09015082-2023-Esercizio-2-101239144.pdf" target="_blank" class="font-medium text-purple hover:underline">IT09015082-2023-Esercizio-2.pdf</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">Bilancio CEE</td>
                                    <td class="px-4 py-3">2023</td>
                                    <td class="px-4 py-3">31/12/2023</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="docs/bilanci/IT09015082-2023-Esercizio-2-101239144.pdf" download class="action-btn inline-flex items-center justify-center"><i class="fa-solid fa-download"></i></a>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-file-pdf text-purple"></i>
                                            <a href="docs/bilanci/IT09015082-2022-Esercizio-3-101239144.pdf" target="_blank" class="font-medium text-purple hover:underline">IT09015082-2022-Esercizio-3.pdf</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">Bilancio CEE</td>
                                    <td class="px-4 py-3">2022</td>
                                    <td class="px-4 py-3">31/12/2022</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="docs/bilanci/IT09015082-2022-Esercizio-3-101239144.pdf" download class="action-btn inline-flex items-center justify-center"><i class="fa-solid fa-download"></i></a>
                                    </td>
                                </tr>
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
                            <p class="mb-2">I bilanci sono estratti dal portale della Camera di Commercio e rappresentano le dichiarazioni finanziarie ufficiali depositate.</p>
                            <p><strong>Periodo di Copertura:</strong> Esercizi chiusi al 31 dicembre 2022, 2023 e 2024.</p>
                            <p><strong>Formato:</strong> PDF conformi alla tassonomia XBRL itcc-ci-2018-11-04.</p>
                            <p><strong>Note:</strong> Dati riferiti a Fabbrica del Valore S.r.l. (Padova), codice fiscale 05070220289.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
