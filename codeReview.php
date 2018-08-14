<?php

// это не namespace, больше похоже на директорию класса 
namespace src\Integration; 
 
class DataProvider
{
    private $host;
    private $user;
    private $password;

    // я бы дописал  типы данных параметров
    
    /**
     * @param $host
     * @param $user
     * @param $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }
    
    // в комменте описал бы что передается в параметре return
    // название функции не информативное 
    /**
     * @param array $request
     *
     * @return array
     */
    public function get(array $request)
    {
        // returns a response from external service
    }
}





namespace src\Decorator; // не namespace, больше похоже на директорию класса 

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
{
    // я бы не делал public 
    public $cache; 
    public $logger;

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

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    
    // нет такого метода, должен быть get
    // я бы вынес работу с кэшем в отдельный метод
    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input)
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            // через $this->get($input) метод публичный в DataProvider
            $result = parent::get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

     // функцию лучше сделать приватной 
    public function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}
