<?php
declare(strict_types=1);
/**
 * Technical Challenge
 *
 * @category    TechChallenge
 * @package     TechChallenge_CheckoutValidator
 * @author      Rafael Falcão <rafaelfalcaof@gmail.com>
 */

namespace TechChallenge\CheckoutValidator\Model\Quote;

use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Model\Stock;

class InventoryValidator
{
    /**
     * @param StockStatusInterface $stock
     * @return bool
     */
    public function validate(StockStatusInterface $stock): bool
    {   
        if (!$stock) {
            return false;
        }

        if ($stock->getStockStatus() === Stock::STOCK_OUT_OF_STOCK) {
            return false;
        }

        return true;
    }
}
