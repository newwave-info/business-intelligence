// Perspect Dashboard - Main JavaScript

// Theme Toggle
function toggleTheme() {
    const html = document.documentElement;
    const themeBtn = document.getElementById('themeToggle');
    const icon = themeBtn?.querySelector('i');

    if (html.classList.contains('dark-mode')) {
        html.classList.remove('dark-mode');
        localStorage.setItem('theme', 'light');
        if (icon) {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
        updateChartColors('light');
    } else {
        html.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');
        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
        updateChartColors('dark');
    }
}

// Update Chart.js colors for theme
function updateChartColors(theme) {
    const textColor = theme === 'dark' ? '#94a3b8' : '#64748b';
    const gridColor = theme === 'dark' ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)';
    const borderColor = theme === 'dark' ? '#334155' : '#e2e8f0';

    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = borderColor;

    // Update all existing charts
    Object.values(Chart.instances).forEach(chart => {
        // Update x and y scales
        if (chart.options.scales) {
            ['x', 'y', 'r'].forEach(axis => {
                if (chart.options.scales[axis]) {
                    // Ensure grid object exists
                    chart.options.scales[axis].grid = chart.options.scales[axis].grid || {};
                    chart.options.scales[axis].grid.color = gridColor;
                    chart.options.scales[axis].grid.borderColor = borderColor;

                    // Ensure ticks object exists
                    chart.options.scales[axis].ticks = chart.options.scales[axis].ticks || {};
                    chart.options.scales[axis].ticks.color = textColor;

                    // For radar charts
                    if (axis === 'r') {
                        chart.options.scales[axis].angleLines = chart.options.scales[axis].angleLines || {};
                        chart.options.scales[axis].angleLines.color = gridColor;
                        chart.options.scales[axis].pointLabels = chart.options.scales[axis].pointLabels || {};
                        chart.options.scales[axis].pointLabels.color = textColor;
                    }
                }
            });
        }

        chart.update();
    });
}

// Get pattern color based on theme
function getPatternColor() {
    const isDark = document.documentElement.classList.contains('dark-mode');
    return isDark ? 'rgba(30, 41, 59, 0.7)' : 'rgba(255, 255, 255, 0.7)';
}

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    const themeBtn = document.getElementById('themeToggle');
    const icon = themeBtn?.querySelector('i');

    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-mode');
        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
        // Apply chart colors after a delay to ensure charts are loaded
        setTimeout(() => updateChartColors('dark'), 500);
    }
}

// Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Accordion Toggle
function toggleAccordion(targetId) {
    const content = document.getElementById(targetId);
    if (!content) return;

    content.classList.toggle('hidden');

    const isOpen = !content.classList.contains('hidden');
    const toggleButton = document.querySelector(`[data-accordion-toggle="${targetId}"]`);
    const icon = toggleButton?.querySelector('.accordion-icon');
    if (icon) {
        icon.classList.toggle('rotate-180', isOpen);
    }

    // Gestione stato active/current
    if (toggleButton) {
        const allIcons = toggleButton.querySelectorAll('i');
        const label = toggleButton.querySelector('span');

        if (isOpen) {
            toggleButton.classList.add('text-purple-600');
            toggleButton.classList.remove('text-gray-600', 'text-gray-800');
            allIcons.forEach(i => {
                i.classList.add('text-purple-600');
                i.classList.remove('text-gray-400', 'text-gray-600');
            });
            if (label) {
                label.classList.add('text-purple-600');
            }
        } else {
            toggleButton.classList.remove('text-purple-600');
            toggleButton.classList.add('text-gray-600');
            allIcons.forEach(i => {
                i.classList.remove('text-purple-600');
                i.classList.add('text-gray-400');
            });
            if (label) {
                label.classList.remove('text-purple-600');
            }
        }
    }
}

// Chart.js Defaults - check for dark mode from localStorage
const savedThemeForDefaults = localStorage.getItem('theme');
const isDarkOnLoad = savedThemeForDefaults === 'dark';

