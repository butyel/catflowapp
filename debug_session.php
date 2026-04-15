<?php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/db.php';

echo "<h1>Debug Session</h1>";
echo "<pre>";
echo "Session Data:\n";
print_r($_SESSION);

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    echo "\nDatabase User Data:\n";
    print_r($user);
}
else {
    echo "\nNo user logged in session.\n";
}
echo "</pre>";
?>
