<?php

class Philwinkle_Fixerio_Model_Import extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_url = 'http://api.fixer.io/latest?base={{CURRENCY_FROM}}&symbols={{CURRENCY_TO}}';
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

    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/fixerio/timeout')))
                ->request('GET')
                ->getBody();


            $converted = json_decode($response);

            $xml = new SimpleXmlElement("<double>{$converted->rates->$currencyTo}</double>");

            if( !$xml ) {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
                return null;
            }
            return (float) $xml;
        }
        catch (Exception $e) {
            if( $retry == 0 ) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
            }
        }
    }
}

