<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 */
class StripeController extends AbstractController
{

    /**
     * @Route("/stripe-create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $em, Cart $cart, $reference)
    {
        $YOUR_DOMAIN = 'http://localhost:8000';
        $productForStripe = [];
        $order = $em->getRepository(Order::class)->findOneByReference($reference);
        if(!$order){
            return new JsonResponse(['error' => 'order']);
        }

        Stripe::setApiKey('sk_test_51IIznSGgKy58y8efedzvKgz2U5LVal6ZCqwFf0XBb1erUqnCLS7gn209NmLlvDocb5p8kDmHv4aFt8fSTX42rvfC00QNxP605w');
        foreach($order->getOrderDetails()->getValues() as $product){
            $product_object = $em->getRepository(Product::class)->findOneByName($product->getProduct());
            $productForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $productForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $productForStripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $em->flush();

        $response =  new JsonResponse();
        $response->setData([
            'id' => $checkout_session->id
        ]);

        return $response;
    }
}
