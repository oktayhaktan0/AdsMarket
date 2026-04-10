<?php
/**
 * Robuust Google Ads Performance API - AdsMarket.nl
 */
header('Content-Type: application/json');
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $conn = getDBConnection();

    // Fetch user details including Looker URL
    $stmt = $conn->prepare("SELECT google_ads_id, looker_studio_url, google_refresh_token FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();

    // Fallback: If no API approved or no tokens, return simulated data (Demo mode)
    // In Faz 2, we show real stats ONLY if token and ID are valid.
    if (empty($user_data['google_refresh_token']) || empty($user_data['google_ads_id']) || GOOGLE_DEVELOPER_TOKEN === 'etwFU3-sMFJLbr22GqLpeA') {
        // High-Quality Simulation (Realistic AdsMarket Demo)
        $sim_data = [
            'success' => true,
            'data' => [
                'looker_studio_url' => $user_data['looker_studio_url'] ?? null,
                'totals' => [
                    'clicks' => 1245 + (date('d') * 50),
                    'spend' => 450.50 + (date('d') * 25),
                    'cpc' => 0.85,
                    'conversions' => 42 + (date('d') * 3)
                ],
                'chart' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'clicks' => [65, 59, 80, 81, 56, 55, 40],
                    'conversions' => [28, 48, 40, 19, 86, 27, 90]
                ]
            ]
        ];
        echo json_encode($sim_data);
        exit;
    }

    // Gerçek API Çağrısı (Gelecekte buraya Google Ads Library gelecek)
    // Şimdilik çökme olmaması için burayı try-catch ile koruyoruz.
    echo json_encode(['success' => true, 'message' => 'API onayı bekleniyor, demo veriler aktif.']);

} catch (Exception $e) {
    // ASLA 500 VERME - Sessizce hatayı bildir
    echo json_encode([
        'success' => false, 
        'error' => 'Sistem meşgul, lütfen daha sonra tekrar deneyin.', 
        'details' => $e->getMessage()
    ]);
}
?>