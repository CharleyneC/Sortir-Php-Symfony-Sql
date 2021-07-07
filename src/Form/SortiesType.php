<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sorties;


use App\Entity\Ville;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                'required' => true,
                'label' => 'Nom de la sortie'
            ])
            ->add('campus', TextType::class, [
                'disabled' => true,
                'mapped' => false
            ])
             ->add('lieu', EntityType::class, [
                 'class' => Ville::class,
                 'choice_label' => 'nom',
                 'label' => 'Ville',
                 'mapped' => false,
                 'placeholder' => 'Choisir une ville',
             ])

            ->add('datedebut', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date et heure'
            ])
            ->add('duree', IntegerType::class, [
                'required' => true,
                'attr' => ['min' => 10, 'max' => 240],
                'label' => 'Durée (en minutes)'
            ])
            ->add('datecloture', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date limite d'inscription"
            ])
            ->add('nbinscriptionsmax', IntegerType::class, [
                'attr' => ['min' => 1, 'max' => 20],
                'required' => true,
                'label' => 'Nombre de places'
            ])
            ->add('descriptioninfos', TextareaType::class, [
                'required' => true,
                'label' => 'Description et infos'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
