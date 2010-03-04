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
 * @subpackage ____sub_package____
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

/**
 * Products list page widget
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class XLite_View_ProductsListPage extends XLite_View_Abstract
{
    /**
     * Display modes 
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $displayModes = array(
        'grid'  => 'Grid',
        'list'  => 'List',
        'table' => 'Table',
    );

    /**
     * Default display mode 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected static $defaultDisplayMode = 'grid';

    /**
     * Grid columns count
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $gridColumns = 3;

    /**
     * Widget template 
     * 
     * @var    string
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $template = 'products_list/grid/body.tpl';

    /**
     * Constructor
     * 
     * @param array $attributes widget params
     *
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function init(array $attributes = array())
    {
        $this->attributes['data'] = array();
        $this->attributes['displayMode'] = self::getDefaultDisplayMode();
        $this->attributes['widgetArguments'] = array();

        parent::init($attributes);
    }

    /**
     * Set properties
     *
     * @param array $attributes params to set
     *
     * @return void
     * @access public
     * @since  3.0.0 EE
     */
    public function setAttributes(array $attributes)
    {
        if (isset($attributes['widgetArguments']) && !is_array($attributes['widgetArguments'])) {
            unset($attributes['widgetArguments']);
        }

        $displayModes = self::getDisplayModes();
        if (isset($attributes['displayMode']) && !isset($displayModes[$attributes['displayMode']])) {
            unset($attributes['displayMode']);
        }

        parent::setAttributes($attributes);
    }

    /**
     * Initialization
     *
     * @return string
     * @access protected
     * @since  3.0.0 EE
     */
    protected function initView()
    {
        parent::initView();

        // Normalize widgetArguments cell
        if (!is_array($this->attributes['widgetArguments'])) {
            $this->attributes['widgetArguments'] = array();
        }

        // Define widget template
        $displayModes = self::getDisplayModes();
        $displayMode = $this->attributes['displayMode'];
        if (
            isset($this->attributes['widgetArguments']['displayMode'])
            && isset($this->attributes['widgetArguments']['displayModeChangable'])
            && !$this->attributes['widgetArguments']['displayModeChangable']
            && $this->attributes['widgetArguments']['displayMode']
            && isset($displayModes[$this->attributes['widgetArguments']['displayMode']])
        ) {
            $displayMode = $this->attributes['widgetArguments']['displayMode'];
        }

        $this->template = 'products_list/' . $displayMode . '/body.tpl';
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     * @access protected
     * @since  3.0.0 EE
     */
    public function isVisible()
    {
        return parent::isVisible()
            && is_array($this->attributes['data'])
            && $this->attributes['data'];
    }

	/**
	 * Get data 
	 * 
	 * @return array
	 * @access public
	 * @see    ____func_see____
	 * @since  3.0.0
	 */
	public function getData()
	{
		return $this->attributes['data'];
	}

    /**
     * Get grid item width (percent)
     * 
     * @return integer
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getGridItemWidth()
    {
        $gridColumns = $this->gridColumns;
        if (isset($this->attributes['widgetArguments']['gridColumns'])) {
            $gridColumns = min(5, max(1, intval($this->attributes['widgetArguments']['gridColumns'])));
        }

        return floor(100 / $gridColumns) - 6;
    }

    /**
     * Get display modes 
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    static public function getDisplayModes()
    {
        return self::$displayModes;
    }

    /**
     * Get default display mode
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    static public function getDefaultDisplayMode()
    {
        return self::$defaultDisplayMode;
    }

    /**
     * Check - show product description or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isShowDescription()
    {
        return !isset($this->attributes['widgetArguments']['showDescription'])
            || $this->attributes['widgetArguments']['showDescription'];
    }

    /**
     * Check - show product price or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isShowPrice()
    {
        return !isset($this->attributes['widgetArguments']['showPrice'])
            || $this->attributes['widgetArguments']['showPrice'];
    }

    /**
     * Check - show Add to cart button or not
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isShowAdd2Cart()
    {
        return !isset($this->attributes['widgetArguments']['showAdd2Cart'])
            || $this->attributes['widgetArguments']['showAdd2Cart'];
    }

    /**
     * Check - allow multiple additions at once or not 
     * 
     * @return boolean
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function isMultipleAdd2Cart()
    {
        return $this->isShowAdd2Cart()
            && isset($this->attributes['widgetArguments']['multipleAdd2Cart'])
            && $this->attributes['widgetArguments']['multipleAdd2Cart'];
    }

}
