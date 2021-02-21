<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController
 * @Route("/registration")
 */
class RegisterController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder){
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     * Create user
     *
     * @Route("/new", name="register")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $searchEmail = $this->em->getRepository(User::class)->findOneByEmail(['email'=>$user->getEmail()]);

            if(!$searchEmail){
                $plainPassword = $user->getPassword();
                $password = $this->encoder->encodePassword($user, $plainPassword);
                $user->setPassword($password);
                $this->em->persist($user);
                $this->em->flush();
                $userInfo = $user->getFirstname().  " ".$user->getLastname();

                //Send email
                $mail = new Mail();
                $content = "Bonjour ".$userInfo."<br>Bienvenue sur MyShop !";
                $mail->send($user->getEmail(), $userInfo, 'Bienvenue sur MyShop', $content);

                //Redirect
                $this->addFlash('success', "Utilisateur $userInfo inscrit !");
                $this->redirectToRoute("register");
            }else{
                $this->addFlash('error', "L'email existe déjà !");
            }

        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
