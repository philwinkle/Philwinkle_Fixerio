<?php

//For Magento 1.9.4.3+ this model will extend Magento's implementation, which in turn extends the abstract class
//For older Magento versions, this model extends the abstract class directly, as there is no Magento implementation
//to extend
if (class_exists('Mage_Directory_Model_Currency_Import_Fixerio')) {
    abstract class Philwinkle_Fixerio_Model_Base extends Mage_Directory_Model_Currency_Import_Fixerio {}
} else {
    abstract class Philwinkle_Fixerio_Model_Base extends Mage_Directory_Model_Currency_Import_Abstract {}
}
