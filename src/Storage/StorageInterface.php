<?php

namespace LgrDev\Storage;

/**
 * Interface for a storage system to add and retrieve messages.
 */
interface StorageInterface
{
    /**
     * Add a message to the storage system with optional expiration.
     *
     * @param string $message    The message to be added.
     * @param string $expiration The expiration setting for the message.
     *                           Specific implementations may interpret this differently.
     *
     * @return string|null The key under which the message is stored, or null if the operation fails.
     */
    public function addMessage(string $message, string $expiration): string|null;

    /**
     * Retrieve a message from the storage system based on a provided key.
     *
     * @param string $key The key to retrieve the message.
     *
     * @return string|null The retrieved message, or null if the message is not found or expired.
     */
    public function getMessage(string $key): string|null;

    public function deleteMessage(string $key): void;

}
