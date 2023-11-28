<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 28.11.2023
 * Time: 18:43
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Testpay;

use App\Entity\User;
use App\Service\PaymentService\PaymentService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompletePayTestAction
 * @package App\Controller\Testpay
 */
#[Route('/test/pay', name: 'test-pay', methods: ['POST'])]
class CompleteTestPayAction extends AbstractController
{
    private PaymentService $paymentService;
    private EntityManagerInterface $entityManager;

    public function __construct(PaymentService $paymentService, EntityManagerInterface $entityManager)
    {
        $this->paymentService = $paymentService;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);

        try {
            $this->paymentService->pay($request, $user);
        } catch (\Exception $e) {
            return $this->redirectToRoute('test-form');
        }

        return $this->redirectToRoute('test-form');
    }
}
