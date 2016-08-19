<?php

class Cocot_Nosize_Block_Form extends Mage_Core_Block_Template
{
    private $_product;
    private $_simples;
    
    public function __construct () 
    {
        parent::_construct();
    }
    
    public function getProduct() {
        if ($this->_product === null) {
            $this->_product = Mage::getModel('catalog/product')->load($this->getProductId());
        }
        return $this->_product;
    }
    
    public function getSimples() {
        if ($this->_simples === null) {
            $this->_simples = array();
            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $this->getProduct());

            foreach ($childProducts as $simple) {

                $shoe_size = $simple->getAttributeText('shoe_size');
                $accesorio_size = $simple->getAttributeText('accesorio_size');
                $indumentaria_size = $simple->getAttributeText('indumentaria_size');
                $bedding_size = $simple->getAttributeText('bedding_size');
                if ($shoe_size) {
                    $size = $shoe_size;
                }
                if ($accesorio_size) {
                    $size = $accesorio_size;
                }
                if ($indumentaria_size) {
                    $size = $indumentaria_size;
                }
                if ($bedding_size) {
                    $size = $bedding_size;
                }

                $this->_simples[] = array(
                    'id' => $simple->getId(),
                    'sku' => $simple->getSku(),
                    'size' => $size
                );

            }

        }
        return $this->_simples;
    }
    
    public function getSizes() {
        // Opcion 1
//        $_product = $this->getProduct();
//        $productAttributeOptions = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);
//        $attributeOptions = array();
//        foreach ($productAttributeOptions as $productAttribute) {
//            foreach ($productAttribute['values'] as $attribute) {
//                $attributeOptions[$productAttribute['attribute_code']][$attribute['value_index']] = $attribute['store_label'];
//            }
//        }
//        return $attributeOptions['shoe_size'];
        // Opcion 2
        $data = array();
        $childData = $this->getSimples();
        foreach ($childData as $item) {
            $data[] = $item['size'];
        }
        return $data;
    }

    public function getProductTypeBlock(Mage_Catalog_Model_Product $product)
    {
        if($product->getTypeId()=="configurable")
        {
            $blockViewType = Mage::app()->getLayout()->createBlock("Mage_Catalog_Block_Product_View_Type_Configurable");
            $blockViewType->setProduct($product);
        }
        return $blockViewType;
    }
}