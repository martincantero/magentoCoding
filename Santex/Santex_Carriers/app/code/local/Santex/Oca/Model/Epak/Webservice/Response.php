<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Model_Epak_Webservice_Response extends Varien_Object
{
    protected $responseString = '';

    public function setResponseString($string) {
        $this->responseString = $string;
        return $this;
    }

    /**
     * This method returns the simple_xml object loaded from the response.
     * Preferred option is getArray, because of some issues with simple_xml and multiple fields with the same tag.
     * @return SimpleXMLElement The "raw" simpleXMLElement object
     */
    public function getObject() {
        $data = $this->processResponse();
        return $data->Table;
    }

    /**
     * simple_xml has issues with tags with the same name (only returns the first one),
     * to fix this we convert the object to an array containing simple_xml objects.
     * Use this method when the response will have more than one row.
     * @return Array Array of simple_xml objects
     */
    public function getArray() {
        $data = $this->processResponse();
        $array = (array)$data;
        return array_pop($array); //We can use array_shift or array_pop, since the array has only one element
    }

    public function getFirstRow() {
        $array = $this->getArray();
        return array_shift($array);
    }

    public function getLastRow() {
        $array = $this->getArray();
        return array_pop($array);
    }

    protected function processResponse() {
        $dataSet = false;
        try {
            if (strlen($this->responseString) == 0) {
                return false;
            }
            $obj = simplexml_load_string($this->responseString);
            $dataSet = $obj->NewDataSet;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
        return $dataSet;
    }
}
