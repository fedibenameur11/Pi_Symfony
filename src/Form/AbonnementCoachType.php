<?php

namespace App\Form;

use App\Entity\AbonnementCoach;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonnementCoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coach')
            ->add('client')
            ->add('duree_abonnement')
            ->add('duree_debut')
            ->add('duree_fin')
            ->add('statut')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbonnementCoach::class,
        ]);
    }
}
