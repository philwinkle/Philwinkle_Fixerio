<?php
/**
 * Fixer.io Import Rates
 *
 */

/**
 * Philwinkle_Fixerio_Model_Import class
 *
 * @category    Philwinkle
 * @package     Philwinkle_Fixerio
 */
class Philwinkle_Fixerio_Model_Import extends Philwinkle_Fixerio_Model_Base
{
    /**
     * XML path to Fixer.IO timeout setting
     */
    const XML_PATH_FIXERIO_TIMEOUT = 'currency/fixerio/timeout';

    /**
     * XML path to Fixer.IO API key setting
     */
    const XML_PATH_FIXERIO_API_KEY = 'currency/fixerio/api_key';

    protected $_url = 'http://data.fixer.io/api/latest';
    protected $_messages = [];

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
     * getEndpointUrl
     *
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->_url;
    }

    public function fetchRates()
    {
        //Make sure to disable Magento's implementation by invoking the implementation in the abstract class
        return Mage_Directory_Model_Currency_Import_Abstract::fetchRates();
    }

    /**
     * _convert
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int    $retry
     *
     * @return float|null
     */
    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {
        $queryParams = array(
            'access_key' => $this->_getConfigAccessKey(),
            'symbols'    => implode(',', array($currencyFrom, $currencyTo))
        );

        if (!$queryParams['access_key']) {
            return null;
        }

        try {
            $url = Mage::helper('core/url')->addRequestParam($this->getEndpointUrl(), $queryParams);

            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig(self::XML_PATH_FIXERIO_TIMEOUT)))
                ->request('GET')
                ->getBody();

            /** Second parameter is objectDecodeType - Zend_Json::TYPE_ARRAY, or Zend_Json::TYPE_OBJECT */
            $converted = Mage::helper('core')->jsonDecode($response, Zend_Json::TYPE_ARRAY);

            if (isset($converted['success'])) {
                if (!$converted['success']) {
                    $this->_messages[] = Mage::helper('directory')->__('Api Error: %s', $converted['error']['info']);
                    Mage::throwException($converted['error']['info']);
                }

                if (isset($converted['rates']) && $rates = $converted['rates']) {
                    if (isset($rates[$currencyTo], $rates[$currencyFrom])) {
                        $rate = $rates[$currencyTo] / $rates[$currencyFrom];

                        // test for bcmath to retain precision
                        if (function_exists('bcadd')) {
                            return bcadd($rate, '0', 12);
                        }

                        return (float) $rate;
                    }
                }

                Mage::throwException('Error fetching currency rates from API response');
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($retry === 0) {
                return $this->_convert($currencyFrom, $currencyTo, $retry + 1);
            }

            $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
        }

        return null;
    }

    /**
     * _getConfigAccessKey
     *
     * @return bool|mixed
     */
    protected function _getConfigAccessKey()
    {
        $accessKey = Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_FIXERIO_API_KEY));
        if ($accessKey) {
            return $accessKey;
        }

        $this->_messages[] = Mage::helper('directory')
            ->__('Fixer.io access key missing. Please obtain it from fixer.io.');

        return false;
    }
}
