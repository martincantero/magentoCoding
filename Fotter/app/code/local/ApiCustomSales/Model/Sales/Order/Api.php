<?php


/**
 * Sales - Order api
 *
 * @category   Fotter
 * @package    Fotter_ApiCustomSales
 * @copyright  www.fotter.com.ar
 * @author     Fotter Core Team <martin.cantero@fotter.com.ar>
 */

class Fotter_ApiCustomSales_Model_Sales_Order_Api extends Mage_Sales_Model_Order_Api
{

    /**
     * Retrieve full order information
     * @info: adding DNI customer attribute
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage()
            );
        }

        $result = $this->_getAttributes($order, 'order');
        
        //Getting customer attribute DNI
        try {
            if ($order->getCustomerIsGuest()){              
                $accountData = $this->getCustomerAccountData($customerId=null, $result['store_id'], $result['order_id'] );
                if ($accountData && is_array($accountData)) {                                    
                   $arrlength = count($accountData);
                    for($x = 0; $x < $arrlength; $x++) {
                        if ($accountData[$x]['label'] == 'DNI') {
                           $customerAttributeDni = $accountData[$x]['value'];    
                        }                                              
                    } 
                }                                
            }else {
                $customerObj  = Mage::getModel('customer/customer')->load($result['customer_id']);
                $customerAttributeDni = $customerObj->getDni();
            }
            
            $result['dni'] = $customerAttributeDni;
            
        } catch (Exception $e) {
            Mage::logException($e);
        }
     
        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');       
        $result['items'] = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        return $result;
    }
    
    
    /*
     * Getting DNI for customer guest account information
     * 
     */
    public function getCustomerAccountData($customerId, $storeId, $orderId)
    {
        if(null == $customerId){   
            $storage = Mage::getModel('amcustomerattr/guest')->load($orderId,'order_id');
        } else {
            $storage   = Mage::getModel('customer/customer')->load($customerId);
        }
        
        $attributes = Mage::getModel('customer/attribute')->getCollection();

        $alias = Mage::helper('amcustomerattr')->getProperAlias($attributes->getSelect()->getPart('from'), 'eav_attribute');
        $attributes->addFieldToFilter($alias . 'is_user_defined', 1);
        $attributes->addFieldToFilter($alias . 'entity_type_id', Mage::getModel('eav/entity')->setType('customer')->getTypeId());
        
        $alias = Mage::helper('amcustomerattr')->getProperAlias($attributes->getSelect()->getPart('from'), 'customer_eav_attribute');
        $attributes->addFieldToFilter($alias . 'on_order_view', 1);
        $attributes->getSelect()->order($alias . 'sorting_order');
        $accountData = array();
        foreach ($attributes as $attribute)
        {
            $label       = $attribute->getFrontend()->getLabel();
            $value       = '';
            $currentData = '';
            if ($inputType = $attribute->getFrontend()->getInputType())
            {;
                $currentData = $storage->getData($attribute->getAttributeCode());
            }

            if ($inputType == 'select' || $inputType == 'selectimg' || $inputType == 'multiselect' || $inputType == 'multiselectimg') 
            {
                // getting values translations
                $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($attribute->getId())
                    ->setStoreFilter($storeId, false)
                    ->load();
                foreach ($valuesCollection as $item) {
                    $values[$item->getId()] = $item->getValue();
                }
                // applying translations
                $options = $attribute->getSource()->getAllOptions(false, true);
                foreach ($options as $i => $option)
                {
                    if (isset($values[$option['value']]))
                    {
                        $options[$i]['label'] = $values[$option['value']];
                    }
                }
                // applying translations

                if (false !== strpos($inputType, 'multi'))
                {
                    $currentData = explode(',', $currentData);
                    foreach ($options as $option)
                    {
                        if (in_array($option['value'], $currentData))
                        {
                            $value .= $option['label'] . ', ';
                        }
                    }
                    if ($value)
                    {
                        $value = substr($value, 0, -2);
                    }
                } else 
                {
                    foreach ($options as $option)
                    {
                        if ($option['value'] == $currentData)
                        {
                            $value = $option['label'];
                        }
                    }
                }

            } elseif ($inputType == 'date') {
                $format = Mage::app()->getLocale()->getDateFormat(
                    Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
                );
                $value = Mage::getSingleton('core/locale')->date($currentData, Zend_Date::ISO_8601, null, false)->toString($format);
            } elseif ($inputType == 'boolean') {
                $value = $currentData ? 'Yes' : 'No';
            } elseif ('file' == $attribute->getTypeInternal()) {
                if ($currentData) {
                    $downloadUrl = Mage::helper('amcustomerattr')->getAttributeFileUrl($currentData, true);
                    $fileName = Mage::helper('amcustomerattr')->cleanFileName($currentData);
                    $value = '<a href="'. $downloadUrl .'">' . $fileName[3] . '</a>';
                } else {
                    $value = 'No Uploaded File';
                }
            } else {
                $value = $currentData;
            }

            if ($value) {
                $accountData[] = array('label' => $label, 'value' => $value);
            }
        }

        return $accountData;
    }        

} 
