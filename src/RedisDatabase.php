<?php
declare(strict_types=1);

namespace LgrDev;

class RedisDatabase
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
    private $redis =null;

    function __construct(string $host, int $port, string $auth, Tools $tools)
    {
        
        $this->redis = new \Redis();
        $this->redis->connect($host,$port);

        if (!empty($auth)) {
            $this->redis->auth($auth);
        }
        
        $this->tools = $tools;
    }
    
    public function addMessage(string $message): string|null
    {
        $key = null;

        if (!empty($message)) {

            $key = $this->tools->createKey();
           
            $encrypte = $this->tools->crypteMessage($message);


            $this->redis->set($key, $encrypte, 3600); // 1 minute

        }

        return $key;
    }

 
    public function getMessage(string $key): string|null
    {
        $message = null;

        if (!empty($key))
        {
            $message = $this->redis->get($key); 
            $this->redis->del($key); 
            
            $message = $this->tools->uncrypteMessage($message);
        }

        return $message;
    }


}

?>