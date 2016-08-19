<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Branch extends Mage_Core_Model_Abstract
{

    public function _construct() {
        parent::_construct();
        $this->_init('oca/branch');
    }

    public function loadByCodeOrNew($code) {
        $this->load($code, 'code');
        if (!$this->getId()) $this->setCode($code);
        return $this;
    }

    public function saveFromWsRow($row) {
        $this->trimAndSet('code', $row->idCentroImposicion, false);
        $this->trimAndSet('short_name', $row->Sigla, false);
        $this->trimAndSet('description', $row->Descripcion);
        $this->trimAndSet('address_street', $row->Calle);
        $this->trimAndSet('address_number', $row->Numero);
        $this->trimAndSet('address_floor', $row->Piso);
        $this->trimAndSet('city', $row->Localidad);
        $this->trimAndSet('zipcode', $row->codigopostal);
        $this->setActive(1);
        $this->save();

        return $this;
    }

    public function getFullDescription() {
        return $this->getDescription() . ' (' . $this->getFullAddress() . ', '. $this->getCity() . ' - ' . $this->getShortName() .') '; 
    }

    public function getFullAddress() {
        $return = $this->getAddressStreet() . ' ' . $this->getAddressNumber();
        if (strlen($this->getAddressFloor) > 0) {
            $return .= ' ' . $this->getAddressFloor();
        }
        return $return;
    }

    /**
     * OCA epak WS devuelve muchos valores con whitespaces, por lo que el model hace un trim antes de guardar
     * @param  string  $key
     * @param  string  $value
     * @param  boolean $ucwords Guardar el $value con las primeras letras mayusculas
     * @return Santex_Oca_Model_Branch
     */
    public function trimAndSet($key, $value, $ucwords=true) {
        $value = trim((string)$value);
        if ($ucwords) {
            $value = ucwords(strtolower($value));
        }
        return $this->setData($key, $value);
    }
}