Chart.defaults.font.family = "'Roboto Mono', monospace";
Chart.defaults.color = isDarkOnLoad ? '#94a3b8' : '#64748b';
Chart.defaults.borderColor = isDarkOnLoad ? '#334155' : '#e2e8f0';
Chart.defaults.font.size = 11;

// Set default scale options with grid colors
Chart.defaults.scales = Chart.defaults.scales || {};
Chart.defaults.scales.linear = Chart.defaults.scales.linear || {};
Chart.defaults.scales.linear.grid = {
    color: isDarkOnLoad ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)',
    borderColor: isDarkOnLoad ? '#334155' : '#e2e8f0'
};
Chart.defaults.scales.category = Chart.defaults.scales.category || {};
Chart.defaults.scales.category.grid = {
    color: isDarkOnLoad ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)',
    borderColor: isDarkOnLoad ? '#334155' : '#e2e8f0'
};
Chart.defaults.scales.radialLinear = Chart.defaults.scales.radialLinear || {};
Chart.defaults.scales.radialLinear.grid = {
    color: isDarkOnLoad ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)'
};
Chart.defaults.scales.radialLinear.angleLines = {
    color: isDarkOnLoad ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)'
};

// Use square points instead of circles
Chart.defaults.elements.point.pointStyle = 'rect';
Chart.defaults.elements.point.rotation = 0;

// HTML Legend Plugin
const htmlLegendPlugin = {
    id: 'htmlLegend',
    afterUpdate(chart, args, options) {
        const legendContainer = document.getElementById(options.containerID);
        if (!legendContainer) return;

        // Clear existing legend
        legendContainer.innerHTML = '';

        const items = chart.options.plugins.legend.labels.generateLabels(chart);

        items.forEach((item, index) => {
            const legendItem = document.createElement('div');
            legendItem.style.cssText = 'display: flex; align-items: center; gap: 6px; cursor: pointer;';

            // Get color from dataset borderColor (works with patterns)
            // For doughnut/pie charts, use the backgroundColor array from the single dataset
            const dataset = chart.data.datasets[0];
            let color;
            if (chart.config.type === 'doughnut' || chart.config.type === 'pie') {
                color = Array.isArray(dataset?.backgroundColor) ? dataset.backgroundColor[index] : item.fillStyle;
            } else {
                const ds = chart.data.datasets[index];
                color = ds?.borderColor || item.strokeStyle || item.fillStyle;
            }

            // Square indicator (hollow)
            const box = document.createElement('span');
            box.style.cssText = `
                width: 8px;
                height: 8px;
                border: 2px solid ${color};
                background: transparent;
                display: inline-block;
            `;

            // Label text
            const label = document.createElement('span');
            label.style.cssText = 'font-size: 10px; color: #52525b; font-family: "Roboto Mono", monospace;';
            label.textContent = item.text;

            if (item.hidden) {
                label.style.textDecoration = 'line-through';
                label.style.opacity = '0.5';
            }

            legendItem.appendChild(box);
            legendItem.appendChild(label);

            // Toggle dataset on click
            legendItem.onclick = () => {
                if (chart.config.type === 'doughnut' || chart.config.type === 'pie') {
                    chart.toggleDataVisibility(index);
                } else {
                    chart.setDatasetVisibility(index, !chart.isDatasetVisible(index));
                }
                chart.update();
            };

            legendContainer.appendChild(legendItem);
        });
    }
};

Chart.register(htmlLegendPlugin);

// View Navigation
function showView(viewId) {
    document.querySelectorAll('.view').forEach(v => v.classList.add('hidden'));
    document.getElementById(viewId)?.classList.remove('hidden');

    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active', 'text-purple-600', 'font-semibold');
        item.classList.add('text-gray-600');
    });

    const evt = typeof event !== 'undefined' ? event : window.event;
    const activeItem = evt?.target?.closest('.nav-item');
    if (activeItem) {
        activeItem.classList.remove('text-gray-600');
        activeItem.classList.add('active', 'text-purple-600', 'font-semibold');
    }

    // Scroll to top on view change
    document.querySelector('.flex-1.overflow-y-auto')?.scrollTo(0, 0);

    setTimeout(() => {
        Object.values(Chart.instances).forEach(chart => chart.resize());
    }, 100);
}

