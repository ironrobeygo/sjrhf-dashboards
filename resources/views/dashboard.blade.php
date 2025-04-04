<!DOCTYPE html>
<html>
<head>
    <title>Major Dashboard</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    <!-- Chart.js + DataLabels -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="max-w-screen-xl mx-auto py-6 px-4">
        <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Funnel Chart -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-2">Funnel Chart</h2>
                <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                 Past 12 Months
                </span>
                <canvas id="funnelChart" height="200"></canvas>
            </div>

             <!-- Prospect Proposal Summary Chart -->
             <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-2">Prospect Proposal Summary </h2>
                <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                Active
                </span>
                <canvas id="proposalChart" height="200"></canvas>
            </div>

            <!-- Funded Opportunities (Current Fiscal Year) -->
            <div 
                class="bg-white border border-gray-200 shadow rounded-xl p-6 cursor-pointer relative"
                onclick="window.location.href='{{ route('opportunities.funded.details') }}'">

                <!-- Header (Top Left) -->
                <div class="absolute top-6 left-6 text-left">
                    <h2 class="text-lg font-semibold text-gray-800">Funded Opportunities</h2>
                    <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                        Current Fiscal Year
                    </span>
                </div>

                <!-- Centered Metrics -->
                <div class="flex flex-col items-center justify-center h-full text-center pt-16 pb-4">
                    <div class="mb-4">
                        <p class="text-5xl font-bold text-blue-600">{{ $fundedData->total_funded_opportunities }}</p>
                        <p class="text-sm text-gray-600 mt-1">Opportunities</p>
                    </div>
                    <div>
                        <p class="text-3xl font-semibold text-green-600">${{ number_format($fundedData->sum_funded, 2) }}</p>
                        <p class="text-sm text-gray-600 mt-1">Total Amount Funded</p>
                    </div>
                </div>
            </div>

            <!-- Placeholder Insight 4 -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-2">Open Opportunities by Purpose</h2>
                <canvas id="purposeChart" height="200"></canvas>
            </div>

            <!-- Individuals vs Organizations (Open Proposals) Insight -->
            <div class="bg-white shadow rounded-lg p-4 w-full">
                <h2 class="text-xl font-semibold mb-2">Individuals vs Organizations</h2>
                <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                Open Proposals
                </span>
                <canvas id="openProposalsChart" height="150"></canvas>
            </div>

            <!-- Placeholder Insight 5 -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-2">Solicited/Ask Made vs. Funded/Closed</h2>
                <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                Last 12 Months
                </span>
                <canvas id="solicitedFundedChart" height="100"></canvas>
            </div>
        </div>
        <div class="mt-6">
            <!-- Constituent Actions by Fundraiser -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-2">Action Category Summary by RM</h2>
                <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                    Current Fiscal Year
                </span>
                <canvas id="actionSummary" height="100"></canvas>
            </div>

            <!-- Constituent Actions by Fundraiser -->
            <div class="bg-white shadow rounded-lg p-4 mt-6">
                <h2 class="text-xl font-semibold mb-2">Action Type Summary by RM</h2>
                <span class="text-xs bg-green-100 text-green-800 font-medium px-2 py-1 rounded-full">
                    Current Fiscal Year
                </span>
                <canvas id="actionTypeSummary" height="100"></canvas>
            </div>
        </div>
    </div>

    <script>
        function abbreviateNumber(value) {
            const suffixes = ["", "k", "M", "B"];
            const tier = Math.log10(value) / 3 | 0;

            if (tier === 0) return value.toLocaleString();

            const suffix = suffixes[tier];
            const scale = Math.pow(10, tier * 3);
            const scaled = value / scale;

            return scaled.toFixed(1).replace(/\.0$/, '') + suffix;
        }

        function cleanPurpose(purpose) {
            // Remove any part starting with a parenthesis
            if (purpose.indexOf('(') !== -1) {
                purpose = purpose.substring(0, purpose.indexOf('('));
            }
            // Trim and convert to lowercase
            purpose = purpose.trim().toLowerCase();
            // Replace any sequence of non-alphanumeric characters with a dash
            purpose = purpose.replace(/[^a-z0-9]+/g, '-');
            // Remove leading or trailing dashes
            purpose = purpose.replace(/^-+|-+$/g, '');
            return purpose;
        }

        /**********
         * 1) Funnel Chart
         **********/
        const funnelCtx = document.getElementById('funnelChart').getContext('2d');
        const funnelChart = new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($funnel)), // e.g. ['Target Ask', 'Expected', 'Funded']
                datasets: [{
                    label: 'Amount ($)',
                    data: @json(array_values($funnel)),
                    backgroundColor: ['#118DFF', '#F57C00', '#2E7D32']
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return abbreviateNumber(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 0, // Hide the color box
                            color: '#000'
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        anchor: 'center',
                        align: 'center',
                        font: { weight: 'bold' },
                        formatter: function(value) {
                            return '$' + Number(value).toLocaleString();
                        }
                    }
                },
                onClick: (evt, elements) => {
                    if (!elements.length) return;
                    const index = elements[0].index;
                    const label = funnelChart.data.labels[index];
                    
                    // Map label to route param
                    let param = '';
                    if (label === 'Target Ask') {
                        param = 'target';
                    } else if (label === 'Expected') {
                        param = 'expected';
                    } else if (label === 'Funded') {
                        param = 'funded';
                    }
                    if (param) {
                        window.location.href = `/opportunities/${param}`;
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
       
        /**********
         * 2) Prospect Proposal Summary (Active)
         **********/
        const proposalCtx = document.getElementById('proposalChart').getContext('2d');
        const proposalChart = new Chart(proposalCtx, {
            type: 'bar',
            data: {
                labels: @json($summaryLabels),
                datasets: [{
                    label: 'Active Proposals',
                    data: @json($summaryCounts),
                    backgroundColor: [
                        '#7E57C2', // Funded/Closed
                        '#66BB6A', // Identification
                        '#FFCA28', // Cultivation
                        '#42A5F5', // Solicited - Ask Made
                        '#26A69A', // Qualification
                        '#EF5350', // No Response
                        '#AB47BC', // Never Submitted
                        '#FFA726', // Declined
                        '#26C6DA', // Verbal Agreement
                        '#8D6E63', // Deferred
                        '#D4E157', // Pre-Ask
                    ]
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 0, // ⬅️ removes the color square
                            color: '#000'
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        anchor: 'center',
                        align: 'center',
                        font: { weight: 'bold' },
                        formatter: value => value
                    }
                },
                onClick: (evt, elements) => {
                    if (!elements.length) return;
                    const i = elements[0].index;
                    const statusLabel = proposalChart.data.labels[i];
                    const cleaned = cleanPurpose(statusLabel);
                    window.location.href = `/proposal-summary/${cleaned}`;
                }
            },
            plugins: [ChartDataLabels]
        });

        /**********
         * 3) Individuals vs Organizations (Open Proposals) Insight
         **********/
        const openProposalsCtx = document.getElementById('openProposalsChart').getContext('2d');
        const openProposalsChart = new Chart(openProposalsCtx, {
            type: 'pie',
            data: {
                labels: ['Individuals', 'Organizations'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $individualOpenCount }}, {{ $organizationOpenCount }}],
                    backgroundColor: ['#36A2EB', '#FFCE56']
                }]
            },
            options: {
                responsive: true,
                onClick: function(evt, elements) {
                    if (!elements.length) return;
                    const index = elements[0].index;
                    let group = '';
                    if (index === 0) {
                        group = 'Individual';
                    } else if (index === 1) {
                        group = 'Organization';
                    }
                    if (group) {
                        // Redirect to a details page for the selected group
                        window.location.href = `/open-proposals/${group}`;
                    }
                },
                plugins: {
                    datalabels: {
                        color: '#fff',
                        formatter: function(value) {
                            return value;
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        const purposeCtx = document.getElementById('purposeChart').getContext('2d');
        const purposeChart = new Chart(purposeCtx, {
            type: 'bar',
            data: {
                labels: @json($purposeLabels),
                datasets: [{
                    label: 'Active Proposals by Purpose',
                    data: @json($purposeCounts),
                    backgroundColor: [
                        '#42A5F5', // Leadership
                        '#66BB6A', // Sponsorship
                        '#FFA726', // Major
                        '#AB47BC', // Transformational
                        '#EF5350'  // Mid-Level
                    ]
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 0, // Hide the color box
                            color: '#000'
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        anchor: 'center',
                        align: 'center',
                        font: { weight: 'bold' },
                        formatter: value => value
                    }
                },
                onClick: (evt, elements) => {
                    if (!elements.length) return;
                    const i = elements[0].index;
                    const label = purposeChart.data.labels[i];
                    const cleaned = cleanPurpose(label);
                    window.location.href = `/open-opportunities-purpose/${cleaned}`;
                }
            },
            plugins: [ChartDataLabels]
        });

        const solicitedCount = {{ $solicitedAskCount }};
        const fundedCount = {{ $fundedClosedCount }};

        const solicitedFundedCtx = document.getElementById('solicitedFundedChart').getContext('2d');
        const solicitedFundedChart = new Chart(solicitedFundedCtx, {
            type: 'pie',
            data: {
                labels: ['Solicited - Ask Made', 'Funded/Closed'],
                datasets: [{
                    data: [solicitedCount, fundedCount],
                    backgroundColor: ['#FF6384', '#36A2EB']
                }]
            },
            options: {
                onClick: function(evt, elements) {
                    if (!elements.length) return;
                    const index = elements[0].index;
                    let opportunityType = '';
                    if (index === 0) {
                        opportunityType = 'solicited-ask-made';
                    } else if (index === 1) {
                        opportunityType = 'funded-closed';
                    }
                    // Redirect to the details page based on the opportunity type
                    window.location.href = `/opportunities-details/${encodeURIComponent(opportunityType)}`;
                },
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#fff',
                        formatter: function(value) {
                            return value;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        const actionSummaryCtx = document.getElementById('actionSummary').getContext('2d');
        const actionSummaryChart = new Chart(actionSummaryCtx, {
            type: 'bar',
            data: {
                labels: @json($fundraiserCategoryLabels),
                datasets: @json($fundraiserActionCategoryChart)
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    datalabels: {
                        color: '#fff',
                        anchor: 'center',
                        align: 'center',
                        font: { weight: 'bold' },
                        formatter: value => value,
                        display: ctx => ctx.dataset.data[ctx.dataIndex] > 0
                    }
                },
                onClick: (e, elements) => {
                    if (!elements.length) return;
                    const el = elements[0];
                    const fundraiser = encodeURIComponent(actionSummaryChart.data.labels[el.index]);
                    const actionType = encodeURIComponent(actionSummaryChart.data.datasets[el.datasetIndex].label);
                    window.location.href = `/actions/${fundraiser}/${actionType}`;
                }
            },
            plugins: [ChartDataLabels]
        });

        const actionTypeSummaryCtx = document.getElementById('actionTypeSummary').getContext('2d');
        const actionTypeSummaryChart = new Chart(actionTypeSummaryCtx, {
            type: 'bar',
            data: {
                labels: @json($fundraiserTypeLabels),
                datasets: @json($fundraiserActionTypeChart)
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    datalabels: {
                        color: '#fff',
                        anchor: 'center',
                        align: 'center',
                        font: { weight: 'bold' },
                        formatter: value => value,
                        display: ctx => ctx.dataset.data[ctx.dataIndex] > 0
                    }
                },
                onClick: (e, elements) => {
                    if (!elements.length) return;
                    const el = elements[0];
                    const fundraiser = encodeURIComponent(actionTypeSummaryChart.data.labels[el.index]);
                    const actionType = encodeURIComponent(actionTypeSummaryChart.data.datasets[el.datasetIndex].label);
                    window.location.href = `/action-type/${fundraiser}/${actionType}`;
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
</body>
</html>