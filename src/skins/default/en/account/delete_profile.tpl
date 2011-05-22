{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Register form template
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *
 * @ListChild (list="customer.account.details.after", weight="100")
 *}

<widget IF="isLogged()" class="\XLite\View\Button\Link" label="Delete profile" location="{buildURL(#profile#,##,_ARRAY_(#mode#^#delete#))}" />
