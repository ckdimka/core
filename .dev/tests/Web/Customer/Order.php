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
 * @package    Tests
 * @subpackage Web
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      1.0.0
 */

require_once __DIR__ . '/ACustomer.php';

class XLite_Web_Customer_Order extends XLite_Web_Customer_ACustomer
{
    /**
     * PHPUnit default function.
     * Redefine this method only if you really need to do so.
     * In any other cases redefine the getRequest() one
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function buy()
    {
        $this->skipCoverage();

        // Login
        $this->open('user');

        $this->type('css=#edit-name', 'master');
        $this->type('css=#edit-pass', 'master');

        $this->submitAndWait('css=#user-login');

        // Add-to-cart
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->createQueryBuilder()
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $this->openAndWait('store/product//product_id-' . $product->getProductId());

        $this->click("//button[@class='bright add2cart']");

        $this->waitForLocalCondition(
            'jQuery(".product-details .product-buttons-added .buy-more").length > 0',
            10000,
            'check content reloading'
        );

        // Checkout
        $this->openAndWait('store/checkout');

        if (0 < intval($this->getJSExpression('jQuery(".current.shipping-step").length'))) {
            $this->toggleByJquery('ul.shipping-rates li input:eq(0)', true);
            $this->click('css=.current .button-row button');
            $this->waitForLocalCondition(
                'jQuery(".payment-step").hasClass("current") == true',
                10000,
                'check switching to payment step'
            );
        }

        if (0 < intval($this->getJSExpression('jQuery(".current.payment-step").length'))) {
            $this->toggleByJquery($this->getPaymentSelector(), true);
            sleep(2);
            $this->click('css=.current .button-row button');
            $this->waitForLocalCondition(
                'jQuery(".review-step").hasClass("current") == true',
                10000,
                'check switching to review step'
            );
        }

        if (0 < intval($this->getJSExpression('jQuery(".current.review-step").length'))) {
            $this->click('css=.payment-step .button-row button');
            $this->waitForLocalCondition(
                'jQuery(".payment-step").hasClass("current") == true',
                10000,
                'check return to payment step'
            );

            $this->toggleByJquery($this->getPaymentSelector(), true);
            sleep(2);
            $this->click('css=.current .button-row button');
            $this->waitForLocalCondition(
                'jQuery(".review-step").hasClass("current") == true',
                10000,
                'check switching to next step #3'
            );
        }

        $this->click('//input[@id="place_order_agree"]');
        $this->click('css=.current .button-row button');
        $this->waitForLocalCondition('location.href.search(/checkoutSuccess/) != -1');

        // Check order
        $ordeid = null;

        if (preg_match('/order_id-(\d+)/Ss', $this->getLocation(), $m)) {
            $ordeid = intval($m[1]);
        }

        $this->assertTrue(0 < $ordeid, 'check order id');

        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($ordeid);

        $this->assertNotNull($order, 'check order');

        $this->assertEquals(1, count($order->getPaymentTransactions()), 'check payment transactions count');
        $this->assertEquals('W', $order->getPaymentTransactions()->get(0)->getStatus(), 'check payment transaction status');
        $this->assertFalse($order->isPayed(), 'check order payed status');

        $this->openAndWait('user/1/orders/' . $order->getOrderId());

        return $order;
    }

    public function testStructure()
    {
        $order = $this->buy();

        $locale = setlocale(LC_ALL, 'C');

        $date = \XLite\Core\Converter::formatTime($order->getDate());

        // Title
        $this->assertEquals(
            'Order #' . $order->getOrderId() . ', ' . $date,
            $this->getJSExpression('jQuery("#page-title").html()'),
            'check page title'
        );

        // Links
        $this->assertElementPresent(
            "//div[@class='order-box']"
            . "/div[@class='order-statuses']"
            . "/div[@class='shipping order-status-" . $order->getStatus() . "']"
        );

        $this->assertElementPresent(
            "//div[@class='order-box']"
            . "/ul[@class='links']"
            . "/li[@class='back']"
            . "/a"
            . "/span[text()='Back to order list']"
        );

        $this->assertElementPresent(
            "//div[@class='order-box']"
            . "/ul[@class='links']"
            . "/li[@class='print']"
            . "/a"
            . "/span[text()='Print invoice']"
        );

        $this->assertEquals(
            1,
            intval($this->getJSExpression('jQuery(".invoice-box").length')),
            'check invoice box count'
        );

        // Invoice header
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='header']"
            . "/tbody"
            . "/tr"
            . "/td[@class='address']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='header']"
            . "/tbody"
            . "/tr"
            . "/td[@class='logo']"
            . "/img[@class='logo']"
        );

        // Invoice title
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/h2[@class='invoice' and text()='Invoice #" . $order->getOrderId() . "']"
        );

        // Subhead
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/div[@class='subhead']"
            . "/span[text()='Grand total: $" . number_format(round($order->getTotal(), 2), 2) . "']"
        );

        // Items
        $countTR = intval($this->getJSExpression('jQuery(".invoice-box .items tr").length'));
        $this->assertEquals(count($order->getItems()) + 1, $countTR, 'TR count checking');

        $countTH = intval($this->getJSExpression('jQuery(".invoice-box .items th").length'));
        $this->assertEquals(5, $countTH, 'TH count checking');

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr"
            . "/th[position()=1 and text()='Product']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr"
            . "/th[position()=2 and text()='SKU']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr"
            . "/th[position()=3 and text()='Price']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr"
            . "/th[position()=4 and text()='Qty']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr"
            . "/th[position()=5 and text()='Total']"
        );

        // First product
        $item = $order->getItems()->get(0);
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=1]"
            . "/a[text()='" . $item->getName(). "']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=2 and text()='" . $item->getSku() . "']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=3 and text()='$" . number_format(round($item->getPrice(), 2), 2) . "']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=4 and text()='" . $item->getAmount() . "']"
        );

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='items']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=5 and text()='$" . number_format(round($item->getTotal(), 2), 2) . "']"
        );

        // Totals
        $countTR = intval($this->getJSExpression('jQuery(".invoice-box .totals tr").length'));
        $this->assertEquals(count($order->getSurcharges()) + 2, $countTR, 'Totals TR count checking');

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='totals']"
            . "/tbody"
            . "/tr[position()=1]"
            . "/td[position()=1 and text()='Subtotal:']"
        );
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='totals']"
            . "/tbody"
            . "/tr[position()=1]"
            . "/td[position()=2 and text()='$" . number_format(round($order->getSubtotal(), 2), 2) . "']"
        );

        $i = 2;
        foreach ($order->getSurcharges() as $m) {
            $this->assertElementPresent(
                "//div[@class='invoice-box']"
                . "/table[@class='totals']"
                . "/tbody"
                . "/tr[position()=$i]"
                . "/td[position()=1 and text()='" . $m->getName() . ":']"
            );
            $this->assertElementPresent(
                "//div[@class='invoice-box']"
                . "/table[@class='totals']"
                . "/tbody"
                . "/tr[position()=$i]"
                . "/td[position()=2 and text()='$" . number_format($m->getValue(), 2) . "']"
            );
            $i++;
        }

        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='totals']"
            . "/tbody"
            . "/tr[position()=$countTR]"
            . "/td[position()=1 and text()='Grand total:']"
        );
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='totals']"
            . "/tbody"
            . "/tr[position()=$countTR]"
            . "/td[position()=2 and text()='$" . number_format(round($order->getTotal(), 2), 2) . "']"
        );

        // Addresses
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='addresses']"
            . "/tbody"
            . "/tr[position()=1]"
            . "/td[position()=1 and @class='ship']"
        );
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='addresses']"
            . "/tbody"
            . "/tr[position()=1]"
            . "/td[position()=2 and @class='bill']"
        );

        // Methods
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='addresses']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=1 and @class='shipping']"
        );
        $this->assertElementPresent(
            "//div[@class='invoice-box']"
            . "/table[@class='addresses']"
            . "/tbody"
            . "/tr[position()=2]"
            . "/td[position()=2 and @class='payment']"
        );

        $txt = $this->getJSExpression('jQuery(".invoice-box .addresses .payment").html()');
        $txt = str_replace("\n", "", trim($txt));
        $this->assertRegExp('/' . $order->getPaymentmethod()->getName() . '/S', $txt, 'check payment method');
    }

    protected function fillShippingAddress()
    {
        // Fill minimum address fields
        $this->select(
            '//select[@id="shipping_address_country"]',
            'value=US'
        );
        $this->waitForLocalCondition(
            'jQuery("select#shipping_address_state").length == 1',
            3000,
            'check state selector'
        );

        $this->select(
            '//select[@id="shipping_address_state"]',
            'value=34'
        );

        $this->typeKeys(
            '//input[@id="shipping_address_zipcode"]',
            '10001'
        );

        // Complete shipping address
        $this->typeKeys(
            '//input[@id="shipping_address_name"]',
            'John Smith'
        );

        $this->typeKeys(
            '//input[@id="shipping_address_street"]',
            'test address'
        );

        $this->typeKeys(
            '//input[@id="shipping_address_city"]',
            'new York'
        );

        $this->waitForLocalCondition(
            'jQuery("ul.shipping-rates li input").length > 0',
            10000,
            'check address-not-completed note hide'
        );

        // Select shipping method
        $this->toggleByJquery('ul.shipping-rates li input:eq(0)', true);

        $this->waitForLocalCondition(
            'jQuery(".current .button-row button.disabled").length == 0',
            3000,
            'check enabled main button'
        );

        // Submit
        $this->click('css=.current .button-row button');

        $this->waitForLocalCondition(
            'jQuery(".payment-step.current").length == 1',
            10000,
            'check switching to next step (shipping->payment)'
        );

    }

    protected function fillPaymentStep()
    {
        $this->toggleByJquery($this->getPaymentSelector(), true);

        $this->waitForLocalCondition(
            'jQuery(".current .button-row button.disabled").length == 0',
            3000,
            'check enabled main button'
        );

        // Submit
        $this->click('css=.current .button-row button');

        $this->waitForLocalCondition(
            'jQuery(".review-step.current").length == 1',
            10000,
            'check switching to next step (payment->review)'
        );

    }

    protected function getPaymentSelector()
    {
        $selector = '#pmethod' . $this->getPaymentMethodIdByName('PhoneOrdering');
        $this->assertElementPresent("css=$selector", "Payment method selector not found: $selector (Phone)");

        return $selector;
    }
}
