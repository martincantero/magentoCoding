<?php
/**
 *
 * Fotter_Apicustomprice_Model_Api_Resource_Customer
 *
 * @category   Fotter
 * @package    Fotter_Apicustomprice
 * @copyright  www.fotter.com.ar
 * @author     Fotter Core Team <martin.cantero@fotter.com.ar>
 * @info:      http://redmine.fotter.net/issues/8230 
 */
class Fotter_Apicustomprice_Model_Api_Resource_Customer extends Mage_Checkout_Model_Api_Resource_Customer
{


    /**
     * Prepare quote for guest checkout order submit
     * @Fix: setting email with getShippingAddress() model data
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Checkout_Model_Api_Resource_Customer
     */
    protected function _prepareGuestQuote(Mage_Sales_Model_Quote $quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getShippingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        return $this;
    }

 
}
