<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 10:00
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Service\Validator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Converter\DataConverter;

/**
 * Class InvoiceValidator
 * @package App\Service\Validator
 */
class InvoiceValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function validate(Request $request): array
    {
        $user = $this->validateUser($request);
        $order = $this->validateOrder($request);
        $amount = $this->validateAmount($request);
        $description = $this->validateDescription($request);

        return [$user, $order, $amount, $description];
    }

    /**
     * @param Request $request
     * @return User|null
     */
    private function validateUser(Request $request): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);
    }

    /**
     * @param Request $request
     * @return string
     */
    private function validateOrder(Request $request): string
    {
        return $request->request->get('order');
    }

    /**
     * @param Request $request
     * @return float
     */
    private function validateAmount(Request $request): float
    {
        $amount = DataConverter::toFloatOrNull($request->request->get('amount'));

        if ($amount !== null) {
            return $amount;
        }

        throw new InvalidTypeException('');
    }

    /**
     * @param Request $request
     * @return string
     */
    private function validateDescription(Request $request): string
    {
        $description = $request->request->get('description');
        if (is_string($description)) {
            return $request->request->get('description');
        }

        throw new InvalidTypeException('');
    }
}
