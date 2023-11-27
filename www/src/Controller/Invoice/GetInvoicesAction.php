<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 16:52
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Invoice;

use App\Service\Invoice\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetInvoicesAction
 * @package App\Controller\Invoice
 */
#[Route('/account/invoices/list', name: 'invoices-list', methods: ['GET'])]
class GetInvoicesAction extends AbstractController
{
    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function __invoke(): Response
    {
        return $this->render(
            'account/invoice/invoice.html.twig',
            [
                'invoices' => $this->invoiceService->getInvoicesList($this->getUser())
            ]
        );
    }
}
