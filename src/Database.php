<?php
declare(strict_types=1);

namespace LgrDev;

class Database
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Tools
     */
    private $tools;

    public function __construct(string $dbtype, string $host, string $dbname, string $username, string $password)
    {

        $this->pdo = new \PDO("$dbtype:host=$host,dbname=$dbname", $username, $password);

    }

    public function addMessage(string $message): string|null
    {
        /**
         * @var string|null
         */
        $key = null;
        $expdate = new \DateTimeImmutable();

        // message expire at +1 hour
        $expdate->add(new \DateInterval("P1H"));

        if (!empty($message)) {

            $key = $this->tools->createKey();
            $stmt = $this->pdo->prepare("insert into privatemessage (key,message, expire) values( ? , ? , ? )");
            $cryptedMessage = $this->tools->crypteMessage($message);
            if ($stmt->execute([$key,$cryptedMessage,$expdate]) == false) {
                $key = null;
            }

        }

        return $key;
    }

    public function getMessage(string $key): string|null
    {
        /**
         * @var string|null
         */
        $msg = null;
        $datenow = new \DateTimeImmutable();

        if (!empty($message)) {

            $stmt = $this->pdo->prepare('select message from privatemessage where key = ? and expire <= ?');

            if ($stmt->execute([$key,$datenow]) == true) {

                if (($row = $stmt->fetch())) {
                    $msg =  $this->tools->uncrypteMessage($row['message']);
                }
            }
        }

        return $msg;
    }

}
