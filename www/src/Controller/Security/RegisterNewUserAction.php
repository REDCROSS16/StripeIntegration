<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/register', name: 'register', methods: ['GET', 'POST'])]
class RegisterNewUserAction extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $hasher;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator,
    ) {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->getFreshEntity();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
            $user->setIsActive(true);

            if (count($this->validator->validate($user)) === 0) {
                $this->entityManager->getRepository(User::class)->save($user);

                return $this->redirectToRoute('account', ['id' => $user->getId()]);
            }
        }

        return $this->render(
            'security/register.html.twig',
            [
                'register_form' => $form->createView()
            ]
        );
    }
}
