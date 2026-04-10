<?php
$page_title = 'Aanmelden - AdsMarket.nl | Start met Slimme Google Ads';
$page_description = 'Meld u aan voor AdsMarket en krijg toegang tot onze geautomatiseerde Google Ads API-tools.';
include 'includes/header.php';

// Get plan from URL
$plan = $_GET['plan'] ?? 'starter';
?>

<section class="auth-section" style="padding: 140px 0 100px; background: var(--gray-50);">
    <div class="container" style="max-width: 500px;">
        <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: var(--shadow-xl); border: 1px solid var(--gray-100);">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 10px;">Start Vandaag</h1>
                <p style="color: var(--gray-600);">Word een Google Ads API-geautoriseerde partner</p>
            </div>
            
            <form id="registerForm" style="display: grid; gap: 20px;">
                <input type="hidden" name="plan" value="<?php echo htmlspecialchars($plan); ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="first_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Voornaam</label>
                        <input type="text" id="first_name" name="first_name" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                    </div>
                    <div>
                        <label for="last_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Achternaam</label>
                        <input type="text" id="last_name" name="last_name" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                    </div>
                </div>
                <div>
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500;">E-mail (Zakelijk)</label>
                    <input type="email" id="email" name="email" required placeholder="naam@bedrijf.nl" style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                </div>
                <div>
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">Wachtwoord (Min. 8 tekens)</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                </div>
                <div>
                    <label for="company_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Bedrijfsnaam</label>
                    <input type="text" id="company_name" name="company_name" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                </div>
                <div>
                    <label for="google_ads_id" style="display: block; margin-bottom: 8px; font-weight: 500;">Google Ads CID (Optioneel)</label>
                    <input type="text" id="google_ads_id" name="google_ads_id" placeholder="XXX-XXX-XXXX" style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px; margin-top: 10px;">Account Aanvragen</button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; font-size: 14px; color: var(--gray-600);">
                Heeft u al een account? <a href="login.php" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">Log hier in</a>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    btn.disabled = true;
    btn.textContent = 'Verwerken...';

    const formData = new FormData(this);
    
    fetch('api/register.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.location.href = res.redirect || 'dashboard.php';
        } else {
            alert(res.message);
            btn.disabled = false;
            btn.textContent = 'Account Aanvragen';
        }
    })
    .catch(err => {
        alert('Er is een fout opgetreden.');
        btn.disabled = false;
        btn.textContent = 'Account Aanvragen';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
