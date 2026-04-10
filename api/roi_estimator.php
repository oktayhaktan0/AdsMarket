<?php
/**
 * AI ROI Projection & Performance Estimator API (Mock Version)
 * AdsMarket.nl
 */
header('Content-Type: application/json');
require_once 'config.php';
require_once 'lib/AiEngine.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$budget = $input['budget'] ?? 1000;
$industry = $input['industry'] ?? 'General';
$target_cpc = $input['cpc'] ?? 0.85;

// SIMULATED AI LATENCY
usleep(3000000); // 3 seconds delay for "Complex Calculation" effect

/**
 * ROI CALCULATION LOGIC (Simulated AI Analysis)
 * Industry specific conversion rates
 */
$rates = [
    'E-commerce' => 0.035, // 3.5%
    'B2B SaaS' => 0.02,   // 2%
    'Real Estate' => 0.015, // 1.5%
    'Legal' => 0.025,     // 2.5%
    'General' => 0.022    // 2.2%
];

$conv_rate = $rates[$industry] ?? 0.022;
$est_clicks = floor($budget / $target_cpc);
$est_conversions = floor($est_clicks * $conv_rate);
$avg_order_value = 150; // Manual assumption for ROI
$est_revenue = $est_conversions * $avg_order_value;
$roi = (($est_revenue - $budget) / $budget) * 100;

// Monthly projection data for chart
$chart_data = [];
$labels = [];
for ($i = 1; $i <= 6; $i++) {
    $labels[] = "Maand $i";
    $chart_data[] = floor($est_revenue * (1 + ($i * 0.15))); // 15% growth monthly
}

echo json_encode([
    'success' => true,
    'projection' => [
        'budget' => $budget,
        'industry' => $industry,
        'clicks' => $est_clicks,
        'conversions' => $est_conversions,
        'revenue' => '€' . number_format($est_revenue, 2),
        'roi' => number_format($roi, 1) . '%',
        'break_even' => ceil($budget / $avg_order_value) . ' sales'
    ],
    'chart' => [
        'labels' => $labels,
        'values' => $chart_data
    ],
    'ai_insight' => "AI Analizi: $industry sektörü için €$target_cpc CPC değeri rekabetçi görünüyor. Mevcut bütçe ile 6 ay içinde %" . number_format($roi + 50, 1) . " kârlılığa ulaşmanız öngörülüyor."
]);
    
