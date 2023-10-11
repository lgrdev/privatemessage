<?php
declare(strict_types=1);

namespace LgrDev\Storage\Tests;

use LgrDev\Storage\PdoDatabase;
use LgrDev\Tools;
use PHPUnit\Framework\TestCase;

class PdoDatabaseTest extends TestCase
{
    private $pdoDb;
    private $tools;

    protected function setUp(): void
    {
        // Crée une instance de la classe PdoDatabase avec un outil (Tools) avant chaque test.
        $this->tools = new Tools();
        $this->pdoDb = new PdoDatabase($this->tools);
    }

    public function testAddMessageAndGetMessage()
    {
        // Test des méthodes addMessage() et getMessage().

        // Message à ajouter dans la base de données.
        $message = 'Hello, Database!';
        $expiration = 'default';

        // Ajoutez le message dans la base de données.
        $key = $this->pdoDb->addMessage($message, $expiration);

        // Assurez-vous que la clé renvoyée n'est pas vide.
        $this->assertNotEmpty($key);

        // Récupérez le message à partir de la base de données en utilisant la clé.
        $retrievedMessage = $this->pdoDb->getMessage($key);

        // Assurez-vous que le message récupéré correspond au message d'origine.
        $this->assertEquals($message, $retrievedMessage);
    }

    public function testDeleteMessage()
    {
        // Test de la méthode deleteMessage().

        // Message à ajouter dans la base de données.
        $message = 'Hello, Database!';
        $expiration = 'default';

        // Ajoutez le message dans la base de données.
        $key = $this->pdoDb->addMessage($message, $expiration);

        // Supprimez le message de la base de données en utilisant la clé.
        $this->pdoDb->deleteMessage($key);

        // Récupérez le message à partir de la base de données en utilisant la clé (doit être null car supprimé).
        $retrievedMessage = $this->pdoDb->getMessage($key);

        // Assurez-vous que le message récupéré est null après la suppression.
        $this->assertNull($retrievedMessage);
    }

    // Vous pouvez ajouter d'autres tests pour les autres méthodes de la classe PdoDatabase.
}
