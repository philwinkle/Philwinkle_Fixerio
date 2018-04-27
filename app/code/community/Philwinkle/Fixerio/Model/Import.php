<?php

class Philwinkle_Fixerio_Model_Import extends Mage_Directory_Model_Currency_Import_Abstract
{

    const RATE_PRECISION = 5;

    protected $_url = 'http://data.fixer.io/api/latest';
    protected $_messages = array();

    /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    /**
     * _getConfigAccessKey
     *
     * @return bool|mixed
     */
    protected function _getConfigAccessKey()
    {
        if ($accessKey = Mage::getStoreConfig('currency/fixerio/access_key')) {
            return $accessKey;
        }

        $this->_messages[] = Mage::helper('directory')
            ->__('Fixer.io access key missing.  Please obtain access key from fixer.io.');

        return false;
    }

    /**
     * _getApiUrl
     *
     * @return string
     */
    protected function _getApiUrl()
    {
        if (!$this->_getConfigAccessKey()) {
            return false;
        }

        return $this->_url . '?access_key=' . $this->_getConfigAccessKey() . '&symbols=%1$s,%2$s';
    }

    /**
     * _convert
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int    $retry
     *
     * @return float|null|string
     */
    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {

        if (!$url = sprintf($this->_getApiUrl(), $currencyFrom, $currencyTo)) {
            return null;
        }

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/fixerio/timeout')))
                ->request('GET')
                ->getBody();

            $converted = Mage::helper('core')->jsonDecode($response);

            if (isset($converted['success'])) {
                if (!$converted['success']) {
                    $this->_messages[] =
                        Mage::helper('directory')->__('Api Returned Error: %s', $converted['error']['info']);
                    Mage::throwException($converted['error']['info']);
                }

                $rates = $converted['rates'];
                if (isset($rates[$currencyTo], $rates[$currencyFrom])) {
                    $rate = round($rates[$currencyTo] / $rates[$currencyFrom], self::RATE_PRECISION);

                    // test for bcmath to retain precision
                    if (function_exists('bcadd')) {
                        return bcadd($rate, '0', 12);
                    }

                    return (float) $rate;
                }

                Mage::throwException('Error fetching currency rates from API response');
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($retry === 0) {
                return $this->_convert($currencyFrom, $currencyTo, 1);
            }

            $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
            return null;
        }

        return null;
    }

}
