<?php

// src/Form/ProductType.php

namespace App\Form;

use App\Document\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productName', TextType::class, [
                'label' => 'Nom du produit',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
