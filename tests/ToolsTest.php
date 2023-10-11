<?php
declare(strict_types=1);

namespace LgrDev\Tests;

use LgrDev\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{
    private $tools;

    protected function setUp(): void
    {
        // Crée une instance de la classe Tools avant chaque test.
        $this->tools = new Tools();
    }

    public function testCrypteMessage()
    {
        // Test de la méthode crypteMessage
        $message = 'Hello, World!';
        $encryptedMessage = $this->tools->crypteMessage($message);

        // Assurez-vous que le message crypté n'est pas vide
        $this->assertNotEmpty($encryptedMessage);

        // Vous pouvez ajouter d'autres assertions ici pour vérifier le cryptage.
    }

    public function testUncrypteMessage()
    {
        // Test de la méthode uncrypteMessage
        $message = 'Hello, World!';
        $encryptedMessage = $this->tools->crypteMessage($message);

        // Assurez-vous que le message décrypté correspond au message d'origine
        $decryptedMessage = $this->tools->uncrypteMessage($encryptedMessage);
        $this->assertEquals($message, $decryptedMessage);
    }

    public function testCreateMessageKey()
    {
        // Test de la méthode createMessageKey
        $key = $this->tools->createMessageKey();

        // Assurez-vous que la clé générée n'est pas vide et a la bonne longueur (32 caractères)
        $this->assertNotEmpty($key);
        $this->assertEquals(32, strlen($key));
    }

    public function testGenerateCsrfToken()
    {
        // Test de la méthode generate_csrf_token
        $token1 = $this->tools->generate_csrf_token();
        $token2 = $this->tools->generate_csrf_token();

        // Assurez-vous que les jetons générés sont non vides et égaux (meme session).
        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
        $this->assertEquals($token1, $token2);
    }

    // Vous pouvez ajouter d'autres tests pour les autres méthodes de la classe Tools.
}

