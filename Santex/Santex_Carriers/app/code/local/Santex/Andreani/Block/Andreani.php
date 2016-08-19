<?php
/**
 * Santex_Andreani
 *
 * @category   Santex
 * @package    Santex_Andreani
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Andreani_Block_Andreani
    extends Mage_Core_Block_Abstract
    implements Mage_Widget_Block_Interface
{

    protected function _toHtml()
    {
		$html ='';
        $html .= 'andreani parameter1 = '.$this->getData('parameter1').'<br/>';
        $html .= 'andreani parameter2 = '.$this->getData('parameter2').'<br/>';
        $html .= 'andreani parameter3 = '.$this->getData('parameter3').'<br/>';
        $html .= 'andreani parameter4 = '.$this->getData('parameter4').'<br/>';
        $html .= 'andreani parameter5 = '.$this->getData('parameter5').'<br/>';
        return $html;
    }
	
}
?>