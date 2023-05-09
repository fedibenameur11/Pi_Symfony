<?php

namespace App\Form;

use App\Entity\Programme;
use App\Entity\ProgrammeSemaine;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityManagerInterface;
class ProgrammeSemaineType extends AbstractType

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_semaine',TextType::class, [
                'label' => 'Numero de la semaine',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ->add('objectif_semaine',TextType::class, [
                'label' => 'Objectif de la semaine',
            ])
            ->add('nutrition_planning',TextareaType::class, [
                'label' => 'Objectif de la semaine',
                'attr' => [
                    'style' => "height: 450px;"
                ],
            ])
            ->add('entrainement_planning',TextareaType::class, [
                'label' => 'Objectif de la semaine',
                'attr' => [
                    'style' => "height: 450px;"
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProgrammeSemaine::class,
        ]);
    }
}