// Track initialized charts
const initializedCharts = new Set();

// Animation config
const animationConfig = {
    duration: 800,
    easing: 'easeOutQuart'
};

// Intersection Observer for chart animations
const chartObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const chartId = entry.target.id;
            if (chartId && !initializedCharts.has(chartId)) {
                initChart(chartId);
                initializedCharts.add(chartId);
            }
        }
    });
}, {
    threshold: 0.2
});

// Intersection Observer for element animations
const animationObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
            animationObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
});

// Initialize sortable tables
document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme
    initTheme();

    if (typeof Tablesort !== 'undefined') {
        document.querySelectorAll('.sortable-table').forEach(table => {
            new Tablesort(table);
        });
    }

    // Observe all chart canvases
    document.querySelectorAll('canvas').forEach(canvas => {
        chartObserver.observe(canvas);
    });

    // Observe elements for animations
    document.querySelectorAll('.widget-card, .widget-metric-large, .widget-metric-medium, .ai-insight-box').forEach(el => {
        animationObserver.observe(el);
    });

    // Initialize sidebar state - Controllo di Gestione open, Dashboard active
    const cgMenu = document.getElementById('cgMenu');
    const cgButton = document.querySelector('[data-accordion-toggle="cgMenu"]');
    if (cgMenu && cgButton) {
        // Ensure accordion is open
        cgMenu.classList.remove('hidden');

        // Ensure button state is active (purple)
        cgButton.classList.add('text-purple-600');
        cgButton.classList.remove('text-gray-600', 'text-gray-800');

        const cgIcons = cgButton.querySelectorAll('i');
        cgIcons.forEach(i => {
            i.classList.add('text-purple-600');
            i.classList.remove('text-gray-400', 'text-gray-600');
        });

        const cgLabel = cgButton.querySelector('span');
        if (cgLabel) {
            cgLabel.classList.add('text-purple-600');
        }

        // Ensure chevron is rotated (open state)
        const chevron = cgButton.querySelector('.accordion-icon');
        if (chevron) {
            chevron.classList.add('rotate-180');
        }
    }
});

// Initialize individual chart by ID
function initChart(chartId) {
    const chartConfigs = getChartConfigs();
    if (chartConfigs[chartId]) {
        new Chart(document.getElementById(chartId), chartConfigs[chartId]);
    }
}

