<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 * 
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage View
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\View\Form\Address;

/**
 * Profile abstract form
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Address extends \XLite\View\Form\AForm
{
    /**
     * Current form name
     *
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getFormName()
    {
        return 'address_form';
    }   

    /**
     * getDefaultParams 
     * 
     * @return array
     * @access protected
     * @since  3.0.0
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        $result['target'] = \XLite\Core\Request::getInstance()->target;
        $result['action'] = 'save';
        
        $addressId = static::getCurrentForm()->getRequestAddressId();

        if ($addressId) {
            $result['address_id'] = $addressId;
        
        } else {

            $profileId = static::getCurrentForm()->getRequestProfileId();

            if ($profileId) {
                $result['profile_id'] = $profileId;
            }
        }

        return $result;
    }

    /**
     * getDefaultClassName
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getDefaultClassName()
    {
        return 'address-form';
    }
}