<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URL: http://www.litecommerce.com/license.php                |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as "THE |
| AUTHOR")  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE "SOFTWARE"). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  "YOU")  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Ruslan R. Fazliev              |
| Portions created by Ruslan R. Fazliev are Copyright (C) 2003 Creative        |
| Development. All Rights Reserved.                                            |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* @package GoogleCheckout
* @access public
* @version $Id$
*/
class XLite_Module_GoogleCheckout_Model_ShippingRate extends XLite_Model_ShippingRate implements XLite_Base_IDecorator
{
	function getGoogleCheckoutCurrency()
	{
		return $this->xlite->get("gcheckout_currency");
	}

	function getGoogleCheckoutXML()
	{
		require_once LC_MODULES_DIR . 'GoogleCheckout' . LC_DS . 'encoded.php';

		// Shipping method name
		$str = $this->getComplex('shipping.name');
		$value = strval($str);
		$value = str_replace("\n", " ", $value);
		$value = str_replace("\r", " ", $value);
		$value = str_replace("\t", " ", $value);
		$valueLength = strlen($value);
		$newValue = "";
		for ($i=0; $i<$valueLength; $i++) {
			$symbol = $value{$i};
			$symbolCode = ord($symbol);
			if (($symbolCode>=0 && $symbolCode<=31) || $symbolCode>=127) {
				$newValue .= "&#" . sprintf("%02d", $symbolCode) . ";";
			} else {
				$newValue .= $symbol;
			}
		}
	    $shippingName = htmlspecialchars($newValue);

		$shippingPrice = sprintf("%.02f", doubleval($this->xlite->config->getComplex('GoogleCheckout.default_shipping_cost')));
		$currency = $this->getGoogleCheckoutCurrency();

		return <<<EOT
                <merchant-calculated-shipping name="$shippingName">
                    <price currency="$currency">$shippingPrice</price>
					<address-filters> 
						<allowed-areas> 
							<world-area /> 
						</allowed-areas> 
					</address-filters> 
				</merchant-calculated-shipping>
EOT;
	}
}

// WARNING:
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
