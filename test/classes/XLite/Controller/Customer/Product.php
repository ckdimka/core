<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  "COPYRIGHT" |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
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
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Class description.
*
* @package Dialog
* @access public
* @version $Id$
*/
class XLite_Controller_Customer_Product extends XLite_Controller_Customer_Abstract
{	
    public $params = array("target", "product_id", "category_id");

    function init()
    {
		if (isset($_REQUEST["product_id"]) && empty($_REQUEST["product_id"])) {
            return $this->redirect("cart.php" . ((isset($_REQUEST["category_id"]) && !empty($_REQUEST["category_id"])) ? "?target=category&category_id=" . $_REQUEST["category_id"] : "" ));
		}
        parent::init();
    }

	function handleRequest()
	{
		if (!$this->isComplex('product.exists')) {
			if ($this->isComplex('category.exists') && $this->isComplex('category.enabled')) {
				 return $this->redirect("cart.php?target=category&category_id=".$this->getComplex('category.category_id'));
			}

			return $this->redirect("cart.php");
		}

		return parent::handleRequest();
	}

    function action_buynow()
    {
        $this->set("returnUrl", "cart.php?target=cart&action=add&product_id=$this->product_id&category_id=$this->category_id");
    }

    function getLocationPath()
    {
        if($this->getComplex('config.General.add_on_mode')){
            return array($this->getComplex('product.name') => $this->get("url"));
        }

        $result = array();
        $path = $this->getComplex('category.path');
        if (!is_null($path)) {
            foreach ($this->getComplex('category.path') as $category) {
                $name = $category->get("name");
                while (isset($result[$name])) {
                	$name .= " ";
                }
                $result[$name] = "cart.php?target=category&category_id=" . $category->get("category_id");
            }
        }    
        $name = $this->getComplex('product.name');
        while (isset($result[$name])) {
        	$name .= " ";
        }
        $result[$name] = $this->get("url");
        return $result;
    }

    // 'description' meta tag
	
	function getTitle()
	{
		return ($this->getComplex('product.meta_title') ? $this->getComplex('product.meta_title') : $this->getComplex('product.name'));
	}
	
    function getDescription()
    {
        $description = $this->getComplex('product.description');
        if (empty($description)) {
            $description = $this->getComplex('product.brief_description');
        }
		return $description;
    }

	function getMetaDescription()
	{
		$description = $this->getDescription();	
		return ($this->getComplex('product.meta_desc') ? $this->getComplex('product.meta_desc') : $description);
	}
    // 'keywords' meta tag
    function getKeywords()
    {
		return $this->getComplex('product.meta_tags');
    }

    function isAvailableForSale()
    {
        return true;
    }
}

// WARNING :
// Please ensure that you have no whitespaces / empty lines below this message.
// Adding a whitespace or an empty line below this line will cause a PHP error.
?>
