<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 13:40
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\CreditCard;

use App\Service\Card\CardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeactivateCardAction
 * @package App\Controller\CreditCard
 */
#[Route('/account/deactivate-card', name: 'deactivate-card', methods: ['GET'])]
class DeactivateCardAction extends AbstractController
{
    public function __invoke(Request $request, CardService $cardService): Response
    {
        $status = $cardService->deactivate($request, $this->getUser());
        $this->addFlash('notice', $status['message']);
        
        return $this->redirectToRoute('account');
    }
}