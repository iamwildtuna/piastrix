<?php

namespace WildTuna\Piastrix;

use WildTuna\Piastrix\Shop;
use WildTuna\Piastrix\Exception\PiastrixException;

class Client
{
    /** @var Shop */
    private $shop;

    /** @var \GuzzleHttp\Client */
    private $httpClient;

    /**
     * @param Shop $shop
     * @throws \InvalidArgumentException
     */
    function __constructor($shop, $timeout = 120)
    {
        if (empty($shop))
            throw new \InvalidArgumentException('Invalid Shop info object');

        $this->shop = $shop;
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->shop->getHost(),
            'timeout' => $timeout,
        ]);
    }


    /**
     * Вывод средств в других валютах с баланса магазина
     *
     * @param array $data
     * @return array
     * @throws PiastrixException
     */
    public function createWithdraw($data)
    {
        $data['sign'] = $this->genSignature($data);

        $response = $this->httpClient->post('/withdraw/status',['json'=>$data]);
        $response = json_decode($response->getBody()->getContents(), true);

        if ($response['error_code'] > 0)
            throw new PiastrixException($response['message']);

        return $response;
    }

    /**
     * Запрос статуса выплаты по id
     *
     * @param int $withdrawId - уникальные идентификатор выплаты на стороне Piastrix
     * @return mixed
     * @throws PiastrixException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWithdrawInfo($withdrawId)
    {
        $data['shop_id'] = $this->shop->getShopId();
        $data['now'] = date('Y-m-d H:i:s').'00';
        $data['withdraw_id'] = $withdrawId;
        $data['sign'] = $this->genSignature($data);

        $response = $this->httpClient->post('/withdraw/status',['json'=>$data]);
        $response = json_decode($response->getBody()->getContents(), true);

        if ($response['error_code'] > 0)
            throw new PiastrixException($response['message']);

        $payInfo['status_code'] = $response['data']['status'];
        $payInfo['status_text'] = self::getStatusText($response['data']['status']);

        return $payInfo;
    }

    /**
     * Запрос статуса выплаты по номеру платежа магазина
     *
     * @param string $withdrawId - уникальные идентификатор выплаты на стороне магазина
     * @return mixed
     * @throws PiastrixException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWithdrawInfoByShopId($withdrawId)
    {
        $data['shop_id'] = $this->shop->getShopId();
        $data['now'] = date('Y-m-d H:i:s').'00';
        $data['shop_payment_id'] = $withdrawId;
        $data['sign'] = $this->genSignature($data);

        $response = $this->httpClient->post('/withdraw/shop_payment_status',['json'=>$data]);
        $response = json_decode($response->getBody()->getContents(), true);

        if ($response['error_code'] > 0)
            throw new PiastrixException($response['message']);

        $payInfo['status_code'] = $response['data']['status'];
        $payInfo['status_text'] = self::getStatusText($response['data']['status']);

        return $payInfo;
    }

    /**
     * Запрос баланса по магазина
     *
     * @return mixed
     * @throws PiastrixException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getShopBalance()
    {
        $data['shop_id'] = $this->shop->getShopId();
        $data['now'] = date('Y-m-d H:i:s').'00';
        $data['sign'] = $this->genSignature($data);

        $response = $this->httpClient->post('/shop_balance',['json'=>$data]);
        $response = json_decode($response->getBody()->getContents(), true);

        if ($response['error_code'] > 0)
            throw new PiastrixException($response['message']);

        return $response['data']['balances']; // Массив с информацией по счетам магазина
    }

    /**
     * @param array $data
     * @return string
     */
    public function genSignature($data)
    {
        ksort($data);
        return hash('sha256',implode(':', $data) . $this->shop->getSecretKey());
    }

    /**
     * @param array $data
     * @return bool
     */
    public function checkSignature($data)
    {
        $data_sign = $data['sign'];
        unset($data['sign']);
        return $data_sign == $this->genSignature($data);
    }

    /**
     * @param int $statusCode
     * @return string
     */
    public static function getStatusText($statusCode)
    {
        $statusText = "Unknown status code: ".$statusCode;

        switch ($statusCode) {
            case 1:
                $statusText = 'New вывод получен и создан в системе (промежуточный статус)';
                break;
            case 2:
                $statusText = 'WaitingManualConfirmation вывод ожидает ручного проведения на стороне Piastrix';
                break;
            case 3:
                $statusText = 'PsProcessing вывод пытается провестись на стороне поставщика услуги';
                break;
            case 4:
                $statusText = 'PsProcessingError ошибка в проведении вывода на стороне поставщика услуги';
                break;
            case 5:
                $statusText = 'Success вывод успешно проведен на стороне поставщика услуги, финальный статус';
                break;
            case 6:
                $statusText = 'Rejected вывод отклонен на стороне поставщика услуги, финальный статус';
                break;
            case 7:
                $statusText = 'ManualConfirmed вывод подтвержден на стороне системы Piastrix и отправлен на поставщика услуг';
                break;
            case 8:
                $statusText = 'ManualCanceled вывод отменен вручную на стороне системы Piastrix, финальный статус';
                break;
            case 9:
                $statusText = 'PsNetworkError сетевая ошибка на стороне платежной системы';
                break;
        }

        return $statusText;
    }
}