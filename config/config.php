<?php
/**
 * config/config.php
 * Config comun con soporte de entornos via variables de entorno (.env)
 *
 * Variables esperadas:
 *  APP_ENV       : local | producción
 *  APP_BASE_URL  : URL base
 *  DB_HOST, DB_NAME, DB_USER, DB_PASS
 *  SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS
 */

$env = getenv('APP_ENV') ?: 'local';

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'inmobiliaria_db';
$dbUser = getenv('DB_USER') ?: '';
$dbPass = getenv('DB_PASS') ?: '';

$baseUrl = getenv('APP_BASE_URL') ?: 'http://localhost';

$smtpHost = getenv('SMTP_HOST') ?: '';
$smtpPort = (int)(getenv('SMTP_PORT') ?: 587);
$smtpUser = getenv('SMTP_USER') ?: '';
$smtpPass = getenv('SMTP_PASS') ?: '';

$agencyEmail = getenv('LEAD_AGENCY_EMAIL') ?: '';
$noReplyEmail = getenv('NOREPLY_EMAIL') ?: $agencyEmail;

return [
    'env' => $env,
    'db'  => [
        'host'    => $dbHost,
        'dbname'  => $dbName,
        'user'    => $dbUser,
        'pass'    => $dbPass,
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => $baseUrl,
        'debug'    => ($env === 'local'),
    ],
    'smtp' => [
        'host' => $smtpHost,
        'port' => $smtpPort,
        'user' => $smtpUser,
        'pass' => $smtpPass,
    ],
    'emails' => [
        'agency'  => $agencyEmail,
        'noreply' => $noReplyEmail,
    ],
];
