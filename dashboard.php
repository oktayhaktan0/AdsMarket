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
                <li><a onclick="switchTab('content')" class="tab-link"><span>✍️</span> AI Content</a></li>
                <li><a onclick="switchTab('roi')" class="tab-link"><span>📊</span> ROI Estimator</a></li>
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

            <!-- TAB: AI CONTENT GENERATOR -->
            <div id="content" class="tab-content">
                <div class="db-card" style="margin-bottom: 24px;">
                    <h2 class="auth-title">AI <span class="gradient-text">SEO Content Creator</span></h2>
                    <p class="auth-subtitle">Genereer binnen enkele seconden SEO-geoptimaliseerde blogposts ve advertentieteksten.</p>
                    
                    <div style="margin-top: 24px; display: grid; gap: 20px;">
                        <div>
                            <label style="display:block; font-size:14px; margin-bottom:8px; font-weight:600;">Onderwerp / Titel</label>
                            <input type="text" id="blog-topic" placeholder="Bv: 'De toekomst van Google Ads'" 
                                   style="width: 100%; padding: 14px 20px; border-radius: 12px; border: 1px solid var(--gray-200);">
                        </div>
                        <div>
                            <label style="display:block; font-size:14px; margin-bottom:8px; font-weight:600;">Focus Keywords (Optioneel)</label>
                            <input type="text" id="blog-keywords" placeholder="Bv: marketing, automation, cpc" 
                                   style="width: 100%; padding: 14px 20px; border-radius: 12px; border: 1px solid var(--gray-200);">
                        </div>
                        <button onclick="runAiBlogGen()" id="blog-btn" class="btn btn-primary" style="padding: 16px;">
                            Genereer Artikel met Claude 3.5
                        </button>
                    </div>
                </div>

                <div id="blog-loading" style="display: none; text-align: center; padding: 40px;">
                    <div class="spinner" style="margin: 0 auto 20px;"></div>
                    <p class="gradient-text" style="font-weight: 700;">AI schrijft uw artikel... Dit duurt ongeveer 5-10 seconden.</p>
                </div>

                <div id="blog-results" style="display: none;">
                    <div class="grid-cards">
                        <div class="db-card" style="grid-column: span 2;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                                <h3 id="res-blog-title">Artikel Voorbeeld</h3>
                                <button class="btn btn-secondary btn-small" onclick="copyBlog()">Kopieer Tekst</button>
                            </div>
                            <div id="res-blog-content" style="background:#f8fafc; padding:30px; border-radius:12px; border:1px solid var(--gray-200); line-height:1.8;">
                                <!-- Content injected here -->
                            </div>
                        </div>
                        <div class="db-card">
                            <h3>SEO Meta Data</h3>
                            <div style="margin-top:20px;">
                                <label style="font-size:12px; color:var(--gray-500);">Meta Title</label>
                                <p id="res-meta-title" style="font-size:14px; font-weight:600; margin-bottom:15px;"></p>
                                
                                <label style="font-size:12px; color:var(--gray-500);">Meta Description</label>
                                <p id="res-meta-desc" style="font-size:14px; line-height:1.4;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: ROI ESTIMATOR -->
            <div id="roi" class="tab-content">
                <div class="grid-cards">
                    <div class="db-card">
                        <h2 class="auth-title">ROI <span class="gradient-text">Projection Tool</span></h2>
                        <p class="auth-subtitle">Bereken uw verwachte winst en schaal uw campagnes met AI data.</p>
                        
                        <div style="margin-top:24px; display:grid; gap:20px;">
                            <div>
                                <label style="display:block; font-size:12px; margin-bottom:8px; font-weight:600;">Maandelijks Budget (€)</label>
                                <input type="number" id="roi-budget" value="1000" style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--gray-200);">
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; margin-bottom:8px; font-weight:600;">Sektor</label>
                                <select id="roi-industry" style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--gray-200);">
                                    <option value="E-commerce">E-commerce</option>
                                    <option value="B2B SaaS">B2B SaaS</option>
                                    <option value="Real Estate">Real Estate</option>
                                    <option value="Legal">Legal</option>
                                    <option value="General">Algemeen</option>
                                </select>
                            </div>
                            <button onclick="runRoiEstimate()" id="roi-btn" class="btn btn-primary">Bereken ROI Projeksiyonu</button>
                        </div>
                    </div>

                    <div class="db-card">
                        <h3>AI <span class="gradient-text">Performance İnsight</span></h3>
                        <div id="roi-loading" style="display:none; padding:20px; text-align:center;">
                            <div class="spinner" style="margin:0 auto 15px;"></div>
                            <p style="font-size:14px; color:var(--gray-500);">AI piyasa verilerini analiz ediyor...</p>
                        </div>
                        <div id="roi-placeholder" style="padding:40px; text-align:center; color:var(--gray-400);">
                            <p>Analyseer uw bütçe om inzichten te krijgen.</p>
                        </div>
                        <div id="roi-insight-box" style="display:none;">
                            <div style="background:var(--gray-50); padding:15px; border-radius:12px; border-left:4px solid var(--primary); margin-bottom:15px;">
                                <p id="roi-insight-text" style="font-size:14px; color:var(--gray-700);"></p>
                            </div>
                            <div class="grid-cards" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div style="background:var(--white); padding:15px; border-radius:12px; border:1px solid var(--gray-100);">
                                    <span style="font-size:10px; color:var(--gray-400);">HEDEF ROI</span>
                                    <h4 id="res-roi-val" class="gradient-text" style="font-size:20px; margin:0;"></h4>
                                </div>
                                <div style="background:var(--white); padding:15px; border-radius:12px; border:1px solid var(--gray-100);">
                                    <span style="font-size:10px; color:var(--gray-400);">EST. REVENUE</span>
                                    <h4 id="res-roi-rev" style="font-size:20px; margin:0; color:var(--gray-900);"></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="roi-chart-card" class="db-card" style="margin-top:24px; display:none;">
                    <h3>6 Maanden <span class="gradient-text">Groei Projeksiyonu</span></h3>
                    <div style="height:350px; margin-top:20px;">
                        <canvas id="roiChart"></canvas>
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

        // AI Blog Generation Logic
        async function runAiBlogGen() {
            const topic = document.getElementById('blog-topic').value;
            const keywords = document.getElementById('blog-keywords').value;
            if(!topic) return alert('Lütfen bir konu girin');

            const btn = document.getElementById('blog-btn');
            const loading = document.getElementById('blog-loading');
            const results = document.getElementById('blog-results');

            btn.disabled = true;
            loading.style.display = 'block';
            results.style.display = 'none';

            try {
                const res = await fetch('api/generate_blog.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ topic, keywords })
                });
                const data = await res.json();

                if(data.success) {
                    document.getElementById('res-blog-title').textContent = data.title;
                    document.getElementById('res-blog-content').innerHTML = data.content;
                    document.getElementById('res-meta-title').textContent = data.meta.title;
                    document.getElementById('res-meta-desc').textContent = data.meta.description;
                    results.style.display = 'block';
                }
            } catch (err) {
                alert('İçerik üretilirken bir hata oluştu.');
            } finally {
                loading.style.display = 'none';
                btn.disabled = false;
            }
        }

        function copyBlog() {
            const content = document.getElementById('res-blog-content').innerText;
            navigator.clipboard.writeText(content);
            alert('Tekst gekopieerd naar klerbord!');
        }

        // ROI Estimation Logic
        let myRoiChart = null;

        async function runRoiEstimate() {
            const budget = document.getElementById('roi-budget').value;
            const industry = document.getElementById('roi-industry').value;
            
            const btn = document.getElementById('roi-btn');
            const loading = document.getElementById('roi-loading');
            const placeholder = document.getElementById('roi-placeholder');
            const insightBox = document.getElementById('roi-insight-box');
            const chartCard = document.getElementById('roi-chart-card');

            btn.disabled = true;
            loading.style.display = 'block';
            placeholder.style.display = 'none';
            insightBox.style.display = 'none';
            chartCard.style.display = 'none';

            try {
                const res = await fetch('api/roi_estimator.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ budget, industry })
                });
                const data = await res.json();

                if(data.success) {
                    document.getElementById('roi-insight-text').textContent = data.ai_insight;
                    document.getElementById('res-roi-val').textContent = data.projection.roi;
                    document.getElementById('res-roi-rev').textContent = data.projection.revenue;
                    
                    insightBox.style.display = 'block';
                    chartCard.style.display = 'block';

                    // Update Chart
                    if(myRoiChart) myRoiChart.destroy();
                    const ctx = document.getElementById('roiChart').getContext('2d');
                    myRoiChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.chart.labels,
                            datasets: [{
                                label: 'Est. Maandelijkse Omzet (€)',
                                data: data.chart.values,
                                backgroundColor: '#2563eb',
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            } catch (err) {
                alert('ROI analizi sırasında bir hata oluştu.');
            } finally {
                loading.style.display = 'none';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
