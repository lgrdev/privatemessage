<?php
declare(strict_types=1);

namespace LgrDev;

use Ramsey\Uuid\Uuid;

/**
 * Utility class for various functions including message encryption and key generation.
 */
class Tools
{
    /**
     * Key used in AES-128 encryption.
     *
     * @var string
     */
    private $key = 'GmGknh+uKR/Pub5Q34h+4Z+9yLvRPp1ylrhn22EftwL5mhy4yQvEo8dOOsYYmpPJ';

    /**
     * Encrypt a message using AES-128 encryption with CBC mode.
     *
     * @param string $message The message to be encrypted.
     *
     * @return string The encrypted message.
     */
    public function crypteMessage(string $message): string
    {
        // Generate an initialization vector (IV) and encrypt the message.
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($message, $cipher, $this->key, $options = OPENSSL_RAW_DATA, $iv);

        // Calculate an HMAC for integrity verification and encode the result.
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

        return $ciphertext;
    }

    /**
     * Decrypt a previously encrypted message using AES-128 encryption with CBC mode.
     *
     * @param string $crypted_message The encrypted message.
     *
     * @return string The decrypted message, or an empty string if the decryption fails.
     */
    public function uncrypteMessage($crypted_message): string
    {
        $message = '';

        // Decode the base64-encoded message and extract components.
        $c = base64_decode($crypted_message);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);

        // Decrypt the ciphertext and verify the HMAC.
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->key, $as_binary = true);

        // Verify if the HMAC matches, ensuring data integrity.
        if (hash_equals($hmac, $calcmac)) { // Timing attack safe comparison
            $message =  $original_plaintext;
        }

        return $message;
    }

    /**
     * Create a new unique key.
     *
     * @return string The generated key.
     */
    public function createKey(): string
    {
        // Generate a new key by removing hyphens from a UUIDv4.
        $newkey = str_replace('-', '', Uuid::uuid4()->toString());

        return $newkey;
    }

    /**
     * Generate a CSRF (Cross-Site Request Forgery) token for use in web forms.
     *
     * @return string The generated CSRF token.
     */
    public function generate_csrf_token(): string
    {
        // Check if a token is present for the current session.
        if (!isset($_SESSION["csrf_token"])) {
            // No token present, generate a new one.
            $token = bin2hex(random_bytes(32));
            $_SESSION["csrf_token"] = $token;
        } else {
            // Reuse the existing token.
            $token = $_SESSION["csrf_token"];
        }

        return $token;
    }
}

