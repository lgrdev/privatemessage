<?php

declare(strict_types=1);

namespace LgrDev;

use Ramsey\Uuid\Uuid;
use Monolog\Logger; // The Logger instance
use Logtail\Monolog\LogtailHandler;


/**
 * Utility class for various functions including message encryption and key generation.
 */
class Tools
{
    /**
     * Key used in AES-128 encryption.
     * length = 64
     *
     * @var string
     */
    private $keyAES128 = 'GmGknh+uKR/Pub5Q34h+4Z+9yLvRPp1ylrhn22EftwL5mhy4yQvEo8dOOsYYmpPJ';
    private $arrEnv = [];

    /**
     * Instance de la classe Logger
     *
     * @var Logger
     */
    public $logger;


    /**
     * Constructeur de la classe Tools
     *
     * @param string|null $pathenvfile
     */
    public function __construct(string $pathenvfile = null)
    {
        if ($pathenvfile != null) {
            $file = $pathenvfile;
        } else {
            $file = dirname(__FILE__, 2) . '/.env';
        }

        $this->loadEnv($file);

        if ($this->issetEnv('AES128_KEY')) {

            $this->keyAES128 = $this->getEnv('AES128_KEY');

        }


        $this->logger = new Logger("my_logger");
        if ($this->issetEnv('bs_token')) {
            $this->logger->pushHandler(new LogtailHandler($this->getEnv('bs_token')));
        } else {
        
        }

    }

    /**
     * Charge le fichier .env dans le tableau $arrEnv[]
     *
     * @param string $path
     * @return void
     */
    public function loadEnv(string $path): void
    {
        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $path));
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            $this->arrEnv[$name] = $value;

        }
    }

    /**
     * Test si la variable de configuration $variable existe
     *
     * @param string $variable
     * @return boolean
     */
    public function issetEnv(string $variable): bool
    {

        return isset($this->arrEnv[$variable]);

    }

    /**
     * Récupère la valeur d'un variable d'environnement
     *
     * @param string $variable
     * @return string|null
     */
    public function getEnv(string $variable): string|null
    {
        $value = null;

        if (!empty($variable)) {

            if (isset($this->arrEnv[$variable])) {

                $value = $this->arrEnv[$variable];

            }

        }

        return $value;
    }

    /**
     * Affecte une valeur à une variable d'environnement
     *
     * @param string $variable
     * @param string $value
     * @return boolean
     */
    public function setEnv(string $variable, string $value): bool
    {
        $bReturn = false;

        if (!empty($variable)) {

            $this->arrEnv[$variable] = $value ;
            $bReturn = true;
        }

        return $bReturn;
    }

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
        $ciphertext_raw = openssl_encrypt($message, $cipher, $this->keyAES128, $options = OPENSSL_RAW_DATA, $iv);

        // Calculate an HMAC for integrity verification and encode the result.
        $hmac = hash_hmac('sha256', $ciphertext_raw, $this->keyAES128, $as_binary = true);
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
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->keyAES128, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $this->keyAES128, $as_binary = true);

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
    public function createMessageKey(): string
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

    /**
     * Verify a CSRF (Cross-Site Request Forgery) token.
     *
     * @param string $token The token to be verified.
     *
     * @return bool True if the token is valid, false otherwise.
     */
    public function verify_csrf_token(string $token): bool
    {
        // Check if a token is present for the current session.
        if (!isset($_SESSION["csrf_token"])) {
            // No token present, return false.
            return false;
        }

        // Check if the token is valid.
        if ($_SESSION["csrf_token"] !== $token) {
            // Invalid token, return false.
            return false;
        }

        // Token is valid.
        return true;
    }

    public function verifiyAuthenication(): bool
    {
        $auth = $_SERVER["HTTP_" . strtoupper(str_replace("-","_","Authorization"))];
        if (isset( $auth) && !empty($auth))
        {

            $head = explode(":", $auth);
            $login = $head[0];
            $apikey = $head[1];

            if (isset($login) && !empty($login) && isset($apikey) && !empty($apikey))
            {
                $pdo = new \PDO($this->getEnv('db_dsn'), $this->getEnv('db_user'), $this->getEnv('db_password'));
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $sql = "SELECT * FROM pmapiusers WHERE login = :login AND apikey = :apikey";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':login', $login, \PDO::PARAM_STR);
                $stmt->bindParam(':apikey', $apikey, \PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($result)
                {
                    return true;
                }

            }
        }

        return false;
    }

    public function verifiyUserAccess(string $login,string $password): bool
    {
        $pdo = new \PDO($this->getEnv('db_dsn'), $this->getEnv('db_user'), $this->getEnv('db_password'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM pmapiusers WHERE login = :login AND password = :password";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $login, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result)
        {
            return true;
        }

        return false;
    }

    public function existLogin(string $login): bool
    {
        $pdo = new \PDO($this->getEnv('db_dsn'), $this->getEnv('db_user'), $this->getEnv('db_password'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT * FROM pmapiusers WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $login, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result)
        {
            return true;
        }

        return false;
    }

    public function createUser(string $login,string $password): bool
    {   
        $pdo = new \PDO($this->getEnv('db_dsn'), $this->getEnv('db_user'), $this->getEnv('db_password'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO pmapiusers (login, password, apikey) VALUES (:login, :password, :apikey)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':login', $login, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, \PDO::PARAM_STR);
        $stmt->bindParam(':apikey', bin2hex(random_bytes(32)), \PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }

}
