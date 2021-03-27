<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    /**
     * @Route("/reset-password", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($request->get('email')){
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user){
                //1: Enregistrer en base la demande de reset password
                $resetPassword = (new ResetPassword())
                    ->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new \DateTime())
                ;

                $this->em->persist($resetPassword);
                $this->em->flush();

                //2: Envoyer un email à l'utilisateur avec un lien lui permettant de mettre à jour son mdp
                $url = $this->generateUrl('update_password', [
                    'token' => $resetPassword->getToken()
                ]);
                $content = "Bonjour ".$user->getFirstname().",<br><br>Vous avez demandé à réinitialiser votre mot de passe sur myShop<br><br>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='$url'>mettre à jour votre mot de passe. </a>";

                $mail = new Mail();
                $mail->send(
                    $user->getEmail(),
                    $user->getFirstname().' '.$user->getLastname(),
                    'Réinitialisez votre mot de passe sur myShop',
                    $content);
                $this->addFlash('notice', 'Vous allez recevoir dans quelques instants un mail avec la procédure pour réinitialiser votre mot de passez');

            }else{
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/update-password/{token}", name="update_password")
     */
    public function updatePassword(Request $request, $token, UserPasswordEncoderInterface $encoder)
    {
       $resetPassword = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

       if(!$resetPassword){
           return $this->redirectToRoute('reset_password');
       }
        $now = new \DateTime();

       if($now > $resetPassword->getCreatedAt()->modify('+ 3 hour')){
           $this->addFlash('notice', "Votre demande de mot de passe à expirée. Merci de la renouvelée.");
           return $this->redirectToRoute('reset_password');
       }

       //Modifier le mot de passe
        $form = $this->createForm(ResetPasswordType::class);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
           $new_pwd = $form->get('new_password')->getData();
           $user = $resetPassword->getUser();
           $password = $encoder->encodePassword($user,$new_pwd);
           $user->setPassword($password);
           $this->em->flush();
           $this->addFlash('notice', "Votre mot de passe à bien été mise à jour.");

           return $this->redirectToRoute('app_login');
       }
       return $this->render('reset_password/update.html.twig', [
           'form' => $form->createView()
       ]);
    }
}
