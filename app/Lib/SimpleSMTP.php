<?php
namespace App\Lib;

use Exception;

/**
 * Clase SimpleSMTP
 * Helper para envío de correos mediante SMTP.
 * Migrado a namespace App\Lib.
 */
class SimpleSMTP {
    private $host;
    private $port;
    private $username;
    private $password;
    private $timeout = 30;
    private $socket;
    private $debug = false;

    public function __construct($host, $port, $username, $password) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function send($to, $subject, $message, $headers = '') {
        // Conectar
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->socket) {
            throw new Exception("Error conectando a SMTP: $errstr ($errno)");
        }
        $this->read();

        // Handshake
        $this->cmd("EHLO " . $_SERVER['SERVER_NAME']);
        
        // Auth
        $this->cmd("AUTH LOGIN");
        $this->cmd(base64_encode($this->username));
        $this->cmd(base64_encode($this->password));

        // Mail transaction
        $this->cmd("MAIL FROM: <" . $this->username . ">");
        $this->cmd("RCPT TO: <" . $to . ">");
        $this->cmd("DATA");

        // Headers & Body
        $email_content = "Subject: $subject\r\n";
        $email_content .= "To: $to\r\n";
        
        // Parsear headers adicionales para asegurarnos de que están bien formados
        if ($headers) {
            $email_content .= trim($headers) . "\r\n";
        }
        
        $email_content .= "\r\n" . $message . "\r\n.\r\n";
        
        $this->cmd($email_content);
        $this->cmd("QUIT");

        fclose($this->socket);
        return true;
    }

    private function cmd($command) {
        fputs($this->socket, $command . "\r\n");
        return $this->read();
    }

    private function read() {
        $response = "";
        while ($str = fgets($this->socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") { break; }
        }
        // Verificar errores (códigos 4xx o 5xx)
        $code = substr($response, 0, 3);
        if ($code >= 400) {
            throw new Exception("Error SMTP ($code): $response");
        }
        return $response;
    }
}
