<?php

namespace App\Form;

use App\Entity\LanguageManagement;
use App\Entity\Language;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageManagementFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'name',
                'label' => 'Langue',
                'placeholder' => 'Sélectionnez les langues que vous voulez apprendre',
                'required' => false,
                'attr' => [
                    'class' => 'language-select',
                ],
            ])
            ->add('isProfessor', CheckboxType::class, [
                'label' => 'Souhaitez-vous enseigner cette langue ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LanguageManagement::class,
        ]);
    }
}