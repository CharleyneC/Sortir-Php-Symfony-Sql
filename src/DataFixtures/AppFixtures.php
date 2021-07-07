<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Sorties;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Faker;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures implements ORMFixtureInterface, ContainerAwareInterface
{

    private $container;

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $em)
    {
        // initialisation de l'objet Faker
        $faker = Faker\Factory::create('fr_FR');

        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            Participant::class => $defaultEncoder,
        ];
        $encoderFactory = new EncoderFactory($encoders);
        //création des campus
        $allNameCampus = array('Nantes', 'Paris', 'Marseille', 'Bordeaux', 'Lille');
        for ($i = 0; $i < 5; $i++) {
            $campus = new Campus();
            $campus->setNom($allNameCampus[$i]);
            $em->persist($campus);
            $allcampus[] = $campus;
        }
        $em->flush();

        //création d'un admin
        $admin = new \App\Entity\Participant();
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, '123456'));
        $admin->setPseudo($faker->userName)
            ->setPrenom($faker->firstName)
            ->setNom($faker->lastName)
            ->setMail($faker->email)
            ->setTelephone($faker->phoneNumber);
        $admin->setCampus($faker->randomElement($allcampus));
        $admin->setAdministrateur(1);
        $admin->setActif(1);
        $admin->setRoles(['ROLE_ADMIN']);
        $em->persist($admin);

        //création des participants
        $user = [];
        for ($i = 0; $i < 10; $i++) {
            $user[$i] = new \App\Entity\Participant();
            $user[$i]->setPassword($this->passwordEncoder->encodePassword($user[$i], '123456'));
            $user[$i]->setPseudo($faker->userName)
                ->setPrenom($faker->firstName)
                ->setNom($faker->lastName)
                ->setMail($faker->email)
                ->setTelephone($faker->phoneNumber);
            $user[$i]->setCampus($faker->randomElement($allcampus));
            $user[$i]->setAdministrateur(0);
            $user[$i]->setActif(1);
            $user[$i]->setRoles(['ROLE_USER']);
            $em->persist($user[$i]);
        }
        $em->flush();

        //création des villes et des lieux
        $city = [];
        $lieu = [];
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 5; $i++) {
            $city[$i] = new \App\Entity\Ville();
            for ($j = 0; $j < 5; $j++) {
                $lieu[$j] = new Lieu();
                $lieu[$j]->setNomLieu($faker->streetName);
                $lieu[$j]->setRue($faker->streetAddress);
                $lieu[$j]->setLatitude($faker->latitude);
                $lieu[$j]->setLongitude($faker->longitude);
                $lieu[$j]->setVille($city[$i]);
                $em->persist($lieu[$j]);
            }
            $city[$i]->setNom($faker->city);
            $city[$i]->setCodePostal($faker->postcode);
            $em->persist($city[$i]);
        }
        $em->flush();

        //création des sorties
        $sortieNom = ['Faire du vélo', 'Aller au cinema', 'Faire un bowling', 'Aller à la plage', 'Boire un verre'];
        $sortie = [];
        for ($i = 0; $i < 25; $i++) {
            $sortie[$i] = new Sorties();
            $sortie[$i]->setNom($faker->randomElement($sortieNom));
            $sortie[$i]->setDatedebut($faker->dateTimeBetween('now', '+2 day'));
            $sortie[$i]->setDatecloture($faker->dateTimeBetween($sortie[$i]->getDatedebut(), '+2 day'));
            $sortie[$i]->setOrganisateur($faker->randomElement($user));
            $sortie[$i]->setDuree($faker->biasedNumberBetween(1, 220));
            $sortie[$i]->setNbinscriptionsmax($faker->biasedNumberBetween(1, 20));
            $sortie[$i]->setDescriptioninfos($faker->sentence($nbWords = 10, $variableNbWords = true));
            $sortie[$i]->setSiteOrganisateur($sortie[$i]->getOrganisateur()->getCampus());
            $sortie[$i]->setLieu($faker->randomElement($lieu));
            $em->persist($sortie[$i]);

        }

        $em->flush();


    }




}

