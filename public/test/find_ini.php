<?php
echo "PHP INI Path: " . php_ini_loaded_file() . "\n";
echo "OpenSSL Loaded: " . (extension_loaded('openssl') ? 'YES' : 'NO') . "\n";
?>
