<?php
/**
 * Santex_Andreani
 *
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Model_Config_Medida
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'gramos', 'label'=>Mage::helper('adminhtml')->__('gramos / cm3')),
            array('value' => 'kilos', 'label'=>Mage::helper('adminhtml')->__('kg / m3')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'gramos' => Mage::helper('adminhtml')->__('gramos / cm3'),
            'kilos' => Mage::helper('adminhtml')->__('kg / m3'),
        );
    }

}
