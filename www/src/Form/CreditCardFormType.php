<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 22.11.2023
 * Time: 16:42
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\CreditCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CreditCardForm
 * @package App\Form
 */
class CreditCardFormType extends AbstractType
{
    const BLANK_MESSAGE = 'This field can not be blank';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min'        => 2,
                        'minMessage' => 'Cardname is too short'
                    ]),
                    new NotBlank([
                        'message' => '',
                    ])
                ]
            ])
            ->add('pan', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'enter correct pan 16 numbers',
                    ])
                ]
            ])
            ->add('expiration', TextType::class, [
                'constraints' => [
                    new Length([
                        'min'        => 5,
                        'max'        => 5
                    ]),
                    new NotBlank([
                        'message' => 'expiration',
                    ])
                ]
            ])
            ->add('cvv', TextType::class, [
                'constraints' => [
                    new Length([
                        'min'        => 3,
                        'max'        => 3
                    ]),
                    new NotBlank([
                        'message' => 'ENTER CVV 3 signs',
                    ])
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreditCard::class,
            'csrf_protection' => false,
        ]);
    }
}
