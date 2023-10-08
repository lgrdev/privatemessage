<?php
declare(strict_types=1);

namespace LgrDev;

use Ramsey\Uuid\Uuid;

class Tools
{
    /**
     * Key used in AES-128 encrypt
     *
     * @var string
     */
    private $key = 'GmGknh+uKR/Pub5Q34h+4Z+9yLvRPp1ylrhn22EftwL5mhy4yQvEo8dOOsYYmpPJ';

    public function crypteMessage(string $message): string
    {
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($message, $cipher, $this->key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

        return $ciphertext;
    }

    public function uncrypteMessage($crypted_message): string
    {
        $message = '';
        $c = base64_decode($crypted_message);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary = true);
        if (hash_equals($hmac, $calcmac)) {// timing attack safe comparison
            $message =  $original_plaintext;
        }

        return $message;
    }

    public function createKey(): string
    {

        $newkey = str_replace('-','', Uuid::uuid4()->toString());

        return $newkey;

    }

    public function generate_csrf_token(): string
    {

        // Check if a token is present for the current session
        if(!isset($_SESSION["csrf_token"])) {

            // No token present, generate a new one
            $token = bin2hex(random_bytes(32));
            $_SESSION["csrf_token"] = $token;

        } else {

            // Reuse the token
            $token = $_SESSION["csrf_token"];

        }

        return $token;
    }
}
