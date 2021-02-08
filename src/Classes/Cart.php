<?php


namespace App\Classes;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager){
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id){
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
    }

    public function get(){
        return $this->session->get('cart');
    }

    public function removeAll(){
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        return $this->session->set('cart', $cart);
    }

    public function decrease($id){
        $cart = $this->session->get('cart', []);

        if($cart[$id] > 1){
            $cart[$id]--;
        }else{
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }

    public function getFull(){
        $cartComplete = [];
        if($this->get()){
            foreach ($this->get() as $id => $quantity){
                $productEntity = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$productEntity){
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $productEntity,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }
}