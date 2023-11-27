<?php

declare(strict_types=1);

namespace App\Controller\CreditCard;

use App\Entity\CreditCard;
use App\Form\CreditCardFormType;
use App\Service\Card\CardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BindCardAction
 * @package App\Controller\Api\CreditCard
 */
#[Route('/account/add-card', name: 'add-card', methods: ['GET', 'POST'])]
class AddCardAction extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private CardService $cardService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        CardService $cardService
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->cardService = $cardService;
    }

    public function __invoke(Request $request): Response
    {
        /** @var CreditCard $card */
        $card = $this->entityManager->getRepository(CreditCard::class)->getFreshEntity();
        $form = $this->createForm(CreditCardFormType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted()
            && count($this->validator->validate($card)) === 0
            && $this->cardService->checkAlgorithmLuna($card->getPAN())) {

            $card->setUser($this->getUser());
            $this->entityManager->getRepository(CreditCard::class)->save($this->cardService->generateToken($card));

            return $this->redirectToRoute('account', ['id' => $card->getId()]);
        }

        return $this->render(
            'card/card-form.html.twig',
            [
                'card_form' => $form->createView()
            ]
        );
    }
}
