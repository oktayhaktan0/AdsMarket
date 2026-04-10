<?php
$page_title = 'Login - AdsMarket.nl | Toegang tot uw ROI Dashboard';
$page_description = 'Log in op uw AdsMarket account en bekijk uw Google Ads prestaties real-time.';
include 'includes/header.php';
?>

<section class="auth-section" style="padding: 140px 0 100px; background: var(--gray-50);">
    <div class="container" style="max-width: 450px;">
        <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: var(--shadow-xl); border: 1px solid var(--gray-100);">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 10px;">Welkom terug</h1>
                <p style="color: var(--gray-600);">Log in op uw Google Ads API dashboard</p>
            </div>
            
            <form id="loginForm" style="display: grid; gap: 20px;">
                <div>
                    <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                    <input type="email" id="email" name="email" required placeholder="naam@bedrijf.nl" style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                </div>
                <div>
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">Wachtwoord</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
                    <label style="display: flex; align-items: center; gap: 8px;"><input type="checkbox"> Onthoud mij</label>
                    <a href="forgot-password.php" style="color: var(--primary-color); text-decoration: none;">Wachtwoord vergeten?</a>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;">Inloggen</button>
            </form>
            
            <div style="text-align: center; margin-top: 30px; font-size: 14px; color: var(--gray-600);">
                Nog geen account? <a href="signup.php" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">Vraag toegang aan</a>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    btn.disabled = true;
    btn.textContent = 'Controleren...';

    const formData = new FormData(this);
    
    fetch('api/login.php', {
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
            btn.textContent = 'Inloggen';
        }
    })
    .catch(err => {
        alert('Ongeldige inloggegevens.');
        btn.disabled = false;
        btn.textContent = 'Inloggen';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
