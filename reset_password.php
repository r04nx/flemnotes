<?php
/**
 * Password Reset Utility
 * Usage: php reset_password.php <route> [new_password]
 * If no password provided, it will remove the password protection
 */

require_once 'config.php';
require_once 'database.php';

if ($argc < 2) {
    echo "Usage: php reset_password.php <route> [new_password]\n";
    echo "Examples:\n";
    echo "  php reset_password.php newnote          - Remove password from 'newnote'\n";
    echo "  php reset_password.php newnote mypass   - Set password 'mypass' for 'newnote'\n";
    exit(1);
}

$route = $argv[1];
$newPassword = $argv[2] ?? null;

$db = Database::getInstance()->getConnection();

// Check if note exists
$stmt = $db->prepare("SELECT * FROM notes WHERE route = ?");
$stmt->execute([$route]);
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    echo "Error: Note with route '$route' does not exist.\n";
    exit(1);
}

echo "Found note: $route\n";
echo "Current status: " . (!empty($note['password_hash']) ? 'Password protected' : 'No password') . "\n\n";

if ($newPassword === null) {
    // Remove password
    $stmt = $db->prepare("UPDATE notes SET password_hash = NULL WHERE route = ?");
    $stmt->execute([$route]);
    echo "✓ Password removed from '$route'\n";
    echo "The note is now accessible without a password.\n";
} else {
    // Set new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE notes SET password_hash = ? WHERE route = ?");
    $stmt->execute([$passwordHash, $route]);
    echo "✓ Password set for '$route'\n";
    echo "New password: $newPassword\n";
    
    // Verify it works
    if (password_verify($newPassword, $passwordHash)) {
        echo "✓ Password verification: SUCCESS\n";
    } else {
        echo "✗ Password verification: FAILED (this shouldn't happen)\n";
    }
}

echo "\nDone!\n";
