<?php
/**
 * AI Google Ads Campaign Structure Generator (Mock Version)
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
$keyword = $input['keyword'] ?? 'Google Ads';
$usp = $input['usp'] ?? 'Gratis Audit, 24/7 Support';

// SIMULATED AI LATENCY
usleep(3500000); 

/**
 * MOCK AD STRUCTURE GENERATION
 */
$headlines = [
    "Ontdek " . $keyword,
    "Bespaar op " . $keyword,
    "Beste " . $keyword . " in NL",
    "Partner van AdsMarket",
    "Gecertificeerde Experts",
    "Krijg Meer Conversies",
    "Lage CPC, Hoge ROI",
    $usp,
    "Start Vandaag Nog",
    "Vraag een Offerte Aan",
    "Al 500+ Tevreden Klanten",
    "Verbeter UW Resultaten",
    "Professionele Aanpak",
    "Maximaliseer UW Groei",
    "Slimme AI Strategie"
];

$descriptions = [
    "Wilt u meer resultaat uit uw " . $keyword . " campagnes? Onze AI-gestuurde strategieën helpen u sneller te groeien.",
    "Specialist in " . $keyword . ". Wij optimaliseren uw advertenties voor een lagere CPC en een hogere conversieratio.",
    "Krijg direct inzicht in uw marktpotentieel met AdsMarket.nl. Onze experts staan 24/7 voor u klaar.",
    "Profiteer van onze jarenlange ervaring met " . $keyword . ". Start vandaag uw gratis audit en zie het verschil."
];

echo json_encode([
    'success' => true,
    'ad_group' => "AG_" . str_replace(' ', '_', strtoupper($keyword)),
    'headlines' => $headlines,
    'descriptions' => $descriptions,
    'ad_strength' => 'Excellent',
    'ai_rationale' => "AI Analizi: Bu başlıklar hem 'Emotional' hem de 'Direct Response' odaklı oluşturulmuştur. Google'ın rotasyon algoritması için maksimum çeşitlilik sağlanmıştır."
]);
    
