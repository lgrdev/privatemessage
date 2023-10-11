<?php
declare(strict_types=1);

namespace LgrDev\Storage\Tests;

use LgrDev\Storage\RedisDatabase;
use LgrDev\Tools;
use PHPUnit\Framework\TestCase;

class RedisDatabaseTest extends TestCase
{
    private $redisDb;
    private $tools;

    protected function setUp(): void
    {
        // Crée une instance de la classe RedisDatabase avec un outil (Tools) avant chaque test.
        $this->tools = new Tools();
        $this->redisDb = new RedisDatabase($this->tools);
    }

    public function testAddMessageAndGetMessage()
    {
        // Test des méthodes addMessage() et getMessage().

        // Message à ajouter dans Redis.
        $message = 'Hello, Redis!';
        $expiration = 'default';

        // Ajoutez le message dans Redis.
        $key = $this->redisDb->addMessage($message, $expiration);

        // Assurez-vous que la clé renvoyée n'est pas vide.
        $this->assertNotEmpty($key);

        // Récupérez le message à partir de Redis en utilisant la clé.
        $retrievedMessage = $this->redisDb->getMessage($key);

        // Assurez-vous que le message récupéré correspond au message d'origine.
        $this->assertEquals($message, $retrievedMessage);
    }

    public function testDeleteMessage()
    {
        // Test de la méthode deleteMessage().

        // Message à ajouter dans Redis.
        $message = 'Hello, Redis!';
        $expiration = 'default';

        // Ajoutez le message dans Redis.
        $key = $this->redisDb->addMessage($message, $expiration);

        // Supprimez le message de Redis en utilisant la clé.
        $this->redisDb->deleteMessage($key);

        // Récupérez le message à partir de Redis en utilisant la clé (doit être null car supprimé).
        $retrievedMessage = $this->redisDb->getMessage($key);

        // Assurez-vous que le message récupéré est null après la suppression.
        $this->assertNull($retrievedMessage);
    }

    // Vous pouvez ajouter d'autres tests pour les autres méthodes de la classe RedisDatabase.
}
