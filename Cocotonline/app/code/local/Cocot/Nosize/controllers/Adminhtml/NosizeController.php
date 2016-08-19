<?php

class Cocot_Nosize_Adminhtml_NosizeController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()->renderLayout();
    }
    
    public function exportCsvAction() {
        $fileName   = 'nosize_'.date('Y-m-d_H-i-s').'.csv';
        $grid       = $this->getLayout()->createBlock('nosize/adminhtml_item_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function newAction() {
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }

    public function editAction() {
        $this->_initAction();

        // Get id if available
        $id = $this->getRequest()->getParam('nosize_id');
        $model = Mage::getModel('nosize/item');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getNosizeId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This baz no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getNosizeId() ? $model->getName() : $this->__('New Baz'));

        $data = Mage::getSingleton('adminhtml/session')->getCocotData(true);
        if (!empty($data)) {
            $model->setData($data);
            
            var_dump($data);
        }

        Mage::register('cocot_nosize', $model);

        $this->_initAction()
                ->_addBreadcrumb($id ? $this->__('Edit Baz') : $this->__('New Baz'), $id ? $this->__('Edit Baz') : $this->__('New Baz'))
                ->_addContent($this->getLayout()->createBlock('nosize/adminhtml_item_edit')->setData('action', $this->getUrl('*/*/save')))
                ->renderLayout();
    }

    public function saveAction() {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('nosize/item');
            $model->setData($postData);
            
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Item has been saved.'));
                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this Item.'));
            }

            Mage::getSingleton('adminhtml/session')->setNosizeData($postData);
            $this->_redirectReferer();
        }
    }

    public function messageAction() {
        $data = Mage::getModel('nosize/item')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction() {
        $this->loadLayout()
                // Make the active menu match the menu config nodes (without 'children' inbetween)
                ->_setActiveMenu('cocot/nosize_item')
                ->_title($this->__('Cocot Nosize'))->_title($this->__('Items'))
//                ->_addBreadcrumb($this->__('Cocot'), $this->__('Cocot'))
//                ->_addBreadcrumb($this->__('Baz'), $this->__('Baz'));
        ;
        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed() {
        return true; //Mage::getSingleton('admin/session')->isAllowed('cocot/nosize_item');
    }

}
