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

    public function decrementProductStock(Product $product)
    {
        $currentStock = $product->getStock();

        if ($currentStock === 0) {
            throw new \Exception('Produit indisponible');
        }

        $product->decrementStock();

        $em = $this->doctrine->getManager();
        $em->persist($product);
        $em->flush();

    }
}