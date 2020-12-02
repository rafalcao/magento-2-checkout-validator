<?php
/**
 * Run command:
 * ./vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist vendor/rafalcao/magento-2-checkout-validator --stderr
 *
 * Technical Challenge
 *
 * @category    TechChallenge
 * @package     TechChallenge_CheckoutValidator
 * @author      Rafael Falcão <rafaelfalcaof@gmail.com>
 */
namespace TechChallenge\CheckoutValidator\Test\Unit;

use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Model\Stock;
use TechChallenge\CheckoutValidator\Model\Quote\InventoryValidator;

class InventoryValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InventoryValidator
     */
    protected $inventoryValidator;

    /**
     * @var StockStatusInterface
     */
    protected $stockStatusInterfaceMock;

    /**
     * Setup
     */
    public function setUp() : void
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->inventoryValidator = $objectManager->getObject(
            'TechChallenge\CheckoutValidator\Model\Quote\InventoryValidator'
        );
        $this->stockStatusInterfaceMock = $this->createStockStatusInterfaceMock();
    }

    /**
     * Test Validate With Stock Status Is In Stock Should Return True
     */
    public function testValidateWithStockStatusIsInStockShouldReturnTrue()
    {
        $this->stockStatusInterfaceMock
            ->method('getStockStatus')
            ->willReturn(Stock::STOCK_IN_STOCK);

        $return = $this->inventoryValidator->validate($this->stockStatusInterfaceMock);
        $this->assertEquals(true, $return);
    }

    /**
     * Test Validate With Stock Status Is Out Of Stock Should Return False
     */
    public function testValidateWithStockStatusIsOutOfStockShouldReturnFalse()
    {
        $this->stockStatusInterfaceMock
            ->method('getStockStatus')
            ->willReturn(Stock::STOCK_OUT_OF_STOCK);

        $return = $this->inventoryValidator->validate($this->stockStatusInterfaceMock);
        $this->assertEquals(false, $return);
    }

    /**
     * Test Validate With Stock Status Is Null Should Return False
     */
    public function testValidateWithStockStatusIsNullShouldReturnFalse()
    {
        $return = $this->inventoryValidator->validate(null);
        $this->assertEquals(false, $return);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createStockStatusInterfaceMock()
    {
        $mock = $this->getMockBuilder(StockStatusInterface::class)
            ->setMethods([
                    'getStockStatus', 'setProductId', 'getStockId', 'setStockId',
                    'getQty', 'setQty', 'setStockStatus', 'getStockItem', 'getExtensionAttributes',
                    'setExtensionAttributes', 'getProductId'
                ])
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }
}
