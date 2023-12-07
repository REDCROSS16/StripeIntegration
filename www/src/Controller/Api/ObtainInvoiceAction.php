<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 22:11
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Invoice\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ObtainInvoiceAction
 * @package App\Controller\Api\Invoice
 */
#[Route(path: '/api/invoice', name: 'api-obtain-invoice', methods: ['POST'])]
class ObtainInvoiceAction extends AbstractController
{
    public function __invoke(Request $request, InvoiceService $invoiceService): Response
    {
        try {
            $invoice = $invoiceService->handleRequest($request);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['status' => 'success', 'invoice' => $invoice->getId()]);
    }
}
