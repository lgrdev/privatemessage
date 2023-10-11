<?php
declare(strict_types=1);

namespace LgrDev\Storage\Tests;

use LgrDev\Storage\MemcacheDatabase;
use LgrDev\Tools;
use PHPUnit\Framework\TestCase;

class MemcacheDatabaseTest extends TestCase
{
    private $memcacheDb;
    private $tools;

    protected function setUp(): void
    {
        // Crée une instance de la classe MemcacheDatabase avec un outil (Tools) avant chaque test.
        $this->tools = new Tools();
        $this->memcacheDb = new MemcacheDatabase($this->tools);
    }

    public function testAddMessageAndGetMessage()
    {
        // Test des méthodes addMessage() et getMessage().

        // Message à ajouter dans Memcache.
        $message = 'Hello, Memcache!';
        $expiration = 'default';

        // Ajoutez le message dans Memcache.
        $key = $this->memcacheDb->addMessage($message, $expiration);

        // Assurez-vous que la clé renvoyée n'est pas vide.
        $this->assertNotEmpty($key);

        // Récupérez le message à partir de Memcache en utilisant la clé.
        $retrievedMessage = $this->memcacheDb->getMessage($key);

        // Assurez-vous que le message récupéré correspond au message d'origine.
        $this->assertEquals($message, $retrievedMessage);
    }

    public function testDeleteMessage()
    {
        // Test de la méthode deleteMessage().

        // Message à ajouter dans Memcache.
        $message = 'Hello, Memcache!';
        $expiration = 'default';

        // Ajoutez le message dans Memcache.
        $key = $this->memcacheDb->addMessage($message, $expiration);

        // Supprimez le message de Memcache en utilisant la clé.
        $this->memcacheDb->deleteMessage($key);

        // Récupérez le message à partir de Memcache en utilisant la clé (doit être null car supprimé).
        $retrievedMessage = $this->memcacheDb->getMessage($key);

        // Assurez-vous que le message récupéré est null après la suppression.
        $this->assertNull($retrievedMessage);
    }

    // Vous pouvez ajouter d'autres tests pour les autres méthodes de la classe MemcacheDatabase.
}
