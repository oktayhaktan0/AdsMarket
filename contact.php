<?php
$page_title = 'Contact - AdsMarket.nl | Vraag een Google Ads Audit aan';
$page_description = 'Neem contact op met AdsMarket en Kasiyo BV. Wij helpen u met uw Google Ads groeistrategie.';
include 'includes/header.php';
?>

    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <span class="section-badge">Contact</span>
                <h1 class="page-title">Laten we <span class="gradient-text">praten</span></h1>
                <p class="page-description">
                    Vraag een gratis audit aan of stel een vraag over onze geavanceerde Google Ads API-gestuurde oplossingen.
                </p>
            </div>
        </div>
    </section>

    <section class="contact-section" style="padding: 100px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 60px;">
                <div class="contact-info">
                    <h2 style="font-size: 32px; margin-bottom: 30px;">Zakelijke <span class="gradient-text">Communicatie</span></h2>
                    <p style="color: var(--gray-600); margin-bottom: 40px; font-size: 17px;">
                        Als uw door Google Ads API goedgekeurde partner geven we prioriteit aan efficiëntie.
                    </p>
                    
                    <div style="margin-bottom: 40px;">
                        <h4 style="font-size: 18px; margin-bottom: 10px; color: var(--gray-900);">Bezoek ons</h4>
                        <p style="color: var(--gray-600); font-size: 16px;">
                            <strong>🏢 Kasiyo BV</strong><br>
                            Cruquiuskade 251<br>
                            1018 AM Amsterdam, Nederland
                        </p>
                    </div>

                    <div style="margin-bottom: 40px;">
                        <h4 style="font-size: 18px; margin-bottom: 10px; color: var(--gray-900);">E-mail</h4>
                        <p style="color: var(--gray-600); font-size: 16px;">
                            info@adsmarket.nl<br>
                            support@adsmarket.nl
                        </p>
                    </div>

                    <div>
                        <h4 style="font-size: 18px; margin-bottom: 10px; color: var(--gray-900);">Telefoon</h4>
                        <p style="color: var(--gray-600); font-size: 16px;">
                            +31 20 123 4567
                        </p>
                    </div>
                </div>

                <div class="contact-form-container" style="background: white; padding: 40px; border-radius: 20px; box-shadow: var(--shadow-xl);">
                    <h3 style="font-size: 24px; margin-bottom: 30px;">Audit aanvragen</h3>
                    <form action="api/send_contact.php" method="POST" style="display: grid; gap: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 500;">Naam</label>
                                <input type="text" id="name" name="name" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                            </div>
                            <div>
                                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                                <input type="email" id="email" name="email" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                            </div>
                        </div>
                        <div>
                            <label for="company" style="display: block; margin-bottom: 8px; font-weight: 500;">Bedrijf</label>
                            <input type="text" id="company" name="company" style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;">
                        </div>
                        <div>
                            <label for="message" style="display: block; margin-bottom: 8px; font-weight: 500;">Boodschap</label>
                            <textarea id="message" name="message" rows="5" required style="width: 100%; padding: 12px 16px; border: 1px solid var(--gray-200); border-radius: 8px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 18px; padding: 15px;">Verzenden →</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
