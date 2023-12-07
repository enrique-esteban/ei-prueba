<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Length;
use App\Service\CurrencyService;
use App\Model\CurrencyModel;


class CurrencyCalculatorForm extends AbstractType
{
    /**
     * Builder of form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currencies = $options['data']['currencies'];

        $builder
            ->add('ammount', NumberType::class, [
                'label' => 'Ammount *',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                    new Length(['max' => 20]),
                ],
            ])
            ->add('from', ChoiceType::class, [
                'label' => 'from',
                'choices' => $currencies,
                'choice_value' => 'slug',
                'choice_label' => function (?CurrencyModel $currency) {
                    return strtoupper($currency->getSlug()) . ' - ' .  $currency->getName();
                },

            ])
            ->add(
                'to',
                ChoiceType::class,
                [
                    'label' => 'to',
                    'choices' => $currencies,
                    'choice_value' => 'slug',
                    'choice_label' => function (?CurrencyModel $currency) {
                        return strtoupper($currency->getSlug()) . ' - ' .  $currency->getName();
                    },
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Calculate',
                ],
            );
    }
}
