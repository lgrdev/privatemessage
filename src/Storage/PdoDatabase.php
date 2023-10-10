<?php
declare(strict_types=1);

namespace LgrDev\Storage;

use LgrDev\Tools;

class PdoDatabase extends StorageBase // implements StorageInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * Constructor for initializing a database connection using PDO.
     *
     * @param string $dbtype   The type of the database (e.g., 'mysql', 'pgsql').
     * @param string $host     The hostname or IP address of the database server.
     * @param string $dbname   The name of the database to connect to.
     * @param string $username The username for authenticating with the database server.
     * @param string $password The password for authenticating with the database server.
     *
     * @throws \PDOException If there's an issue with the database connection.
     */
    public function __construct(string $dbtype, string $host, string $dbname, string $username, string $password, Tools $tools)
    {
        $this->tools = $tools;
        
        try {
            // Create a PDO (PHP Data Objects) database connection using the provided parameters.
            // The PDO constructor takes a DSN (Data Source Name) string that specifies the database type, host, and database name.
            // It also requires the username and password for authentication.
            $this->pdo = new \PDO("$dbtype:host=$host;dbname=$dbname", $username, $password);

            // Set PDO error mode to exceptions to handle database-related errors as exceptions.
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            // Handle any exceptions that occur during database connection.
            // You may want to log the error or take appropriate action depending on your application's needs.
            // In this example, we re-throw the exception to indicate a failed connection.
            throw $e;
        }
    }

    /**
     * Add a message to the database with encryption and set its expiration time.
     *
     * @param string $message    The message to be added.
     * @param string $expiration The expiration setting for the message (1, 2, 3, 4).
     *
     * @return string|null The key under which the message is stored in the database or null if the message is empty or insertion fails.
     */
    public function addMessage(string $message, string $expiration): string|null
    {
        // Initialize the key as null.
        $key = null;

        // Create a DateTimeImmutable object representing the current date and time.
        $currentDateTime = new \DateTimeImmutable();

        // Update the expiration time based on the provided expiration setting.
        switch ($expiration) {
            case '1':
                // Message expires in +1 hour.
                $currentDateTime = $currentDateTime->add(new \DateInterval("PT1H"));
                break;
            case '2':
                $currentDateTime = $currentDateTime->add(new \DateInterval("P1D"));
                break;
            case '3':
                $currentDateTime = $currentDateTime->add(new \DateInterval("P4D"));
                break;
            case '4':
                $currentDateTime = $currentDateTime->add(new \DateInterval("P7D"));
                break;
        }

        // Check if the provided message is not empty.
        if (!empty($message)) {
            // Generate a unique key for the message using the createKey() method.
            $key = $this->tools->createKey();
              
            // Encrypt the message using the crypteMessage() method.
            $encryptedMessage = $this->tools->crypteMessage($message);
            
            // Prepare a SQL query to insert the message into the database with the key, message, and expiration.
            $stmt = $this->pdo->prepare('INSERT INTO privatemessage (msgkey, msgvalue, msgexpireat) VALUES (?, ?, ?)');

            // Execute the prepared statement with the key, encrypted message, and expiration date.
            if ($stmt->execute([$key, $encryptedMessage, $currentDateTime->format('Y-m-d H:i:s')]) === false) {
                // If insertion fails, set the key to null.
                $key = null;
            }
        }

        // Return the key under which the message is stored in the database or null if the message is empty or insertion fails.
        return $key;
    }

    /**
     * Retrieve and decrypt a message from a database table based on the provided key and expiration date.
     *
     * @param string $key The key to retrieve the message.
     *
     * @return string|null The decrypted message if found and not expired, or null otherwise.
     */
    public function getMessage(string $key): string|null
    {
        // Initialize the message variable as null.
        $message = null;

        // Create a DateTimeImmutable object representing the current date and time.
        $currentDateTime = new \DateTimeImmutable();

        // Check if the provided key is not empty.
        if (!empty($key)) {
            // Prepare a SQL query to select the message from the database table where the key matches and it's not expired.
            $stmt = $this->pdo->prepare('SELECT msgvalue FROM privatemessage WHERE msgkey = ? AND msgexpireat >= ?');
            $stmt->bindParam(1, $key, \PDO::PARAM_STR);
            $stmt->bindParam(2, $currentDateTime->format('Y-m-d H:i:s'), \PDO::PARAM_STR);

            // Execute the prepared statement with the key and current date.
            if ($stmt->execute()) {

                // Fetch the row from the result.
                $row = $stmt->fetch();

                // Check if a row was found and the message is not expired.
                if ($row) {
                    
                    // Decrypt the retrieved message using the tools->uncrypteMessage() method.
                    $message = $this->tools->uncrypteMessage($row['msgvalue']);
                    
                    // delete message in database
                    $this->deleteMessage($key);
                }
            }
        }

        // Return the decrypted message or null if the key is empty, no row is found, or the message is expired.
        return $message;
    }

    public function deleteMessage(string $key): void 
    {
        // Check if the provided key is not empty.
        if (!empty($key)) {
            
            // Prepare a SQL query to delete the message from the database table where the key matches.
            $stmt = $this->pdo->prepare('DELETE FROM privatemessage WHERE msgkey = ?');
            $stmt->bindParam(1, $key, \PDO::PARAM_STR);
            
            // execute the query
            $stmt->execute();

        }
    }

}
