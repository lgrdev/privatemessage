<?php

declare(strict_types=1);

namespace LgrDev\Storage;

use LgrDev\Tools;

class MemcacheDatabase extends StorageBase
{
    /**
     * Undocumented variable
     *
     * @var Tools
     */
    private $tools;

    /**
     * Undocumented variable
     *
     * @var \Redis
     */
    private $store = null;

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;

        if (!$this->tools->issetEnv('mc_host') || !$this->tools->issetEnv('mc_port')) {
            throw new \Exception('Parametres Memcached non fournis');
        }

        try {
            $this->store = new \Memcached('privatemessage');

            $this->store->addServer($this->tools->getEnv('mc_host'), intval($this->tools->getEnv('mc_port')));
        } catch(\MemcachedException $e) {
            $this->tools->logger->error($e->getMessage());
            throw $e;
        }


    }

    /**
     * Add a message to Redis with encryption and set its expiration time.
     *
     * @param string $message    The message to be added to Redis.
     * @param string $expiration The expiration setting for the message (2, 3, 4, or default).
     *
     * @return string|null The key under which the message is stored in Redis or null if the message is empty.
     */
    public function addMessage(string $message, string $expiration): string|null
    {
        // Check if the provided message is empty. If it is, return null immediately.
        if (empty($message)) {
            return null;
        }

        // Generate a unique key for the message using the createKey() method.
        $key = $this->tools->createMessageKey();

        // Encrypt the message using the crypteMessage() method.
        $encryptedMessage = $this->tools->crypteMessage($message);

        // Set the default expiration time (default : 3600 seconds).
        $expire = $this->getExpiration($expiration);

        // Store the encrypted message in Redis with the generated key and specified expiration time.
        $this->store->set($key, $encryptedMessage, $expire);
        echo $key;
        // Return the key under which the message is stored in Redis.
        return $key;
    }

    /**
     * Retrieve and decrypt a message from Redis and remove it from Redis.
     *
     * @param string $key The key to retrieve the message from Redis.
     *
     * @return string|null The decrypted message if found, or null if the key is empty or the message is not in Redis.
     */
    public function getMessage(string $key): string|null
    {
        // Initialize the message variable as null.
        $message = null;

        // Check if the provided key is empty or null.
        if (!empty($key)) {
            // Retrieve the message associated with the key from Redis.
            $message = $this->store->get($key);

            // Check if a message was found in Redis.
            if ($message !== false) {

                // Decrypt the retrieved message using the tools->uncrypteMessage() method.
                $message = $this->tools->uncrypteMessage($message);

                // Remove the key and its associated message from Redis.
                $this->deleteMessage($key);

            } else {
                // If the message was not found in Redis, set the message variable to null.
                $message = null;
            }
        }
        echo $message;
        // Return the decrypted message or null if the key is empty or the message is not in Redis.
        return $message;
    }

    /**
     * Return 1 if the key exist in database or 0 if not.
     *
     * @param string $key The key to retrieve the message from memcached.
     *
     * @return int The key exist or not
     */
    public function statusMessage(string $key): int
    {
        // Initialize the variable as 0.
        $existKey = 0;

        // Check if the provided key is empty or null.
        if (!empty($key)) {
            
            // Retrieve the message associated with the key from memcached.
            $message = $this->store->get($key);

            // Check if a message was found in memcached
            if ($message !== false) {
                $existKey=1;
            }
        }

        // Return 1 if key exist or 0 if not.
        return $existKey;
    }

    public function deleteMessage(string $key): void
    {
        // Check if the provided key is not empty.
        if (!empty($key)) {

            // Remove the key and its associated message from Redis.
            $this->store->delete($key);
            echo $key;
        }
    }

}
