<?php
/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Adminhtml_PedidosController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_title($this->__('Andreani'))->_title($this->__('Estado de pedidos de Andreani'));
        $this->loadLayout();
        $this->_setActiveMenu('andreani/andreani');
        $this->_addContent($this->getLayout()->createBlock('andreani/adminhtml_pedidos'));
        $this->renderLayout();
    }

    public function gridAction()
    {
       $this->_title($this->__('Andreani'))->_title($this->__('Estado de pedidos'));
        $this->loadLayout();
        $this->_setActiveMenu('andreani/andreani');
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('andreani/adminhtml_pedidos_grid')->toHtml()
        );
    }

    public function massEliminarAction()
    {
       $ids = $this->getRequest()->getParam('id');
       if(!is_array($ids)) {
           Mage::getSingleton('adminhtml/session')->addError(Mage::helper('andreani')->__('Por favor seleccionar una orden!'));
       } else {
           try {
              foreach ($ids as $id) {
                 //Mage::getModel('andreani/order')->load($id)->delete();
                  Mage::getModel('andreani/order')->load($id)->setData("estado","Eliminada")->save();
              }
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('andreani')->__('Se han eliminado %d registro(s).', count($ids)));
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
          }
       }
       $this->_redirect('*/*/index');
    }

    public function massEntregadoAction()
    {
       $ids = $this->getRequest()->getParam('id');

       if(!is_array($ids)) {
           Mage::getSingleton('adminhtml/session')->addError(Mage::helper('andreani')->__('Por favor seleccionar una orden!'));
       } else {
          try {
              date_default_timezone_set('America/Argentina/Buenos_Aires');
              $date = date('d/m/Y h:i:s A', time());
              foreach ($ids as $id) {
                 Mage::getModel('andreani/order')->load($id)->setData("entrega",$date)->save();
                  Mage::getModel('andreani/order')->load($id)->setData("estado","Entregado")->save();
              }
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('andreani')->__('Se han actualizado %d registro(s).', count($ids)));
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
          }
        }
       $this->_redirect('*/*/index');
    }

    public function massPendienteAction()
    {
       $ids = $this->getRequest()->getParam('id');
       if(!is_array($ids)) {
           Mage::getSingleton('adminhtml/session')->addError(Mage::helper('andreani')->__('Por favor seleccionar una orden!'));
       } else {
          try {
              foreach ($ids as $id) {
                  Mage::getModel('andreani/order')->load($id)->setData("estado","Pendiente")->save();
              }
              Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('andreani')->__('Se han actualizado %d registro(s).', count($ids)));
          } catch (Exception $e) {
              Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
          }
        }
        $this->_redirect('*/*/index');
    }

    public function viewAction()
    {
        $helper = Mage::helper('andreani/data');

        $id = (int) $this->getRequest()->getParam('id');

        // 1. Traemos los datos de la tabla "andreani_order" según el OrderId[0] y asignarla a $datos
       $collection = Mage::getModel('andreani/order')->getCollection()
            ->addFieldToFilter('id', $id);
        $collection->getSelect()->limit(1);

        if (!$collection) {
            $helper->_getLogger()->saveLog("Andreani :: no existe la orden en la tabla andreani_order.", $helper->_process_name);
            return;
        }
        foreach($collection as $thing) {
           $datos = $thing->getData();
       }

        if ( $datos['cod_tracking'] != '' ) {
            try {
                /*
				$options = array(
					'soap_version' 	=> SOAP_1_2,
					'exceptions'	=> 1,
					'trace' 		=> 1,
					'style' 		=> SOAP_DOCUMENT,
					'encoding'		=> SOAP_LITERAL,
				);

				$optRequest = array();
				$optRequest["ObtenerTrazabilidad"] = array(
					'Pieza' => array(
						'NroPieza'		=> '',
						'NroAndreani'	=> $datos['cod_tracking'],
						'CodigoCliente'	=> $datos['cliente']
					));
				$client = new SoapClient($url, $options);
				$request = $client->__soapCall("ObtenerTrazabilidad", $optRequest);
*/
                $request = $helper->getSoapClientTrazabilidad($datos, Santex_Andreani_Helper_Data::WS_TIPO_TRAZABILIDAD , $this->_process_name);


                $texto	=  $request->Pieza->NroPieza . "\n";
                $texto .= "Nombre del Envio: " . $request->Pieza->Envios->Envio->NombreEnvio .  "\n";
                $texto .= "Código de tracking: " . $request->Pieza->Envios->Envio->NroAndreani . "\n";
                $texto .= "Fecha de alta: " . $request->Pieza->Envios->Envio->FechaAlta . "\n";

                foreach( $request->Pieza->Envios->Envio->Eventos as $indice => $valor )
                {
                    $texto .= "Eventos: " . "\n\n";
                    $texto .= "Fecha del evento: " . $valor->Fecha . "\n";
                    $texto .= "Estado del envio: " . $valor->Estado . "\n";
                    $texto .= "Motivo: " . $valor->Motivo . "\n";
                    $texto .= "Sucursal: " . $valor->Sucursal . "\n";
                    $texto .= "------------------ \n";
                }

                Mage::getModel('andreani/order')->load($id)->setData("tracking",$texto)->save();

            } catch (SoapFault $e) {
                $helper->_getLogger()->saveLog(print_r($e,true), $helper->_process_name);
            }
        } else {
            $texto =  "El envío se encuentra pendiente. Diríjase a 'Ventas->Pedidos' para dar comienzo al proceso cuando el mismo se haya realizado";
            Mage::getModel('andreani/order')->load($id)->setData("tracking",$texto)->save();
		}

        if ($id) {
            $order = Mage::getModel('andreani/order')->load($id);
            if (!$order || !$order->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('andreani')->__('No se encontró el ID de la orden'));
                $this->_redirect('*/*/');
            }
        }
        
        Mage::register('order_data', $order);
 
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('andreani/adminhtml_pedidos_edit');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('andreani/order');
            $model->setData($data)->setId($this->getRequest()->getParam('id'));
            $model->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('andreani')->__('El pedido fue editado con éxito.'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);
        }

        $this->_redirect('*/*/');
    }

    public function getConstanciaAction() {

        $helper = Mage::helper('andreani/data');

        $id = $this->getRequest()->getParam('id');
        $order = Mage::getModel('andreani/order')->load($id);

        $datos = $order->getData();

        try {

            $client = $helper->getSoapClient($datos, Santex_Andreani_Helper_Data::WS_TIPO_CONFIRMAR , $helper->_process_name);

            $constanciaResponse = $client->ImprimirConstancia(array(
                    'entities' =>array(
                                'ParamImprimirConstancia' =>array(
                                        'NumeroAndreani' => $datos['cod_tracking']
                                    ))));
            $ConstanciaURL = $constanciaResponse->ImprimirConstanciaResult->ResultadoImprimirConstancia->PdfLinkFile;

            $this->_redirectUrl($ConstanciaURL);

            Mage::getModel('andreani/order')->load($id)->setData('constancia',$ConstanciaURL)->save();

        } catch (SoapFault $e) {

            $helper->_getLogger()->saveLog("Error: " . $e, $helper->_process_name);

            Mage::getSingleton('adminhtml/session')->addError('Error Andreani: '.$e->getMessage().' - Por favor vuelva a intentar en unos minutos.');
            $this->_redirect('*/*/index');
        }

    }

}
?>