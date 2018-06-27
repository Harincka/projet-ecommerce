<?php
/**
 * Created by PhpStorm.
 * User: seb
 * Date: 27/06/18
 * Time: 09:14
 */

namespace AppBundle\Manager;


use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StockManager
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function decrementProductStock(Product $product, $quantity)
    {
        if ($quantity <= 0) {
            throw new \Exception('Quantity incorrect');
        }

        $currentStock = $product->getStock();

        if ($currentStock === 0) {
            throw new \Exception('Produit indisponible');
        }

        $success = $product->decrementStock($quantity);

        if (!$success) {
            return false;
        }

        $em = $this->doctrine->getManager();
        $em->persist($product);
        $em->flush();

        return true;
    }
}