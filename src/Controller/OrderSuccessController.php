<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 */
class OrderSuccessController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    /**
     * @Route("/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {

        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()){
            $cart->removeAll();
            $order->setIsPaid(true);
            $this->em->flush();

            //Envoyer un email à notre client pour lui confirmer la commande
            //Send email
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br>Merci pour votre commande !";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande est bien validée !', $content);
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
