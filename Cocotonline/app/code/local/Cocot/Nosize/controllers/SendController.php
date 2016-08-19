<?php
class Cocot_Nosize_SendController extends Mage_Core_Controller_Front_Action 
{
    
    //public function emailAction() {
        //Mage::getModel('nosize/item')->sendEmail();
    //}
    
    public function indexAction()
    {
        $post = $this->getRequest()->getPost();
        /*if (!isset($post['simple_id'])) {
            return false;
        }*/
        $model = Mage::getModel('nosize/item');
        $simple = Mage::getModel('catalog/product')->load($post['product_id']);
        $simpleProduct= $pieces = explode("|", $post['size']);

        $data = array(
            'email'         => $post['email'],
            'name'          => $post['name'],
            'simple_id'     => $simpleProduct[0],
            'size'          => $simpleProduct[1],
            'product_id'    => $post['product_id'],
            'product_sku'   => $post['product_sku'],
            'product_name'  => $post['product_name'],
            'product_url'   => $post['product_url'],
            'date'          => date('Y-m-d H:i:s')
        );       

        $is_registered = (bool) Mage::getModel('nosize/item')->getCollection()
                            ->addFieldToFilter('sent', 0)
                            ->addFieldToFilter('email', $data['email'])
                            ->addFieldToFilter('simple_id', $data['product_id'])
                            ->count();

        if (!$is_registered) {
            $model->setData($data);
            try {
                //$insertId = 
                $model->save()->getId();
                $save_flag = true;
            } catch (Exception $e) {
                $save_flag = false;
                //echo $e->getMessage();
            }   
        } else {
            $save_flag = true;
        }
        if ($save_flag) {
            $this->_redirect('*/*/success');
        } else {
            $this->_redirect('*/*/failure');
        }
    }
    
    public function getformAction() {
        $params = $this->getRequest()->getParams();
        Mage::register('nosizeProductId', $params['id']);

        echo $this->getLayout()
                ->createBlock('nosize/form')
                ->setTemplate('cocot/nosize/form.phtml')
                ->setProductId($params['id'])
                ->toHtml();
    }
    
    public function successAction() {
        echo $this->getLayout()
                ->createBlock('core/template')
                ->setTemplate('cocot/nosize/success.phtml')
                ->toHtml();
    }
    
    public function failureAction() {
        echo $this->getLayout()
                ->createBlock('core/template')
                ->setTemplate('cocot/nosize/failure.phtml')
                ->toHtml();
    }

}
