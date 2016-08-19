<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Epak_Webservice extends Santex_Oca_Model_Epak_Webservice_Request
{
    public function updateBranches() {
        $response = $this->makeSoapCall('GetCentrosImposicion');
        $this->processBranchResult($response->getObject());
    }

    public function tarifarEnvioCorporativo($params) {
        $response = $this->makeSoapCall('Tarifar_Envio_Corporativo', $params);
        $result = $response->getObject();
        if (!isset($result->Total)) return null;
        return $result;
    }

    public function getCentrosImposicionPorCP($cp) {
        $hideDisabled = Mage::getStoreConfig('oca/settings/hide_disabled_branch');
        $params = array(
            'CodigoPostal' => $cp
        );
        $response = $this->makeSoapCall('GetCentrosImposicionPorCP', $params);
        $result = $response->getArray();
        if(!$result || count($result) < 1) return array();
        $return = array();
        foreach ($result as $row) {
            $branch = Mage::getModel('oca/branch')->load($row->idCentroImposicion, 'code');
            //If the branch is not in the DB, we add it
            if (!$branch->getId()) $branch->saveFromWsRow($row);
            if (!$hideDisabled || $branch->getActive()) {
                $return[] = $branch;
            }
        }
        return $return;
    }

    public function getShipmentTracking($tracking) {
        $params = array('Pieza' => $tracking);
        $response = $this->makeSoapCall('Tracking_Pieza', $params);
        return $response->getArray();
    }

    public function getShipmentLastStatus($tracking) {
        $params = array('Pieza' => $tracking);
        $response = $this->makeSoapCall('Tracking_Pieza', $params);
        return trim($response->getLastRow()->Desdcripcion_Estado);
    }

    public function getShipmentDate($tracking) {
        $params = array('Pieza' => $tracking);
        $response = $this->makeSoapCall('Tracking_Pieza', $params);
        $date = new DateTime(trim($response->getFirstRow()->fecha));
        return $date->format('d/m/Y');
    }

    private function processBranchResult($rows) {
        foreach ($rows as $row) {
            $branch = Mage::getModel('oca/branch')->loadByCodeOrNew($row->idCentroImposicion);
            $branch->saveFromWsRow($row);
        }
    }
}
