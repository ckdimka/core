{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Item Buy now button
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="productsList.listItem.body", weight="40")
 *}
<widget class="XLite_View_BuyNow" product="{product}" IF="isShowAdd2Cart(product)" style="aux-button add-to-cart" />
