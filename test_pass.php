<?php
$hash = '$2y$10$/A.NicdqFzwNgvnOPRzfHlaBW2/VHgAb4G';
$passwords = ['admin', 'password', 'password123', 'admin123', 'admin@123', 'admin1234', '123456', '12345678', 'admin12345'];

foreach ($passwords as $p) {
    if (password_verify($p, $hash)) {
        echo "Password is: $p\n";
        exit;
    }
}
echo "Not found\n";
