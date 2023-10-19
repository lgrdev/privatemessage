<?php

declare(strict_types=1);

namespace LgrDev;

use LgrDev\Storage\RedisDatabase;
use Twig\Environment;

class PrivateMessage
{
    /**
     * Undocumented variable
     *
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Undocumented variable
     *
     * @var LgrDev\Storage\RedisDatabase
     */
    private $stockage;

    /**
     * Undocumented variable
     *
     * @var Tools
     */
    private $tools;

    public function __construct(RedisDatabase $redis, Tools $tools, Environment $twig)
    {
        $this->stockage = $redis;
        $this->tools    = $tools;
        $this->twig     = $twig;
    }

    public function displayResultKey(): callable
    {
        return function () {
            // Get the comment and set the expiration time
            $message = $_POST['comment'];
            $expirein = (isset($_POST['expirein']) && !empty($_POST['expirein'])) ? $_POST['expirein'] : '1';

            // Add the message to storage and get its unique key
            $key = $this->stockage->addMessage($message, $expirein);

            // Generate a link for the user to access the secret message
            $link = 'https://' . $_SERVER['HTTP_HOST'] . '/message/' . $key;

            // Render the 'linksecret.twig' template with the link
            echo $this->twig->render('linksecret.twig', ["link" => $link]);
        };
    }

    public function displayMessage(): callable
    {
        return function (string $key) {
            // Retrieve the Id message from the URL
            // $key = $_GET['id'];

            // Get the message associated with the key from storage
            $message = $this->stockage->getMessage($key);

            if ($message == '') {
                // If the message is empty, indicate that it doesn't exist or has already been read
                $message = "Message inexistant ou déjà lu.";
            }

            // Render the 'readsecret.twig' template with the secret message
            echo $this->twig->render('readsecret.twig', ["secret" => $message]);
        };
    }

    public function displayIndex(): callable
    {
        return function () {
            // Generate a new CSRF token
            $token = $this->tools->generate_csrf_token();
    
            // Store the CSRF token in the session for future submissions
            $_SESSION["csrf_token"] = $token;
    
            // Render the 'newsecret.twig' template with the CSRF token
            echo $this->twig->render('newsecret.twig', ["Token" => $token]);
        };
    }

    public function index(): callable
    {
        // Check if the form has been submitted
        if (isset($_POST['comment']) && !empty($_POST['comment'])) {

            // Check if the CSRF token is valid
            if ($_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                // Reset token to prevent further submissions
                unset($_SESSION["csrf_token"]);
                die("CSRF token validation failed");
            }

            $this->displayResultKey();

        } elseif (isset($_GET['secret']) && !empty($_GET['secret'])) {

            $this->displayMessage();

        } else {

            // Render the 'newsecret.twig' template for a new secret message submission
            $this->displayIndex();

        }
    }

}
