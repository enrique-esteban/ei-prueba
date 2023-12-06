<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Service\CurrencyService;
use App\Model\CurrencyModel;


class CurrencyCalculatorForm extends AbstractType
{
    private $currencies;

    public function __construct(CurrencyService $cs)
    {
        $this->currencies = $cs->getCurrencies();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ammount', NumberType::class, [
                'label' => 'Cantidad *',
                'required' => true
            ])
            ->add('from', ChoiceType::class, [
                'label' => 'de',
                'choices' => $this->currencies,
                'choice_value' => 'slug',
                'choice_label' => function (?CurrencyModel $currency) {
                    return strtoupper($currency->getSlug()) . ' - ' .  $currency->getName();
                },
                'data' => "Axelar",
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10]),
                ],

            ])
            ->add(
                'to',
                ChoiceType::class,
                [
                    'label' => 'a',
                    'choices' => $this->currencies,
                    'choice_value' => 'slug',
                    'choice_label' => function (?CurrencyModel $currency) {
                        return strtoupper($currency->getSlug()) . ' - ' .  $currency->getName();
                    },
                    'data' => "usd",
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
            );
    }
}
