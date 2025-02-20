<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Integration\Service\Quote;

use Magento\Quote\Model\Quote;
use TIG\PostNL\Service\Quote\CheckIfQuoteItemsCanBackorder;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class CheckIfQuoteItemsCanBackorderTest extends TestCase
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterfaceMock;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfigurationMock;

    public $instanceClass = CheckIfQuoteItemsCanBackorder::class;

    public function setUp() : void
    {
        parent::setUp();

        $this->stockRegistryInterfaceMock = $this->getFakeMock(StockRegistryInterface::class)->getMock();
        $this->stockConfigurationMock     = $this->getFakeMock(StockConfigurationInterface::class)->getMock();
    }

    public function testQuoteItemsCanBackorder()
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteCanBackorder.php';

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getObject(Quote::class)->load('backorder01', 'reserved_order_id');

        $checkoutSession        = $this->getFakeMock(CheckoutSession::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quote);

        $instance = $this->getInstance(
            [
                'checkoutSession'         => $checkoutSession,
                '$stockRegistryInterface' => $this->stockRegistryInterfaceMock,
                '$stockConfiguration'     => $this->stockConfigurationMock,
            ]
        );
        $result   = $instance->getValue();

        $this->assertEquals(true, $result);
    }

    public function testQuoteItemsCanNotBackorder()
    {
        require __DIR__ . '/../../../Fixtures/Quote/quoteCanNotBackorder.php';

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->getObject(Quote::class)->load('nobackorder01', 'reserved_order_id');

        $checkoutSession        = $this->getFakeMock(CheckoutSession::class)->getMock();
        $checkoutSessionExpects = $checkoutSession->method('getQuote');
        $checkoutSessionExpects->willReturn($quote);

        $instance = $this->getInstance(
            [
                'checkoutSession'         => $checkoutSession,
                '$stockRegistryInterface' => $this->stockRegistryInterfaceMock,
                '$stockConfiguration'     => $this->stockConfigurationMock,
            ]
        );
        $result   = $instance->getValue();

        $this->assertEquals(false, $result);
    }
}
