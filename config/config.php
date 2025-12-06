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

$env = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'local';

$dbHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$dbName = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'inmobiliaria_db';
$dbUser = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: '';
$dbPass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';

$baseUrl = $_ENV['APP_BASE_URL'] ?? getenv('APP_BASE_URL') ?: 'http://localhost';

$smtpHost = $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?: '';
$smtpPort = (int)($_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: 587);
$smtpUser = $_ENV['SMTP_USER'] ?? getenv('SMTP_USER') ?: '';
$smtpPass = $_ENV['SMTP_PASS'] ?? getenv('SMTP_PASS') ?: '';

$agencyEmail = $_ENV['LEAD_AGENCY_EMAIL'] ?? getenv('LEAD_AGENCY_EMAIL') ?: '';
$noReplyEmail = $_ENV['NOREPLY_EMAIL'] ?? getenv('NOREPLY_EMAIL') ?: $agencyEmail;

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
