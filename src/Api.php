<?php

declare(strict_types=1);

namespace LgrDev;

use LgrDev\Storage\RedisDatabase;

class Api
{

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

    public function __construct(RedisDatabase $redis, Tools $tools)
    {
        $this->stockage = $redis;
        $this->tools    = $tools;
    }

    public function apiCreateMessage(): callable
    {
        return function () {

            if (!$this->tools->verifiyAuthenication()) {
                http_response_code(401);
                echo json_encode(["Error" => "Authentification failed"]);        
                return;
            }
            
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (isset($data['expirein']) && !empty($data['expirein'])) {
                $expirein = $data['expirein'];
            } else {
                $expirein = '1';
            }
            if (isset($data['message']) && !empty($data['message'])) {
                $message = $data['message'];
            } else {
                http_response_code(400);
                echo json_encode(["Error" => "bad request","Message" => 'Message vide.']);
                return;
            }

            // Add the message to storage and get its unique key
            $key = $this->stockage->addMessage($message, $expirein);

            if (isset($key) && !empty($key)) {
                http_response_code(200);
                echo json_encode(["Key" => $key]);        
            } else {
                http_response_code(400);
                echo json_encode(["Error" => "bad request","Message" => 'Message vide.']);
            }

        };

    }

    public function apiGetMessage(): callable
    {
        return function (string $key) {

            if (!$this->tools->verifiyAuthenication()) {
                http_response_code(401);
                echo json_encode(["Error" => "Authentification failed"]);        
                return;
            }
            
            $message = $this->stockage->getMessage($key);

            if ($message == '') {
                // If the message is empty, indicate that it doesn't exist or has already been read
                http_response_code(400);
                echo json_encode(["Error" => "bad request","Message" => 'Message inexistant ou déjà lu.']);
                return;
            }

            // Render the 'newsecret.twig' template with the CSRF token
            http_response_code(200);
            echo json_encode(["Message" => $message]);        
        };

    }
    public function apiStatusMessage(): callable
    {
        return function (string $key) {

            if (!$this->tools->verifiyAuthenication()) {
                http_response_code(401);
                echo json_encode(["Error" => "Authentification failed"]);        
                return;
            }
            
            $isExist = $this->stockage->existMessage($key);

            if ($isExist != 1) {
                // If the message is empty, indicate that it doesn't exist or has already been read
                http_response_code(400);
                echo json_encode(["Error" => "bad request","Message" => 'Message inexistant ou déjà lu.']);
                return;
            }

            // Render the 'newsecret.twig' template with the CSRF token
            http_response_code(200);
            echo json_encode(["Message" => 'This key is valid']);        
        };

    }

    public function apiDeleteMessage(): callable
    {
        return function (string $key) {

            if (!$this->tools->verifiyAuthenication()) {
                http_response_code(401);
                echo json_encode(["Error" => "Authentification failed"]);        
                return;
            }

            $this->stockage->deleteMessage($key);

            http_response_code(200);
            echo json_encode(["Message" => 'Message supprimé.']);          
            
        };

    }
}
