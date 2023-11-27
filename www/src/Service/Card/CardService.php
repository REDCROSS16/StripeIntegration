<?php

declare(strict_types=1);

namespace App\Service\Card;

use App\Entity\CreditCard;
use App\Utils\Converter\DataConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CardService
 * @package App\Service\Card
 */
class CardService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserInterface $user
     * @return array
     */
    public function getAvailableCards(UserInterface $user): array
    {
        return $this->getCardRepo()->getAvailableCards($user->getId());
    }

    /**
     * @param string $token
     * @return CreditCard
     */
    public function getCreditCardByToken(string $token): CreditCard
    {
        return $this->getCardRepo()->getCreditCardByToken($token);
    }

    /**
     * @param string $pan
     * @return bool
     */
    public function checkAlgorithmLuna(string $pan): bool
    {
        $pan = strrev(preg_replace('/\D/', '', $pan));

        $sum = 0;
        for ($i = 0, $j = strlen($pan); $i < $j; $i++) {
            (($i % 2) == 0) ? $value = $pan[$i] : $value = $pan[$i] * 2;

            if ($value > 9) {
                $value -= 9;
            }

            $sum += $value;
        }

        return ($sum % 10) == 0;
    }

    /**
     * @param CreditCard $card
     * @return CreditCard
     */
    public function generateToken(CreditCard $card): CreditCard
    {
        return $card->setToken(DataConverter::toStringOrNull((new \DateTime('now'))->getTimestamp()));
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return array|null
     */
    public function activate(Request $request, UserInterface $user): ?array
    {
        /** @var CreditCard $card */
        $card = $this->getCardRepo()->findOneBy(['id' => $request->get('cardId')]);

        if ($card->getUser()->getId() !== $user->getId()) {
            return [
                'status' => 'error',
                'message' => 'incorrect user'
            ];
        }

        if (!$this->canActivateCard($user)) {
            return [
                'status' => 'error',
                'message' => 'You have active card'
            ];
        }


        try {
            $card->setIsActive(true);
            $this->getCardRepo()->save($card);

            return [
                'status' => 'success',
                'message' => 'cardId' . $card->getId()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


    /**
     * @param Request $request
     * @param UserInterface $user
     * @return array|null
     */
    public function deactivate(Request $request, UserInterface $user): ?array
    {
        /** @var CreditCard $card */
        $card = $this->getCardRepo()->findOneBy(['id' => $request->get('cardId')]);

        if ($card->getUser()->getId() !== $user->getId()) {
            return [
                'status' => 'error',
                'message' => 'incorrect user'
            ];
        }

        try {
            $card->setIsActive(false);
            $this->getCardRepo()->save($card);
            return [
                'status' => 'success',
                'message' => 'cardId' . $card->getId()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @return EntityRepository
     */
    private function getCardRepo(): EntityRepository
    {
        return $this->entityManager->getRepository(CreditCard::class);
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    private function canActivateCard(UserInterface $user): bool
    {
        return $this->getCardRepo()->countListOfActiveCard($user->getId()) < 1;
    }

    /**
     * @param UserInterface $user
     * @return CreditCard
     */
    public function getActiveCard(UserInterface $user): CreditCard
    {
        return $this->getCardRepo()->getActiveCardByUser($user);
    }
}
