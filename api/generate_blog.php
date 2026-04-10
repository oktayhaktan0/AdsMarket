<?php
/**
 * AI SEO Blog Generation API (Mock Version)
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

$ai = new AiEngine(
    defined('OPENAI_API_KEY') ? OPENAI_API_KEY : null,
    defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : null
);

$input = json_decode(file_get_contents('php://input'), true);
$topic = $input['topic'] ?? '';
$keywords = $input['keywords'] ?? '';

if (empty($topic)) {
    echo json_encode(['success' => false, 'message' => 'Lütfen bir konu başlığı girin.']);
    exit;
}

// SIMULATED AI LATENCY (Generating content takes longer)
usleep(4000000); // 4 seconds delay

/**
 * MOCK CONTENT GENERATION
 */
$title = "Hoe optimizeer je '" . $topic . "' voor Google Ads?";
$content = "<h2>Introductie</h2>
<p>In de wereld van digitale marketing is <strong>" . $topic . "</strong> een cruciaal onderdeel geworden van elke succesvolle strategie. Of u nu een kleine ondernemer bent of een groot marketingbureau beheert, het begrijpen van de nuances van dit onderwerp kan het verschil maken tussen een gemiddelde campagne en een ROI-knaller.</p>

<h3>Waarom is " . $topic . " belangrijk?</h3>
<p>Het algoritme van Google verandert voortdurend, maar de focus op relevantie en gebruikersintentie blijft hetzelfde. Bij het werken met " . ($keywords ?: $topic) . " is het essentieel om te kijken naar wat de gebruiker echt zoekt.</p>

<ul>
    <li><strong>Snelheid:</strong> Uw bestemmingspagina moet razendsnel laden.</li>
    <li><strong>Relevantie:</strong> De advertentie moet direct aansluiten bij de zoekopdracht.</li>
    <li><strong>Gebruikerservaring:</strong> Een duidelijke call-to-action (CTA) is onmisbaar.</li>
</ul>

<h3>Conclusie</h3>
<p>Door AI-gestuurde tools zoals AdsMarket.nl te gebruiken, kunt u het proces van keyword research en contentcreatie automatiseren, waardoor u meer tijd overhoudt voor strategie.</p>";

echo json_encode([
    'success' => true,
    'title' => $title,
    'content' => $content,
    'meta' => [
        'title' => $title . " | AdsMarket Blog",
        'description' => "Ontdek alles over " . $topic . " en hoe u dit kunt inzetten voor uw online groei.",
        'reading_time' => '4 min'
    ]
]);
    
