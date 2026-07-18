<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Contact Us';

$formError = '';
$formSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $company = clean($_POST['company'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $formError = 'Please fill in your name, email and message.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } else {
        $ins = $pdo->prepare("INSERT INTO inquiries (name, email, phone, company, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$name, $email, $phone, $company, $subject ?: 'General Inquiry', $message]);
        $formSuccess = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb-band">
    <div class="container"><a href="index.php">Home</a> / <span>Contact</span></div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Get In Touch</span>
            <h2>Talk to our engineering &amp; sales team</h2>
            <p>Send your requirement and we'll respond with specification guidance and a quotation.</p>
        </div>

        <div class="contact-grid">
            <div class="contact-info-card">
                <h3>Contact Details</h3>
                <div class="contact-info-item">
                    <div style="
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        background: linear-gradient(145deg, var(--lime), var(--forest));
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 1.1rem;
                        flex-shrink: 0;
                    ">📍</div>
                    <div><strong>Corporate Office</strong><span><?= clean(setting('corporate_address')) ?></span></div>
                </div>
                <div class="contact-info-item">
                    <div style="
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        background: linear-gradient(145deg, var(--lime), var(--forest));
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 1.1rem;
                        flex-shrink: 0;
                    ">🏢</div>
                    <div><strong>Registered Office</strong><span><?= clean(setting('registered_address')) ?></span></div>
                </div>
                <div class="contact-info-item">
                    <div style="
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        background: linear-gradient(145deg, var(--lime), var(--forest));
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 1.1rem;
                        flex-shrink: 0;
                    ">📞</div>
                    <div><strong>Cell &amp; WhatsApp</strong>
                        <a href="tel:<?= clean(setting('phone_1')) ?>"><?= clean(setting('phone_1')) ?></a>
                        <a href="tel:<?= clean(setting('phone_2')) ?>"><?= clean(setting('phone_2')) ?></a>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div style="
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        background: linear-gradient(145deg, var(--lime), var(--forest));
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 1.1rem;
                        flex-shrink: 0;
                    ">✉️</div>
                    <div><strong>Email</strong><a href="mailto:<?= clean(setting('email')) ?>"><?= clean(setting('email')) ?></a></div>
                </div>
                <div class="contact-info-item">
                    <div style="
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        background: linear-gradient(145deg, var(--lime), var(--forest));
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 1.1rem;
                        flex-shrink: 0;
                    ">🌐</div>
                    <div><strong>Website</strong><span><?= clean(setting('website')) ?></span></div>
                </div>
            </div>

            <div>
                <?php if ($formSuccess): ?>
                    <div class="alert alert-success">Thank you, <?= clean($_POST['name']) ?> — your message has been received. We'll get back to you shortly.</div>
                <?php endif; ?>
                <?php if ($formError): ?>
                    <div class="alert alert-error"><?= clean($formError) ?></div>
                <?php endif; ?>

                <form class="card-form" method="post">
                    <div class="form-row">
                        <div class="form-group"><label>Full Name *</label><input type="text" name="name" required value="<?= clean($_POST['name'] ?? '') ?>"></div>
                        <div class="form-group"><label>Email *</label><input type="email" name="email" required value="<?= clean($_POST['email'] ?? '') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= clean($_POST['phone'] ?? '') ?>"></div>
                        <div class="form-group"><label>Company</label><input type="text" name="company" value="<?= clean($_POST['company'] ?? '') ?>"></div>
                    </div>
                    <div class="form-group"><label>Subject</label><input type="text" name="subject" value="<?= clean($_POST['subject'] ?? '') ?>"></div>
                    <div class="form-group"><label>Message *</label><textarea name="message" rows="5" required><?= clean($_POST['message'] ?? '') ?></textarea></div>
                    <button type="submit" name="contact_submit" class="btn btn-primary" style="width:100%; justify-content:center;">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt" style="padding-top:0;">
    <div class="container">
        <!-- Map updated with exact latitude and longitude -->
        <iframe src="https://www.google.com/maps?q=23.719927,90.420172&z=15&output=embed" width="100%" height="360" style="border:0; border-radius:14px;" loading="lazy"></iframe>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>