<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\CartProduct;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    /**
     * @Route("/add", name="add_to_cart")
     * @Method("POST")
     *
     * @param Product $product
     * @throws \Exception
     */
    public function addToCart(Request $request)
    {
        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->find($request->get('product_id'));

        // TODO CREATE SERVICE
        $this->get('app.product.stock')->decrementProductStock($product);

        $session = $this->get('session');

        $cart = $session->get('cart');

        $currentQty = $cart[$product->getId()] ?? 0;

        // on ajoute le produit au panier
        $cart[$product->getId()] = $currentQty + 1;

        $session->set('cart', $cart);
        $session->save();

        $this->addFlash('info', 'Le produit ' . $product->getName(). ' a bien été ajouté. <a href="'.$this->generateUrl('cart').'">Voir le panier.</a>');

        return $this->redirectToRoute('product_details', ['id' => $product->getId() ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cartAction()
    {
        $cart = $this->get('session')->get('cart') ?? [];

        $display = $this->get('app.cart')->getProductsForDisplay($cart);

        return $this->render('cart/details.html.twig', [
            'products' => $display['products'],
            'totalAmount' => $display['totalAmount'],
        ]);
    }

    /**
     * @Route("/remove", name="remove_from_cart")
     * @Method("POST")
     */
    public function removeFromCart(Request $request)
    {
        $productRepository = $this->get('doctrine')->getRepository(Product::class);
        $product = $productRepository->find($request->get('product_id'));

        $this->get('app.cart')->removeProduct($product);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/clearcart", name="clear_cart")
     */
    public function clearCart(Request $request) {
        $cart = $this->get('session')->get('cart') ?? [];

        $session = $this->get('session');
        $session->remove('cart');

        $session->save();

        return $this->redirectToRoute('cart');
    }
}