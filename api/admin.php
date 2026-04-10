<?php
/**
 * AdsMarket Admin Control Center - Faz 1 & 2 Management
 */
require_once 'config.php';
session_start();

// Admin Security
$conn = getDBConnection();
$user_id = $_SESSION['user_id'] ?? 0;
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$role_res = $stmt->get_result()->fetch_assoc();

if (!$role_res || $role_res['role'] !== 'admin') {
    die("<h1 style='text-align:center; margin-top:100px; font-family:sans-serif;'>🛑 Yetkisiz Erişim - Bu bölüm sadece AdsMarket Adminleri içindir.</h1>");
}

// Fetch Stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$active_subs = $conn->query("SELECT COUNT(*) as count FROM users WHERE subscription_status = 'active'")->fetch_assoc()['count'];
$open_tickets = $conn->query("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'open'")->fetch_assoc()['count'];

// Fetch Users
$users = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>AdsMarket | Admin Kontrol Merkezi</title>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; color: #1e293b; padding: 20px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid #2563eb; }
        .stat-card h3 { margin: 0; font-size: 14px; color: #64748b; }
        .stat-card p { margin: 10px 0 0; font-size: 24px; font-weight: 800; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        th { background: #f1f5f9; padding: 15px; text-align: left; font-size: 13px; color: #64748b; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .active { background: #dcfce7; color: #166534; }
        .past_due { background: #fee2e2; color: #991b1b; }
        .none { background: #f1f5f9; color: #64748b; }
    </style>
</head>
<body>
    <h1>AdsMarket Admin Paneli 🚀</h1>

    <div class="grid">
        <div class="stat-card">
            <h3>Toplam Müşteri</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="stat-card">
            <h3>Aktif Abonelik</h3>
            <p><?php echo $active_subs; ?></p>
        </div>
        <div class="stat-card">
            <h3>Bekleyen Destek Talebi</h3>
            <p><?php echo $open_tickets; ?></p>
        </div>
    </div>

    <h2>Müşteri Listesi</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Müşteri / Şirket</th>
                <th>E-posta / Website</th>
                <th>Google Ads ID</th>
                <th>Paket</th>
                <th>Durum</th>
                <th>Kayıt Tarihi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td>
                    <strong><?php echo $u['full_name']; ?></strong><br>
                    <small><?php echo $u['company_name']; ?></small>
                </td>
                <td>
                    <?php echo $u['email']; ?><br>
                    <a href="<?php echo $u['website']; ?>" target="_blank" style="color:#2563eb; font-size:12px;"><?php echo $u['website']; ?></a>
                </td>
                <td><code><?php echo $u['google_ads_id'] ?: 'Girilmedi'; ?></code></td>
                <td><span style="text-transform:capitalize;"><?php echo $u['plan']; ?></span></td>
                <td>
                    <span class="status <?php echo $u['subscription_status']; ?>">
                        <?php echo $u['subscription_status']; ?>
                    </span>
                </td>
                <td><?php echo date('d.m.Y', strtotime($u['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
