<?php
/**
 * AI Keyword Research & Intent Analysis API (Mock Version)
 * AdsMarket.nl
 */
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

// Get raw POST data
$input = json_decode(file_get_contents('php://input'), true);
$seed_keyword = $input['keyword'] ?? '';

if (empty($seed_keyword)) {
    echo json_encode(['success' => false, 'message' => 'Lütfen bir anahtar kelime girin.']);
    exit;
}

// SIMULATED AI LATENCY (Gives the "WOW" effect of processing)
usleep(2500000); // 2.5 seconds delay

/**
 * MOCK DATA GENERATION
 * In production, this will call OpenAI / Google Ads API.
 */
$mock_results = [
    [
        'keyword' => $seed_keyword,
        'volume' => rand(1000, 5000),
        'cpc' => '€' . number_format(lcg_value() * 2 + 0.5, 2),
        'intent' => 'Transactional',
        'difficulty' => 'Medium',
        'ai_suggestion' => 'Direct search ads targeting "buy now" intent.'
    ],
    [
        'keyword' => 'beste ' . $seed_keyword,
        'volume' => rand(500, 2000),
        'cpc' => '€' . number_format(lcg_value() * 1.5 + 0.3, 2),
        'intent' => 'Commercial',
        'difficulty' => 'Low',
        'ai_suggestion' => 'Comparison landing page with USP highlights.'
    ],
    [
        'keyword' => $seed_keyword . ' kopen',
        'volume' => rand(800, 3000),
        'cpc' => '€' . number_format(lcg_value() * 2.5 + 1.0, 2),
        'intent' => 'Transactional',
        'difficulty' => 'High',
        'ai_suggestion' => 'Product-specific shopping ads.'
    ],
    [
        'keyword' => 'hoe ' . $seed_keyword . ' werken',
        'volume' => rand(200, 1000),
        'cpc' => '€' . number_format(lcg_value() * 0.5 + 0.1, 2),
        'intent' => 'Informational',
        'difficulty' => 'Low',
        'ai_suggestion' => 'Informative blog post with internal link to product.'
    ]
];

echo json_encode([
    'success' => true,
    'keyword' => $seed_keyword,
    'results' => $mock_results,
    'summary' => "AI Analizi Tamamlandı: '$seed_keyword' için yüksek dönüşüm potansiyeli tespit edildi. Özellikle 'Transactional' niyetli kelimelere odaklanılması önerilir."
]);
    