// Get all chart configurations
function getChartConfigs() {
    return {
        'radarChart': {
            type: 'radar',
            data: {
                labels: ['Crescita', 'EBITDA %', 'ROE', 'Liquidità', 'Leva', 'Efficienza'],
                datasets: [
                    {
                        label: '2025',
                        data: [95, 95, 100, 50, 85, 90],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#8b5cf6'
                    },
                    {
                        label: 'Target',
                        data: [75, 80, 85, 75, 70, 80],
                        borderColor: '#3f3f46',
                        backgroundColor: 'rgba(63, 63, 70, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: {
                    legend: { display: false },
                    htmlLegend: { containerID: 'radarChart-legend' }
                },
                scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } } }
            }
        },

        'revenueChart': {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    {
                        label: 'Ricavi',
                        data: [526, 553, 825],
                        borderColor: '#8b5cf6',
                        backgroundColor: pattern.draw('diagonal', 'rgba(139, 92, 246, 0.05)'),
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 2
                    },
                    {
                        label: 'EBITDA',
                        data: [40, 65, 205],
                        borderColor: '#3f3f46',
                        backgroundColor: 'rgba(63, 63, 70, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'revenueChart-legend' } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } } }
            }
        },

        'profitChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    { label: 'EBIT', data: [16, 33, 180], backgroundColor: pattern.draw('diagonal', '#8b5cf6'), borderColor: '#8b5cf6', borderRadius: 0 },
                    { label: 'Utile', data: [3, 4, 151], backgroundColor: pattern.draw('diagonal', '#52525b'), borderColor: '#52525b', borderRadius: 0 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'profitChart-legend' } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } } }
            }
        },

        'currentRatioChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [1.92, 0.92, 1.52],
                    backgroundColor: [pattern.draw('diagonal', '#a78bfa'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 2.5 } }
            }
        },

        'cashRatioChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [0.20, 0.02, 0.06],
                    backgroundColor: [pattern.draw('diagonal', '#a78bfa'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 0.3 } }
            }
        },

        'treasuryMarginChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [-43, -63, 100],
                    backgroundColor: [pattern.draw('diagonal', '#71717a'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: v => v + ' €k' } } }
            }
        },

        'cashFlowWaterfallChart': {
            type: 'bar',
            data: {
                labels: ['Utile Netto', 'Ammortamenti', 'Δ TFR', 'Δ Crediti', 'Δ Debiti', 'Investimenti', 'Δ Cassa'],
                datasets: [{
                    data: [150.7, 25.6, 12.6, 31.2, -69.4, -66.3, 9.8],
                    backgroundColor: [
                        pattern.draw('diagonal', '#8b5cf6'),
                        pattern.draw('diagonal', '#a78bfa'),
                        pattern.draw('diagonal', '#c4b5fd'),
                        pattern.draw('diagonal', '#ddd6fe'),
                        pattern.draw('diagonal', '#71717a'),
                        pattern.draw('diagonal', '#52525b'),
                        pattern.draw('diagonal', '#3f3f46')
                    ],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { ticks: { callback: v => v + ' €k' } } }
            }
        },

        'cashFlowTrendChart': {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    {
                        label: 'Cash Flow Operativo',
                        data: [62, 97, 189],
                        borderColor: '#8b5cf6',
                        backgroundColor: pattern.draw('diagonal', 'rgba(139, 92, 246, 0.05)'),
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 2
                    },
                    {
                        label: 'Disponibilità Liquide',
                        data: [20, 7, 17],
                        borderColor: '#52525b',
                        backgroundColor: 'rgba(82, 82, 91, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'cashFlowTrendChart-legend' } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } } }
            }
        },

        'debtStructureChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    { label: 'Breve', data: [229, 393, 272], backgroundColor: pattern.draw('diagonal', '#8b5cf6'), borderColor: '#8b5cf6', borderRadius: 0 },
                    { label: 'Lungo', data: [200, 182, 234], backgroundColor: pattern.draw('diagonal', '#52525b'), borderColor: '#52525b', borderRadius: 0 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'debtStructureChart-legend' } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } } }
            }
        },

        'icrChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    label: 'ICR',
                    data: [2.2, 1.5, 8.2],
                    backgroundColor: [pattern.draw('diagonal', '#a78bfa'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 10 } }
            }
        },

        'dupontMarginChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [0.5, 0.7, 18.3],
                    backgroundColor: [pattern.draw('diagonal', '#71717a'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 20, ticks: { callback: v => v + '%' } } }
            }
        },

        'dupontTurnoverChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [0.86, 0.69, 0.92],
                    backgroundColor: [pattern.draw('diagonal', '#71717a'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 1.2 } }
            }
        },

        'dupontLeverageChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [3.80, 4.41, 2.69],
                    backgroundColor: [pattern.draw('diagonal', '#a78bfa'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 5 } }
            }
        },

        'roaChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [0.43, 0.45, 16.8],
                    backgroundColor: [pattern.draw('diagonal', '#71717a'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 20, ticks: { callback: v => v + '%' } } }
            }
        },

        'productivityChart': {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    {
                        label: 'Ricavi/Personale',
                        data: [2.66, 2.73, 2.92],
                        borderColor: '#8b5cf6',
                        borderWidth: 3,
                        fill: false,
                        tension: 0,
                        pointRadius: 5
                    },
                    {
                        label: 'VA/Personale (€10k)',
                        data: [4.8, 6.1, 10.0],
                        borderColor: '#3f3f46',
                        borderWidth: 3,
                        fill: false,
                        tension: 0,
                        pointRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'productivityChart-legend' } },
                scales: { y: { beginAtZero: true } }
            }
        },

        'capexChart': {
            type: 'bar',
            data: {
                labels: ['2023→2024', '2024→2025'],
                datasets: [{
                    data: [319, 66],
                    backgroundColor: [pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } } }
            }
        },

        'breakEvenChart': {
            type: 'line',
            data: {
                labels: ['0', '200', '400', '552', '600', '825'],
                datasets: [
                    {
                        label: 'Ricavi',
                        data: [0, 200, 400, 552, 600, 825],
                        borderColor: '#8b5cf6',
                        borderWidth: 3,
                        fill: false,
                        tension: 0,
                        pointRadius: 5
                    },
                    {
                        label: 'Costi Totali',
                        data: [350, 423, 495, 552, 580, 652],
                        borderColor: '#3f3f46',
                        borderWidth: 3,
                        fill: false,
                        tension: 0,
                        pointRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'breakEvenChart-legend' } },
                scales: {
                    x: { display: true },
                    y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } }
                }
            }
        },

        'debtROEChart': {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    {
                        label: 'D/E',
                        data: [2.66, 3.16, 1.52],
                        borderColor: '#3f3f46',
                        backgroundColor: 'rgba(63, 63, 70, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 1
                    },
                    {
                        label: 'ROE %',
                        data: [1.6, 2.0, 45.3],
                        borderColor: '#8b5cf6',
                        backgroundColor: pattern.draw('diagonal', 'rgba(139, 92, 246, 0.05)'),
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'debtROEChart-legend' } },
                scales: { y: { beginAtZero: true, max: 50 } }
            }
        },

        'costDSOChart': {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    {
                        label: 'Costi %',
                        data: [83.5, 82.5, 72.3],
                        borderColor: '#8b5cf6',
                        backgroundColor: pattern.draw('diagonal', 'rgba(139, 92, 246, 0.05)'),
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 2
                    },
                    {
                        label: 'DSO (10gg)',
                        data: [25.4, 21.4, 15.7],
                        borderColor: '#52525b',
                        backgroundColor: 'rgba(82, 82, 91, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'costDSOChart-legend' } },
                scales: { y: { beginAtZero: true, max: 90 } }
            }
        },

        'zscoreChart': {
            type: 'bar',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [{
                    data: [1.85, 1.42, 3.18],
                    backgroundColor: [pattern.draw('diagonal', '#a78bfa'), pattern.draw('diagonal', '#52525b'), pattern.draw('diagonal', '#8b5cf6')],
                    borderRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 4 } }
            }
        },

        'attivoDonutChart': {
            type: 'doughnut',
            data: {
                labels: ['Immob. Materiali', 'Crediti', 'Attività Fin.', 'Liquidità', 'Immob. Fin.', 'Ratei', 'Immob. Immat.'],
                datasets: [{
                    data: [471, 355, 43, 17, 5, 4, 1],
                    backgroundColor: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#52525b', '#71717a', '#a1a1aa'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    offset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    htmlLegend: { containerID: 'attivoDonutChart-legend' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.label}: €${ctx.raw}k`
                        }
                    }
                }
            }
        },

        'passivoDonutChart': {
            type: 'doughnut',
            data: {
                labels: ['Debiti Breve', 'Debiti Lungo', 'Utile Esercizio', 'Capitale', 'Utili a Nuovo', 'TFR', 'Altre Riserve', 'Riserva Legale', 'Ratei'],
                datasets: [{
                    data: [272, 234, 151, 100, 61, 54, 17, 5, 3],
                    backgroundColor: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#52525b', '#71717a', '#a1a1aa', '#d4d4d8', '#e4e4e7'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    offset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    htmlLegend: { containerID: 'passivoDonutChart-legend' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.label}: €${ctx.raw}k`
                        }
                    }
                }
            }
        },

        'costiDonutChart': {
            type: 'doughnut',
            data: {
                labels: ['Servizi', 'Personale', 'Ammortamenti', 'Oneri Diversi', 'Godim. Beni', 'Materie Prime'],
                datasets: [{
                    data: [314, 282, 26, 13, 10, 7],
                    backgroundColor: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#52525b', '#71717a', '#a1a1aa'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    offset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    htmlLegend: { containerID: 'costiDonutChart-legend' },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.label}: €${ctx.raw}k`
                        }
                    }
                }
            }
        },

        'patrimonioLineChart': {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025'],
                datasets: [
                    {
                        label: 'Ricavi',
                        data: [526, 553, 825],
                        borderColor: '#8b5cf6',
                        backgroundColor: pattern.draw('diagonal', 'rgba(139, 92, 246, 0.05)'),
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 7
                    },
                    {
                        label: 'Costi Servizi',
                        data: [241, 254, 314],
                        borderColor: '#a78bfa',
                        backgroundColor: 'rgba(167, 139, 250, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 6
                    },
                    {
                        label: 'Costi Personale',
                        data: [198, 202, 282],
                        borderColor: '#c4b5fd',
                        backgroundColor: 'rgba(196, 181, 253, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 5
                    },
                    {
                        label: 'EBITDA',
                        data: [40, 65, 205],
                        borderColor: '#52525b',
                        backgroundColor: 'rgba(82, 82, 91, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 4
                    },
                    {
                        label: 'EBIT',
                        data: [16, 33, 180],
                        borderColor: '#71717a',
                        backgroundColor: 'rgba(113, 113, 122, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 3
                    },
                    {
                        label: 'Altri Costi',
                        data: [54, 32, 30],
                        borderColor: '#a1a1aa',
                        backgroundColor: 'rgba(161, 161, 170, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 2
                    },
                    {
                        label: 'Ammortamenti',
                        data: [19, 29, 26],
                        borderColor: '#d4d4d8',
                        backgroundColor: 'rgba(212, 212, 216, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0,
                        pointRadius: 5,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animationConfig,
                plugins: { legend: { display: false }, htmlLegend: { containerID: 'patrimonioLineChart-legend' } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' €k' } } }
            }
        }
    };
}

