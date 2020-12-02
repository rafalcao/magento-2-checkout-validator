<?php
declare(strict_types=1);
/**
 * Technical Challenge
 *
 * @category    TechChallenge
 * @package     TechChallenge_CheckoutValidator
 * @author      Rafael Falcão <rafaelfalcaof@gmail.com>
 */

namespace TechChallenge\CheckoutValidator\Plugin\Checkout;

use Magento\CatalogInventory\Helper\Data;
use Magento\Checkout\Model\PaymentInformationManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\Item;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use TechChallenge\CheckoutValidator\Model\Quote\InventoryValidator;

class PaymentInformationManagementPlugin
{
    /** @var string  */
    const OUT_OF_STOCK_MSG = 'The product %1 is out of stock.';

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var InventoryValidator
     */
    private $inventoryValidator;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * PaymentInformationManagementPlugin constructor.
     * @param LoggerInterface $logger
     * @param Cart $cart
     * @param InventoryValidator $inventoryValidator
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        Cart $cart,
        InventoryValidator $inventoryValidator,
        StockRegistryInterface $stockRegistry
    ){
        $this->cart                 = $cart;
        $this->inventoryValidator   = $inventoryValidator;
        $this->stockRegistry        = $stockRegistry;
    }

    /**
     * @param PaymentInformationManagement $subject
     * @param $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagement $subject,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ){
        $items = $this->cart->getQuote()->getItemsCollection();
        foreach ($items as $item) {
            if (!$this->inventoryValidator->validate($this->getStock($item))) {
                $this->addError($item);
            }
        }
    }

    /**
     * @param Item $item
     * @return StockRegistryInterface
     */
    private function getStock(Item $item): StockStatusInterface
    {
        $product = $item->getProduct();
        $stockStatus = $this->stockRegistry->getStockStatus($product->getId(), $product->getStore()->getWebsiteId());

        return $stockStatus;
    }

    /**
     * @param Item $item
     */
    private function addError(Item $item): void
    {
        $this->cart->getQuote()->addErrorInfo(
            'stock-' . $item->getProduct()->getId(),
            'checkoutvalidator',
            Data::ERROR_QTY,
            ' | ' . __(self::OUT_OF_STOCK_MSG, $item->getProduct()->getName())
        );
    }


}
