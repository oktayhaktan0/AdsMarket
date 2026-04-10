<?php
/**
 * printable Invoice View - AdsMarket.nl
 */
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die("Yetkisiz erişim veya geçersiz fatura.");
}

$invoice_num = $_GET['id'];
$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Fetch invoice and user details
$sql = "SELECT i.*, u.full_name, u.company_name, u.email, u.website 
        FROM invoices i 
        JOIN users u ON i.user_id = u.id 
        WHERE i.invoice_num = ? AND i.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $invoice_num, $user_id);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

if (!$invoice) {
    die("Fatura bulunamadı.");
}

$amount = $invoice['amount'];
$vat = $amount * 0.21; // %21 BTW (Netherlands)
$total = $amount + $vat;

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur <?php echo $invoice_num; ?> - AdsMarket.nl</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.6; padding: 40px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); border-radius: 10px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: 800; }
        .logo span { color: #2563eb; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .info-col h3 { font-size: 14px; color: #667085; text-transform: uppercase; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #f8fafc; text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; }
        table td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
        .totals { float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; }
        .total-row.grand { font-weight: 800; font-size: 18px; color: #2563eb; border-top: 2px solid #2563eb; margin-top: 10px; }
        .footer { margin-top: 100px; text-align: center; font-size: 12px; color: #98a2b3; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px auto; text-align: right;">
        <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer;">🖨 Yazdır / PDF Kaydet</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">Ads<span>Market</span></div>
            <div>
                <h2 style="margin:0;">FACTUUR</h2>
                <p style="margin:0; color:#667085;">#<?php echo $invoice_num; ?></p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-col">
                <h3>Van:</h3>
                <strong>AdsMarket B.V.</strong><br>
                Amsterdam, Nederland<br>
                info@adsmarket.nl<br>
                BTW: NL888888888B01
            </div>
            <div class="info-col" style="text-align: right;">
                <h3>Voor:</h3>
                <strong><?php echo $invoice['company_name']; ?></strong><br>
                Attr: <?php echo $invoice['full_name']; ?><br>
                <?php echo $invoice['email']; ?><br>
                <?php echo $invoice['website']; ?>
            </div>
        </div>

        <div class="info-section" style="margin-bottom: 20px; background: #f8fafc; padding: 15px; border-radius: 8px;">
            <div>
                <strong>Factuurdatum:</strong> <?php echo date('d-m-Y', strtotime($invoice['created_at'])); ?>
            </div>
            <div>
                <strong>Status:</strong> <span style="color: #10b981; font-weight: 700;">BETAALD</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Omschrijving</th>
                    <th style="text-align: right;">Bedrag</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>AdsMarket Subscription (Maandelijks)</td>
                    <td style="text-align: right;">€<?php echo number_format($amount, 2, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotaal:</span>
                <span>€<?php echo number_format($amount, 2, ',', '.'); ?></span>
            </div>
            <div class="total-row">
                <span>BTW (21%):</span>
                <span>€<?php echo number_format($vat, 2, ',', '.'); ?></span>
            </div>
            <div class="total-row grand">
                <span>Totaal:</span>
                <span>€<?php echo number_format($total, 2, ',', '.'); ?></span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <div class="footer">
            <p>Bedankt voor uw vertrouwen in AdsMarket.nl</p>
            <p>AdsMarket B.V. | KVK: 12345678 | https://adsmarket.nl</p>
        </div>
    </div>
</body>
</html>
