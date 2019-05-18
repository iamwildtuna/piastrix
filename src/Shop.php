<?php

namespace WildTuna\Piastrix;

use Symfony\Component\Yaml\Yaml;
use WildTuna\Piastrix\Exception\ConfigValidationException;

class Shop
{
    /**
     * @var string
     */
    private $host       = null;
    /**
     * @var int
     */
    private $shop_id    = null;
    /**
     * @var string
     */
    private $secret_key = null;
    /**
     * @var array
     */
    private $params     = null;

    /**
     * @param string $config_file
     * @throws ConfigValidationException
     */
    function __constructor($config_file)
    {
        // Парсим Yaml конфиг с данными для подключения
        $this->params = Yaml::parse(file_get_contents($config_file));

        if (empty($this->params['host']) || empty($this->params['shop_id']) || empty($this->params['secret_key'])) {
            throw new ConfigValidationException('Auth info incorrect', 401);
        }

        $this->setHost($this->params['auth']['host']);
        $this->setShopId($this->params['auth']['shop_id']);
        $this->setSecretKey($this->params['auth']['secret_key']);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    /**
     * @param int $shop_id
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }

    /**
     * @param string $secret_key
     */
    public function setSecretKey($secret_key)
    {
        $this->secret_key = $secret_key;
    }

    /**
     * Возвращает список доступных провайдеров для пополнения из конфиг-файла
     * @return array
     * @throws ConfigValidationException
     */
    public function getPayInProviders()
    {
        if (empty($this->params['available_payment_systems']))
            throw new ConfigValidationException('Incorrect config file', 402);

        return $this->params['available_payment_systems']['in'];
    }

    /**
     * Возвращает список доступных провайдеров для вывода средств из конфиг-файла
     * @return array
     * @throws ConfigValidationException
     */
    public function getPayOutProviders()
    {
        if (empty($this->params['available_payment_systems']))
            throw new ConfigValidationException('Incorrect config file', 402);

        return $this->params['available_payment_systems']['out'];
    }
}