// Cash Flow Data Functions
// Store chart instances for cleanup
const cashFlowCharts = {
    cumulative: null,
    category: null
};

function destroyCashFlowCharts() {
    if (cashFlowCharts.cumulative) {
        cashFlowCharts.cumulative.destroy();
        cashFlowCharts.cumulative = null;
    }
    if (cashFlowCharts.category) {
        cashFlowCharts.category.destroy();
        cashFlowCharts.category = null;
    }
}

function loadCashFlowData() {
    const container = document.getElementById('monthlyCheckpoints');
    if (!container) {
        console.warn('View not yet loaded, retrying...');
        setTimeout(loadCashFlowData, 100);
        return;
    }

    fetch('api/cashflow.php')
        .then(response => response.json())
        .then(data => {
            console.log('Cash flow data loaded, processing...');
            destroyCashFlowCharts();
            initCumulativeCashFlowChart(data.cumulativeFlow);
            generateMonthlyCheckpoints(data.monthlyCheckpoints);
            initMonthlyCategoryChart(data.monthlyCategories);
            console.log('Cash flow view updated');
        })
        .catch(error => console.error('Error loading cash flow data:', error));
}

function initCumulativeCashFlowChart(data) {
    const ctx = document.getElementById('cumulativeCashFlowChart');
    if (!ctx) return;

    // Ensure canvas context is clean
    const canvasElement = ctx.parentElement ? ctx : null;
    if (!canvasElement) return;

    const isDark = document.documentElement.classList.contains('dark-mode');
    const colors = {
        entrate: '#3b82f6',
        uscite: '#ef4444',
        saldo: '#22c55e',
        grid: isDark ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)'
    };

    cashFlowCharts.cumulative = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.dates.map(d => {
                const [year, month] = d.split('-');
                const months = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
                return months[parseInt(month) - 1];
            }),
            datasets: [
                {
                    label: 'Entrate Cumulative',
                    data: data.entrate,
                    borderColor: colors.entrate,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: colors.entrate,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Uscite Cumulative',
                    data: data.uscite,
                    borderColor: colors.uscite,
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: colors.uscite,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Saldo Netto',
                    data: data.saldo,
                    borderColor: colors.saldo,
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: colors.saldo,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: animationConfig,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 12, weight: 500 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => '€' + (v / 1000).toFixed(0) + 'k'
                    },
                    grid: { color: colors.grid }
                },
                x: {
                    grid: { color: colors.grid }
                }
            }
        }
    });
}

