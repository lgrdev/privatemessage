<?php
declare(strict_types=1);

namespace LgrDev\Storage;

/**
 * Classe abstraite ayant 2 utilités :
 * - elle permet de déclarer l'interface utilisée par les classes RedisDatabase et PdoDatabase
 * - elle déclare l'implémentation de l'interface StorageIterface
 * 
 */
abstract class StorageBase implements StorageInterface {
    
    // La classe de base abstraite implémente l'interface StorageInterface.
    // Elle peut contenir des méthodes communes à toutes les classes dérivées.

    const ONE_HOUR      = '1';
    const ONE_DAY       = '2';
    const FOUR_DAYS     = '3';
    const SEVEN_DAYS    = '4';

    protected function getExpiration(string $expiration): int
    {
        // by default : 1 hour
        $expire = 3600;

        // Update the expiration time based on the provided expiration setting.
        switch ($expiration) {
            case self::ONE_DAY:
                $expire = 60 * 60 * 24; // 24 hours
                break;
            case self::FOUR_DAYS:
                $expire = 60 * 60 * 24 * 4; // 4 days
                break;
            case self::SEVEN_DAYS:
                $expire = 60 * 60 * 24 * 7; // 7 days
                break;
        }

        return $expire;
    }

    protected function getDateTimeExpiration($expiration): \DateTimeImmutable
    {
        $expireDateTime = new \DateTimeImmutable();

        // Update the expiration time based on the provided expiration setting.
        switch ($expiration) {

            case self::ONE_DAY:
                $expireDateTime = $expireDateTime->add(new \DateInterval("P1D"));
                break;
            case self::FOUR_DAYS:
                $expireDateTime = $expireDateTime->add(new \DateInterval("P4D"));
                break;
            case self::SEVEN_DAYS:
                $expireDateTime = $expireDateTime->add(new \DateInterval("P7D"));
                break;
            
            default :
                // Message expires in +1 hour.
                $expireDateTime = $expireDateTime->add(new \DateInterval("PT1H"));
                break;
        }

        return  $expireDateTime;
    }

}