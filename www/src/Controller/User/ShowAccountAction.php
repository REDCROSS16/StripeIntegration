<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 23.11.2023
 * Time: 12:07
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\User;

use App\Service\Card\CardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowAccountAction
 * @package App\Controller\Api\User
 */
#[Route('/account', name: 'account', methods: ['GET', 'POST'])]
class ShowAccountAction extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager, CardService $cardService): Response
    {

//        dd($cardService->getCreditCardByToken('1701025197'));

        // TODO: отображение следующего списания

        return $this->render(
            'account/cards.html.twig',
            [
                'cards' => $cardService->getAvailableCards($this->getUser())
            ]
        );
    }
}
