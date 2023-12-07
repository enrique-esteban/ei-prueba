<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CurrencyService;
use App\Form\CurrencyCalculatorForm;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function homeController(CurrencyService $cs, Request $request): Response
    {
        $currencies = $cs->getCurrencies();

        if ($currencies['error_code'] !== 0) {
            return $this->render('home.html.twig', [
                'form' => null,
                'data' => null,
            ]);
        }

        $form = $this->createForm(CurrencyCalculatorForm::class, ['currencies' => $currencies['list']]);
        $data = null;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $exchangeAmmount = $cs->getCurrencyRate($data['from']->getSlug(), $data['to']->getSlug(), $data['ammount']);

            if ($exchangeAmmount['error_code'] === 1) {
                return $this->render('home.html.twig', [
                    'form' => null,
                    'data' => null,
                ]);
            }

            $data = [
                'original_ammount' => $data['ammount'],
                'exchange_ammount' => $exchangeAmmount['price'],
                'from_text' => $this->getCompleteName($data['from']->getSlug(), $currencies['list']),
                'to_text' => $this->getCompleteName($data['to']->getSlug(), $currencies['list']),
            ];
        }

        return $this->render('home.html.twig', [
            'form' => $form,
            'data' => $data
        ]);
    }

    private function getCompleteName($slug, $currencies): string
    {
        foreach ($currencies as $value) {
            if ($value->getSlug() === $slug) {
                return strtoupper($value->getSlug()) . ' ' . $value->getName();
            }
        }

        return "";
    }
}
