<?php
/**
 * Santex_Santex
 *
 * @category   Santex
 * @package    Santex_Santex
 * @copyright  Copyright (c) 2014 Santex Group. (http://santexgroup.com/)
 */

class Santex_Santex_Block_System_Config_Form_Fieldset_Santex extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);

        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'Santex_')) {
                $html .= $this->_getFieldHtml($element, $moduleName);
            }

        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _getFieldHtml($fieldset, $moduleName)
    {
        $configData = $this->getConfigData();
        $path = 'advanced/modules_disable_output/' . $moduleName;
        $data = isset($configData[$path]) ? $configData[$path] : array();

        $moduleKey = substr($moduleName, strpos($moduleName, '_') + 1);
        $ver = (Mage::getConfig()->getModuleConfig($moduleName)->version);
        $id = $moduleName;

        $string = '<a target="_blank"><img src="' . $this->getSkinUrl('santex/santex/images/module.png') . '" title="' . $this->__('Installed') . '"/></a>';
        $moduleName = "$string $moduleName";

        if ($ver) {
            $field = $fieldset->addField($id, 'label',
                                         array(
                                              'name' => 'field_name_here',
                                              'label' => $moduleName,
                                              'value' => $ver,
                                         ))->setRenderer($this->_getFieldRenderer());
            return $field->toHtml();
        }
        return '';

    }

}
