<?php
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";

// Check if required extensions are loaded
$required_extensions = ['pdo', 'pdo_sqlite', 'mbstring', 'tokenizer', 'xml', 'curl', 'json'];
echo "\nRequired Extensions:\n";
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ $ext\n";
    } else {
        echo "✗ $ext (NOT INSTALLED)\n";
    }
}

// Check if Composer is available
echo "\nComposer Check:\n";
exec('composer --version 2>&1', $composer_output, $composer_return);
if ($composer_return === 0) {
    echo "✓ Composer is installed\n";
    echo implode("\n", $composer_output) . "\n";
} else {
    echo "✗ Composer is not installed or not in PATH\n";
}

// Check Laravel version
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    if (class_exists('Illuminate\Foundation\Application')) {
        $app = new Illuminate\Foundation\Application(getcwd());
        echo "\nLaravel Version: " . $app->version() . "\n";
    }
} else {
    echo "\nLaravel is not installed (vendor directory missing)\n";
}

echo "\nCurrent Directory: " . getcwd() . "\n";
echo "Directory Permissions:\n";
echo "Public: " . substr(sprintf('%o', fileperms('public')), -4) . "\n";
if (file_exists('storage')) {
    echo "Storage: " . substr(sprintf('%o', fileperms('storage')), -4) . "\n";
}
if (file_exists('bootstrap/cache')) {
    echo "Bootstrap Cache: " . substr(sprintf('%o', fileperms('bootstrap/cache')), -4) . "\n";
}
?>