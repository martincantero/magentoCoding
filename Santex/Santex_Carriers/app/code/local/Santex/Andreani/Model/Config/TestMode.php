<?php
/**
 * Santex_Andreani
 *
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Model_Config_TestMode
{

   /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label'=>Mage::helper('adminhtml')->__('Habilitado')),
            array('value' => '0', 'label'=>Mage::helper('adminhtml')->__('Deshabilitado')),
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
            '1' => Mage::helper('adminhtml')->__('Habilitado'),
            '0' => Mage::helper('adminhtml')->__('Deshabilitado'),
        );
    }

}
