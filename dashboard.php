<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klant Dashboard - AdsMarket.nl</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="premium-effects.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .dashboard-layout { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .sidebar { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); border-right: 1px solid var(--gray-200); padding: 40px 24px; position: sticky; top: 0; height: 100vh; z-index: 100; }
        .main-content { background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.05), transparent), radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.05), transparent), var(--gray-50); padding: 40px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        /* Grid and Metric Cards preserved from dash.html */
        .grid-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px; }
        .db-card { background: var(--white); padding: 32px; border-radius: 20px; box-shadow: var(--shadow-sm); border: 1px solid var(--gray-200); }
        .metric-value { font-size: 32px; font-weight: 800; color: var(--gray-900); }
        .status-badge { padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; }
        .status-active { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .nav-sidebar { list-style: none; margin-top: 40px; }
        .nav-sidebar a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; color: var(--gray-600); text-decoration: none; transition: 0.3s; cursor: pointer; }
        .nav-sidebar a.active, .nav-sidebar a:hover { background: var(--primary-gradient); color: var(--white); }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="logo" style="margin-bottom: 40px;">
                <a href="index.php" style="text-decoration:none;"><span class="logo-text">Ads<span class="logo-highlight">Market</span></span></a>
            </div>
            <ul class="nav-sidebar">
                <li><a onclick="switchTab('overview')" class="tab-link active"><span>🏠</span> Overzicht</a></li>
                <li><a onclick="switchTab('performance')" class="tab-link"><span>📊</span> Prestaties</a></li>
                <li><a onclick="switchTab('keywords')" class="tab-link"><span>🔍</span> AI Research</a></li>
                <li><a onclick="switchTab('onboarding')" class="tab-link"><span>🚀</span> Onboarding</a></li>
                <li><a onclick="switchTab('billing')" class="tab-link"><span>💳</span> Facturatie</a></li>
                <li><a onclick="switchTab('support')" class="tab-link"><span>🎧</span> Support</a></li>
                <li><hr style="border:none; border-top:1px solid var(--gray-100); margin: 20px 0;"></li>
                <li><a href="api/logout.php" style="color: #ef4444;"><span>🚪</span> Uitloggen</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="dashboard-header" style="display:flex; justify-content:space-between; margin-bottom:40px;">
                <div>
                    <h1 class="auth-title">Goede dag, <span id="user-name" class="gradient-text"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span></h1>
                    <p class="auth-subtitle">Welkom terug bij uw AdsMarket dashboard.</p>
                </div>
                <div>
                    <span id="global-status" class="status-badge status-pending">Loading...</span>
                </div>
            </div>

            <!-- TAB: OVERVIEW -->
            <div id="overview" class="tab-content active">
                <div class="grid-cards">
                    <div class="db-card">
                        <h3 style="font-size:14px; margin-bottom:15px; color:var(--gray-500);">Totale Uitgaven</h3>
                        <div class="metric-value">€1.240,50</div>
                        <p style="font-size:12px; color:#10b981; margin-top:8px;">↑ 12% vs vorige maand</p>
                    </div>
                    <div class="db-card">
                        <h3 style="font-size:14px; margin-bottom:15px; color:var(--gray-500);">Conversies</h3>
                        <div class="metric-value">48</div>
                        <p style="font-size:12px; color:#10b981; margin-top:8px;">↑ 5 nieuwe leads</p>
                    </div>
                    <div class="db-card">
                        <h3 style="font-size:14px; margin-bottom:15px; color:var(--gray-500);">CPA</h3>
                        <div class="metric-value">€25,84</div>
                        <p style="font-size:12px; color:#ef4444; margin-top:8px;">↓ 8% verbetering</p>
                    </div>
                </div>

                <div class="db-card" style="margin-top:24px;">
                    <h3>ROI <span class="gradient-text">Rapportage Panel</span></h3>
                    <p style="font-size: 14px; color: var(--gray-500); margin-top: 10px;">
                        Real-time prestatiebewaking via uw Google Ads API-integratie.
                    </p>
                </div>
            </div>

            <!-- TAB: PERFORMANCE -->
            <div id="performance" class="tab-content">
                <div class="db-card">
                    <h3>Campagne Prestaties</h3>
                    <div style="height: 400px; margin-top:20px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- TAB: AI KEYWORD RESEARCH -->
            <div id="keywords" class="tab-content">
                <div class="db-card" style="margin-bottom: 24px;">
                    <h2 class="auth-title">AI <span class="gradient-text">Keyword Research</span></h2>
                    <p class="auth-subtitle">Voer bir anahtar kelime girin ve AI'nın pazar niyetini analiz etmesini sağlayın.</p>
                    
                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <input type="text" id="kw-input" placeholder="Bv: 'Schoenen kopen' veya 'Marketing ajansı'" 
                               style="flex: 1; padding: 14px 20px; border-radius: 12px; border: 1px solid var(--gray-200); font-family: inherit;">
                        <button onclick="runAiResearch()" id="research-btn" class="btn btn-primary" style="padding: 0 30px;">
                            Analyseer met AI
                        </button>
                    </div>
                </div>

                <div id="ai-loading" style="display: none; text-align: center; padding: 40px;">
                    <div class="shimmer" style="height: 100px; border-radius: 20px; margin-bottom: 24px;"></div>
                    <p class="gradient-text" style="font-weight: 700; font-size: 18px;">AI anahtar kelimeleri gruplandırıyor ve niyet analizi yapıyor...</p>
                </div>

                <div id="ai-results" style="display: none;">
                    <div class="db-card" style="margin-bottom: 24px; border-left: 4px solid var(--primary);">
                        <p id="ai-summary" style="font-weight: 500; color: var(--gray-700);"></p>
                    </div>

                    <div class="db-card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid var(--gray-100);">
                                    <th style="padding: 12px; font-size: 14px; color: var(--gray-500);">Anahtar Kelime</th>
                                    <th style="padding: 12px; font-size: 14px; color: var(--gray-500);">Hacim</th>
                                    <th style="padding: 12px; font-size: 14px; color: var(--gray-500);">T. CPC</th>
                                    <th style="padding: 12px; font-size: 14px; color: var(--gray-500);">AI Intent</th>
                                    <th style="padding: 12px; font-size: 14px; color: var(--gray-500);">AI Önerisi</th>
                                </tr>
                            </thead>
                            <tbody id="kw-table-body">
                                <!-- Data injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- More tabs can be implemented similarly... -->
        </main>
    </div>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Fetch User Status
        fetch('api/get_profile.php')
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    const statusEl = document.getElementById('global-status');
                    statusEl.textContent = res.data.subscription_status === 'active' ? 'Account Actief' : 'Betaling Vereist';
                    statusEl.className = 'status-badge ' + (res.data.subscription_status === 'active' ? 'status-active' : 'status-pending');
                }
            });

        // Simple Chart Init
        const ctx = document.getElementById('performanceChart')?.getContext('2d');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Klikken',
                        data: [1200, 1900, 3000, 5000, 2400, 3500],
                        borderColor: '#2563eb',
                        tension: 0.4
                    }]
                }
            });
        }

        // AI Research Logic
        async function runAiResearch() {
            const keyword = document.getElementById('kw-input').value;
            if(!keyword) return alert('Lütfen bir kelime girin');

            const btn = document.getElementById('research-btn');
            const loading = document.getElementById('ai-loading');
            const results = document.getElementById('ai-results');
            const tableBody = document.getElementById('kw-table-body');

            btn.disabled = true;
            loading.style.display = 'block';
            results.style.display = 'none';

            try {
                const res = await fetch('api/keyword_research.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ keyword })
                });
                const data = await res.json();

                if(data.success) {
                    document.getElementById('ai-summary').textContent = data.summary;
                    tableBody.innerHTML = '';
                    
                    data.results.forEach(item => {
                        const row = `
                            <tr style="border-bottom: 1px solid var(--gray-100);">
                                <td style="padding: 16px 12px; font-weight: 600;">${item.keyword}</td>
                                <td style="padding: 16px 12px;">${item.volume}</td>
                                <td style="padding: 16px 12px;">${item.cpc}</td>
                                <td style="padding: 16px 12px;">
                                    <span class="status-badge" style="background: var(--blue-50); color: var(--primary);">${item.intent}</span>
                                </td>
                                <td style="padding: 16px 12px; font-size: 13px; color: var(--gray-600);">${item.ai_suggestion}</td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    results.style.display = 'block';
                }
            } catch (err) {
                alert('Analiz sırasında bir hata oluştu.');
            } finally {
                loading.style.display = 'none';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