function generateMonthlyCheckpoints(monthlyData) {
    const container = document.getElementById('monthlyCheckpoints');
    if (!container) {
        console.warn('monthlyCheckpoints container not found');
        return;
    }

    container.innerHTML = '';

    const months = Object.keys(monthlyData).sort();

    if (months.length === 0) {
        console.warn('No monthly data available');
        return;
    }

    let html = '';

    months.forEach((month, idx) => {
        const data = monthlyData[month];
        const isPositive = data.saldo >= 0;

        html += `
            <div class="widget-card widget-purple p-6 animate-in" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.06) 0%, rgba(255, 255, 255, 0) 100%); border: 1px solid rgba(139, 92, 246, 0.25); border-radius: 8px; animation-delay: ${idx * 0.05}s;">
                <div class="flex justify-between items-start mb-4 pb-3 border-b border-gray-200">
                    <div>
                        <div class="text-[11px] font-medium text-gray-700 uppercase tracking-wider">${data.mese}</div>
                        <div class="text-[10px] text-gray-600 mt-1">${data.transazioni} transazioni</div>
                    </div>
                    <div class="inline-block px-2 py-1 rounded text-[9px] font-semibold ${isPositive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}" style="${isPositive ? 'background-color: #dcfce7; color: #15803d;' : 'background-color: #fee2e2; color: #dc2626;'}">
                        ${isPositive ? '↑ Positivo' : '↓ Negativo'}
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-[10px] text-gray-700 uppercase tracking-wider font-semibold">Entrate</div>
                            <div class="text-sm font-bold text-gray-900 mt-1" style="color: #0f172a;">€${(data.entrate).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        </div>
                        <i class="fa-solid fa-arrow-down-right text-green-600 text-lg"></i>
                    </div>

                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-[10px] text-gray-700 uppercase tracking-wider font-semibold">Uscite</div>
                            <div class="text-sm font-bold text-gray-900 mt-1" style="color: #0f172a;">€${(data.uscite).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right text-red-600 text-lg"></i>
                    </div>

                    <div class="pt-3 border-t border-gray-200">
                        <div class="text-[10px] text-gray-700 uppercase tracking-wider font-semibold mb-1">Saldo Netto</div>
                        <div class="text-lg font-bold" style="color: ${isPositive ? '#16a34a' : '#dc2626'};">
                            €${(data.saldo).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    console.log('Monthly checkpoints generated:', months.length, 'months');
}

function initMonthlyCategoryChart(data) {
    const ctx = document.getElementById('monthlyCategory');
    if (!ctx) return;

    const isDark = document.documentElement.classList.contains('dark-mode');
    const colors = isDark ? 'rgba(71, 85, 105, 0.4)' : 'rgba(0, 0, 0, 0.1)';

    const categoryColors = [
        '#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#8b5cf6',
        '#ec4899', '#06b6d4', '#14b8a6', '#f97316', '#6366f1'
    ];

    cashFlowCharts.category = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.months,
            datasets: data.data.slice(0, 8).map((catData, idx) => ({
                label: data.categories[idx],
                data: catData,
                backgroundColor: categoryColors[idx % categoryColors.length],
                borderRadius: 4,
                maxBarThickness: 60
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: animationConfig,
            indexAxis: 'x',
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 11 }
                    }
                }
            },
            scales: {
                y: {
                    stacked: false,
                    beginAtZero: true,
                    ticks: {
                        callback: v => '€' + (v / 1000).toFixed(0) + 'k'
                    },
                    grid: { color: colors }
                },
                x: {
                    grid: { color: colors }
                }
            }
        }
    });
}

// Initialize cash flow on page load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (document.getElementById('cumulativeCashFlowChart')) {
            loadCashFlowData();
        }
    }, 500);
});

// Also load when cashflow view is shown
const originalShowView = window.showView;
window.showView = function(viewId) {
    if (typeof originalShowView === 'function') {
        originalShowView(viewId);
    }
    if (viewId === 'cashflow' && document.getElementById('cumulativeCashFlowChart')) {
        loadCashFlowData();
    }
};
