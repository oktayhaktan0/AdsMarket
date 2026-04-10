<?php
/**
 * Simple SMTP Client for AdsMarket.nl
 * Connects directly to SMTP server to bypass mail() limitations
 */
class SimpleSMTP {
    private $host, $port, $user, $pass, $timeout = 30;

    public function __construct($host, $port, $user, $pass) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
    }

    public function send($to, $subject, $body_html, $from_name, $from_email) {
        $boundary = md5(uniqid());
        $headers = [
            "MIME-Version: 1.0",
            "To: <$to>",
            "From: \"$from_name\" <$from_email>",
            "Subject: $subject",
            "Date: " . date('r'),
            "Content-Type: multipart/alternative; boundary=\"$boundary\"",
            "Message-ID: <" . time() . "-" . md5($to) . "@adsmarket.nl>"
        ];

        $message = "This is a multi-part message in MIME format.\r\n\r\n";
        $message .= "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . strip_tags($body_html) . "\r\n\r\n";
        $message .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n<html><body>$body_html</body></html>\r\n\r\n";
        $message .= "--$boundary--";

        try {
            // Port 465 usually uses SSL
            $prefix = ($this->port == 465) ? "ssl://" : "";
            $socket = @fsockopen($prefix . $this->host, $this->port, $errno, $errstr, $this->timeout);
            
            if (!$socket) throw new Exception("Bağlantı hatası: $errstr");

            $this->getResponse($socket); // 220
            
            fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, "AUTH LOGIN\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, base64_encode($this->user) . "\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, base64_encode($this->pass) . "\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, "MAIL FROM: <$from_email>\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, "RCPT TO: <$to>\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, "DATA\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, implode("\r\n", $headers) . "\r\n\r\n" . $message . "\r\n.\r\n");
            $this->getResponse($socket);
            
            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            return true;
        } catch (Exception $e) {
            error_log("SMTP Hatası: " . $e->getMessage());
            return false;
        }
    }

    private function getResponse($socket) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $response;
    }
}
?>
