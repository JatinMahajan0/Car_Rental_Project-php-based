<?php
require '../config.php';
require '../includes/mailer.php';
csrfVerify();

$email = trim(strtolower($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('forgot_error', 'Please enter a valid email address.', 'error');
    redirect('/car-rental/forgot_password.php');
}

$stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Always redirect to sent page (security: don't reveal if email exists)
if ($user) {
    $token = bin2hex(random_bytes(32));

    // Ensure table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token (token),
        INDEX idx_email (email)
    )");

    // Delete old tokens for this email, insert new one
    // Use MySQL NOW() + INTERVAL for expiry to avoid PHP/MySQL timezone mismatch
    $pdo->prepare("DELETE FROM password_resets WHERE email=?")->execute([$email]);
    $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, NOW() + INTERVAL 1 HOUR)")
        ->execute([$email, $token]);


    // Build the reset URL dynamically — works on any port (80, 8080, etc.)
    $scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST']; // includes port if non-standard, e.g. localhost:8080
    $resetLink = "$scheme://$host/car-rental/reset_password.php?token=" . urlencode($token);
    $userName  = htmlspecialchars($user['name']);

    // HTML email body
    $body = "
    <!DOCTYPE html>
    <html>
    <body style=\"margin:0;padding:0;background:#030810;font-family:Inter,Arial,sans-serif;\">
      <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#030810;padding:40px 0;\">
        <tr><td align=\"center\">
          <table width=\"520\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#0d1b2a;border-radius:16px;border:1px solid rgba(255,255,255,0.08);overflow:hidden;\">

            <!-- Header -->
            <tr>
              <td style=\"background:linear-gradient(135deg,#030810 0%,#0d1b2a 100%);padding:32px 40px;border-bottom:1px solid rgba(0,229,255,0.15);text-align:center;\">
                <div style=\"display:inline-flex;align-items:center;gap:10px;\">
                  <div style=\"background:rgba(0,229,255,0.15);border-radius:8px;width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;\">
                    🚗
                  </div>
                  <span style=\"color:#ffffff;font-size:20px;font-weight:900;letter-spacing:-0.5px;\">Velocity <span style=\"color:#00E5FF;\">Elite</span></span>
                </div>
              </td>
            </tr>

            <!-- Body -->
            <tr>
              <td style=\"padding:40px;\">
                <div style=\"text-align:center;margin-bottom:28px;\">
                  <div style=\"width:64px;height:64px;background:rgba(0,229,255,0.1);border:1px solid rgba(0,229,255,0.3);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:28px;\">🔐</div>
                </div>
                <h1 style=\"color:#ffffff;font-size:22px;font-weight:900;text-align:center;margin:0 0 8px;\">Password Reset Request</h1>
                <p style=\"color:#64748b;font-size:14px;text-align:center;margin:0 0 32px;\">Hi <strong style=\"color:#e2e8f0;\">$userName</strong>, we received a request to reset your password.</p>

                <div style=\"background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:24px;margin-bottom:28px;text-align:center;\">
                  <p style=\"color:#94a3b8;font-size:13px;margin:0 0 16px;\">Click the button below to reset your password. This link expires in <strong style=\"color:#fbbf24;\">1 hour</strong>.</p>
                  <a href=\"$resetLink\"
                     style=\"display:inline-block;background:#00E5FF;color:#000000;font-weight:800;font-size:14px;text-decoration:none;padding:14px 32px;border-radius:10px;letter-spacing:0.5px;\">
                    RESET MY PASSWORD &rarr;
                  </a>
                  <p style=\"color:#475569;font-size:11px;margin:16px 0 0;word-break:break-all;\">
                    Or copy this link into your browser:<br>
                    <a href=\"$resetLink\" style=\"color:#00E5FF;font-size:11px;\">$resetLink</a>
                  </p>
                </div>

                <p style=\"color:#475569;font-size:12px;text-align:center;margin:0;\">
                  If you didn't request a password reset, you can safely ignore this email.<br>
                  Your password will not be changed.
                </p>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td style=\"background:#030810;padding:20px 40px;border-top:1px solid rgba(255,255,255,0.06);text-align:center;\">
                <p style=\"color:#334155;font-size:11px;margin:0;\">© " . date('Y') . " Velocity Elite. All rights reserved.</p>
              </td>
            </tr>
          </table>
        </td></tr>
      </table>
    </body>
    </html>
    ";

    $sent = sendMail($email, $user['name'], 'Reset Your Velocity Elite Password', $body);

    if (!$sent) {
        // Fallback: save to notification so reset still works even if email fails
        notify($pdo, $user['id'],
            "Password reset requested. <a href='$resetLink' style='color:#00E5FF;'>Click here to reset</a> (expires 1 hour).",
            'warning', $resetLink);
    }
}

redirect('/car-rental/forgot_password_sent.php?email=' . urlencode($email));
