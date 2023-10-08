<?php
declare(strict_types=1);

namespace LgrDev;

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
     * @var LgrDev\RedisDatabase
     */
    private $stockage;

    /**
     * Undocumented variable
     *
     * @var Tools
     */
    private $tools;

    public function __construct(RedisDatabase $redis,Tools $tools,Environment $twig)
    {
        $this->stockage = $redis;
        $this->tools = $tools;

        $this->twig     = $twig;
    }

    public function index()
    {

        if (isset($_POST['comment']) && !empty($_POST['comment'])) {

            if ($_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                // Reset token
                unset($_SESSION["csrf_token"]);
                die("CSRF token validation failed");
            }

            $message = $_POST['comment'];

            $key = $this->stockage->addMessage($message);

            $link = 'https://'.$_SERVER['HTTP_HOST'].'/?secret=' . $key;

            // Render linksecret template
            echo $this->twig->render('linksecret.twig', ["link" => $link]);

        } elseif (isset($_GET['secret']) && !empty($_GET['secret'])) {

            $key = $_GET['secret'];
            $message = $this->stockage->getMessage($key);

            if ($message == '') {

                $message = "Message inexistant ou déjà lu.";

            }

            // Render readsecret template
            echo $this->twig->render('readsecret.twig', ["secret" => $message]);
        } else {
            // Render newsecret template

            $token = $this->tools->generate_csrf_token();
            echo $this->twig->render('newsecret.twig', ["Token" => $token]);
        }

    }

}
