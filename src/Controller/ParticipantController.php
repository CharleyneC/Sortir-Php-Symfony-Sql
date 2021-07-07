<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use claviska\SimpleImage;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ParticipantController extends AbstractController
{

    /**
     * @Route("/admin/register", name="app_register")
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setAdministrateur(0);
            $participant->setActif(1);
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $form->get('plainPassword')->getData()
                )
            );
            $participant->setRoles(["ROLE_USER"]);
            $emi = $this->getDoctrine()->getManager();
            $emi->persist($participant);
            $emi->flush();
            $this->addFlash('success', "L'utilisateur a bien été enregistré!");

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/editprofile/{id}", name="edit_profile")
     */
     public function editProfile(Participant $participant,
                                 Request $request,
                                 EntityManagerInterface $em,
                                 UserPasswordEncoderInterface $passwordEncoder,
                                 GuardAuthenticatorHandler $guardHandler,string $uploadDir): Response
     {
         //récupération du formulaire
         $form = $this->createForm(ParticipantType::class, $participant);
         $form->handleRequest($request);


         //si le formulaire a été soumis
         if ($form->isSubmitted() && $form->isValid()) {
             $participant->setPassword(
                 $passwordEncoder->encodePassword(
                     $participant,
                     $form->get('plainPassword')->getData()
                 )
             );

             //on récupère l'image uploadée
             /** @var UploadedFile $picture */
             $photo = $form->get('photoDeProfil')->getData();

             if ($photo){
             //génère un nom de fichier aléatoire avec la bonne extension
                 $newFilename = md5(uniqid( )) . "." . $photo->guessExtension();
             //déplace le fichier uploadé dans public/image/
                 $photo->move($uploadDir, $newFilename);
             //hydrate propriété de l'entité avec le nom du fichier
                 $participant->setPhotoDeProfil($newFilename);

                 $image = new SimpleImage();
                 $image->fromFile($uploadDir . $newFilename)
                    ->bestFit(50,50)
                    ->toFile($uploadDir . "small/" . $newFilename);
             }

             //on enregistre en bdd
             $em = $this->getDoctrine()->getManager();
             $em->flush();
             $this->addFlash('success', 'Profil modifié !');
         }
         return $this->render('user/profil.html.twig', [
             'participant' => $participant,
             'registrationForm' => $form->createView(),
         ]);

     }

    /**
     * @Route("/admin/userlist", name="user_list")
     */
    public function userlist(ParticipantRepository $participantsRepository): Response
    {
        return $this->render('user/userlist.html.twig', ['participants' => $participantsRepository->findAll()]);
    }

    /**
     * @Route("/admin/delete/{id}", name="user_delete", requirements={"id":"\d+"})
     */
    public function delete($id)
    {
        $qb = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->delete(Participant::class, 'p')
            ->where('p.idParticipant = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
        return new JsonResponse([
            "status" => "deleted"
        ], 200);
    }

    /**
     * @Route("/admin/disable/{id}", name="user_disable", requirements={"id":"\d+"})
     */
    public function disable(ParticipantRepository $participantRepository, EntityManagerInterface $em, Request $request, $id): Response
    {
        $roles = ['ROLE_BANNED'];
        $qb = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->update(Participant::class, 'p')
            ->set('p.actif', ':actif')
            ->setParameter('actif', '0')
            ->set('p.roles', ':roles')
            ->setParameter('roles', json_encode($roles))
            ->where('p.idParticipant = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $p = $qb->execute();

        return new JsonResponse([
            "status" => "disabled"
        ], 200);
    }

    /**
     * @Route("/admin/enable/{id}", name="user_enable", requirements={"id":"\d+"})
     */
    public function enable(ParticipantRepository $participantRepository, EntityManagerInterface $em, Request $request, $id): Response
    {
        $roles = ['ROLE_USER'];
        $qb = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->update(Participant::class, 'p')
            ->set('p.actif', ':actif')
            ->setParameter('actif', '1')
            ->set('p.roles', ':roles')
            ->setParameter('roles', json_encode($roles))
            ->where('p.idParticipant = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $p = $qb->execute();

        return new JsonResponse([
            "status" => "enabled"
        ], 200);
    }
}
