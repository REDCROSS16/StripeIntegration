<?php

declare(strict_types=1);

namespace App\Controller\CreditCard;

use App\Service\Card\CardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ActivateCardAction
 * @package App\Controller\CreditCard
 */
#[Route('/account/activate-card', name: 'activate-card', methods: ['GET'])]
class ActivateCardAction extends AbstractController
{
    public function __invoke(Request $request, CardService $cardService): Response
    {
        $status = $cardService->activate($request, $this->getUser());
        $this->addFlash('notice', $status['message']);

        return $this->redirectToRoute('account');
    }
}
