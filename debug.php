<?php
require_once 'config.php';
require_once 'database.php';

$db = Database::getInstance()->getConnection();

echo "=== Debug: Notes in Database ===\n\n";

$stmt = $db->query("SELECT route, password_hash, is_public, created_at FROM notes ORDER BY created_at DESC LIMIT 10");
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($notes as $note) {
    echo "Route: " . $note['route'] . "\n";
    echo "Has Password: " . (!empty($note['password_hash']) ? 'YES' : 'NO') . "\n";
    if (!empty($note['password_hash'])) {
        echo "Password Hash: " . substr($note['password_hash'], 0, 20) . "...\n";
    }
    echo "Is Public: " . $note['is_public'] . "\n";
    echo "Created: " . date('Y-m-d H:i:s', $note['created_at']) . "\n";
    echo "---\n";
}

echo "\n=== Testing Password for 'newnote' ===\n";
$stmt = $db->prepare("SELECT * FROM notes WHERE route = ?");
$stmt->execute(['newnote']);
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if ($note) {
    echo "Note exists: YES\n";
    echo "Has password: " . (!empty($note['password_hash']) ? 'YES' : 'NO') . "\n";
    
    if (!empty($note['password_hash'])) {
        // Test some common passwords
        $testPasswords = ['test', 'password', '123456', 'admin', ''];
        foreach ($testPasswords as $testPwd) {
            $result = password_verify($testPwd, $note['password_hash']);
            echo "Password '$testPwd': " . ($result ? 'MATCH' : 'no match') . "\n";
        }
    }
} else {
    echo "Note 'newnote' does not exist\n";
}
