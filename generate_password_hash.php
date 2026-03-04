<?php
// generate_password.php
// Usage: php generate_password.php my_secure_password

if ($argc < 2) {
    echo "Usage: php bin/generate_password.php <password>\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Plain password: " . $password . "\n";
echo "Hashed password: " . $hash . "\n";
echo "Length: " . strlen($hash) . " characters\n";

?>