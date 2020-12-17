<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountController
 * @package App\Controller
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em){
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     * @Route("/", name="account")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * @Route("/password", name="account_password")
     * @return Response
     */
    public function editPassword(Request $request){
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $oldPassword = $form->get('old_password')->getData();
            if($this->encoder->isPasswordValid($user, $oldPassword))
            {
                $newPassword = $form->get('new_password')->getData();
                $password = $this->encoder->encodePassword($user, $newPassword);
                $user->setPassword($password);
                $this->addFlash('success', "Votre nouveau mot de passe est enregistré.");

                $this->em->persist($user);
                $this->em->flush();
            }else{
                $this->addFlash('warning', "Votre mot de passe actuel est incorrect.");
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
