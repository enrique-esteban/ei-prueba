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
    public function getCurrencies(CurrencyService $cs, Request $request): Response
    {
        $currencies = $cs->getCurrencies();
        $form = $this->createForm(CurrencyCalculatorForm::class);
        $data = null;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            $data = $form->getData();

            dump($data);

            /// return $this->redirectToRoute('task_success');
        }


        return $this->render('home.html.twig', [
            'currencies' => $currencies,
            'form' => $form,
            'data' => $data
        ]);
    }
}
