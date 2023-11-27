<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetLogoutAction
 * @package App\Controller\Api\Security
 */
#[Route(path: '/logout', name: 'logout', methods: ['GET'])]
class GetLogoutAction extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->redirectToRoute('login');
    }
}