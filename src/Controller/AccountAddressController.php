<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account/address")
 */
class AccountAddressController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    /**
     * @Route("/", name="account_address")
     */
    public function index(): Response
    {
         return $this->render('account/address.html.twig', [
        ]);
    }

    /**
     * @Route("/add", name="account_address_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush();
            $this->addFlash('success', "Votre adresse à bien été ajoutée.");

            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="account_address_edit")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request, $id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);
        if(!$address || $address->getUser() !== $this->getUser()){
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', "Votre adresse à bien été éditée.");

            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="account_address_delete")
     * @return Response
     */
    public function delete($id): Response
    {
        $address = $this->em->getRepository(Address::class)->findOneById($id);

        if($address && $address->getUser() == $this->getUser()){
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirectToRoute('account_address');
    }
}
