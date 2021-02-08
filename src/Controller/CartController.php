<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Cart $cart, EntityManagerInterface $entityManager){
        $this->cart = $cart;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="cart_index")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $this->cart->getFull()
        ]);
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     */
    public function add($id): Response
    {
        $this->cart->add($id);
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/remove/all", name="cart_remove_all")
     */
    public function removeAll(): Response
    {
        $this->cart->removeAll();
        return $this->redirectToRoute('product_list');
    }

    /**
     * @Route("/delete/{id}", name="cart_delete")
     */
    public function delete($id){
        $cart = $this->cart->delete($id);
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/decrease/{id}", name="cart_decrease")
     */
    public function decrease($id){
        $cart = $this->cart->decrease($id);
        return $this->redirectToRoute('cart_index');
    }
}
