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
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Admin;

/**
 * Search
 *
 * @see   ____class_see____
 * @since 1.0.0
 */
class FeaturedProducts extends \XLite\View\ItemsList\Product\Admin\AAdmin
{
    /**
     * Widget param names
     */

    const PARAM_SUBSTRING         = 'substring';
    const PARAM_CATEGORY_ID       = 'categoryId';
    const PARAM_SKU               = 'sku';
    const PARAM_SEARCH_IN_SUBCATS = 'searchInSubcats';


    /**
     * Return search parameters
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Product::P_SUBSTRING         => self::PARAM_SUBSTRING,
            \XLite\Model\Repo\Product::P_CATEGORY_ID       => self::PARAM_CATEGORY_ID,
            \XLite\Model\Repo\Product::P_SKU               => self::PARAM_SKU,
            \XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS => self::PARAM_SEARCH_IN_SUBCATS,
        );
    }


    /**
     * Return title
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getHead()
    {
        return 'Search result';
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getListName()
    {
        return 'itemsList.product.admin.featured';
    }

    /**
     * Define view list
     *
     * @param string $list List name
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineViewList($list)
    {
        $result = parent::defineViewList($list);

        if ($this->getListName() . '.footer' === $list) {
            $result[] = $this->getWidget(array('label' => 'Add featured products'), '\XLite\View\Button\Submit');
        }

        return $result;
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Return list of the modes allowed by default
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultModes()
    {
        $list = parent::getDefaultModes();
        $list[] = 'search_featured_products';

        return $list;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Admin\Product\Search';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getCommonParams()
    {
        return parent::getCommonParams() + array('mode' => 'search_featured_products');
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_SUBSTRING => new \XLite\Model\WidgetParam\String(
                'Substring', ''
            ),
            self::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\Int(
                'Category ID', 0
            ),
            self::PARAM_SKU => new \XLite\Model\WidgetParam\String(
                'SKU', ''
            ),
            self::PARAM_SEARCH_IN_SUBCATS => new \XLite\Model\WidgetParam\Checkbox(
                'Search in subcategories', 0
            ),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge(
            $this->requestParams,
            \XLite\View\ItemsList\Product\Admin\Search::getSearchParams()
        );
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (\XLite\View\ItemsList\Product\Admin\Search::getSearchParams() as $modelParam => $requestParam) {
            $result->$modelParam = $this->getParam($requestParam);
        }

        if (empty($result->{self::PARAM_CATEGORY_ID})) {
            unset($result->{self::PARAM_CATEGORY_ID});
            unset($result->{self::PARAM_SEARCH_IN_SUBCATS});
        }

        return $result;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Product')->search($cnd, $countOnly);
    }
}
