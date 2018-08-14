<?php
namespace App\SkyengTest; 
 
class DataProvider
{
    /*
     * The host address to connect
     * @var string      
    */
    private $host;
    
    /*
     * The user of host server 
     * @var string      
    */
    private $user;
    
    /*
     * The password of host server
     * @var string      
    */
    private $password;

    // я бы дописал  типы данных параметров
    
    /**
     * @param string $host 
     * @param string $user
     * @param string $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }
    
    /**
     * @param array $request
     * @return array
     */
    public function getResponse(array $request): array;
    {
        // returns a response from external service
    }
}





namespace App\SkyengTest;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
{
    private $cache; 
    private $logger;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param CacheItemPoolInterface $cache
     */
    public function __construct($host, $user, $password, CacheItemPoolInterface $cache)
    {
        parent::__construct($host, $user, $password);
        $this->cache = $cache;
    }

    /*
     * @param LoggerInterface $logger
     * @return void 
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

   
    public function getCache(array $input)
    {
        $cacheKey = $this->getCacheKey($input);
        $cacheItem = $this->cache->getItem($cacheKey);
          
            if ($cacheItem->isHit()) 
            {
                return $cacheItem->get();
            }

        $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );
    }



    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input)
    {
        try 
        {
            $this->getCache($input);

            $result = $this->getResponse($input);

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    private function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}
