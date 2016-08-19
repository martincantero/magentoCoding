<?php
/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Adminhtml_Tracking_PedidosController extends Mage_Adminhtml_Controller_Action
{
 
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('andreani/pedidos');
        $this->_addContent($this->getLayout()->createBlock('andreani/adminhtml_pedidos'));
        $this->renderLayout();
    }
}
?>