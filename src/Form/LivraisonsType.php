<?php

namespace App\Form;

use App\Entity\Livraisons;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotNull;

class LivraisonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_livraisons')
            ->add('date_livraisons', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotNull(),
                    new GreaterThan('today'),
                ],
            ])
            ->add('duree_livraison')
            ->add('commande')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livraisons::class,
        ]);
    }
}
