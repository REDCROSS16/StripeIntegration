<?php

declare(strict_types=1);

namespace App\Service\PaymentService;

use App\Entity\Invoice;
use App\Entity\Payment;
use App\ENUM\Currency;
use App\ENUM\InvoiceStatus;
use App\ENUM\SubscriptionPlan;
use App\Service\Card\CardService;
use App\Service\Invoice\InvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PaymentService
 * @package App\Service\PaymentService
 */
class PaymentService
{
    private EntityManagerInterface $entityManager;
    private InvoiceService $invoiceService;
    private CardService $cardService;
    private HttpClientInterface $client;

    public function __construct(EntityManagerInterface $entityManager, InvoiceService $invoiceService, CardService $cardService, HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->invoiceService = $invoiceService;
        $this->cardService = $cardService;
        $this->client = $client;
    }

    /**
     * @return EntityRepository
     */
    public function getPaymentsRepo(): EntityRepository
    {
        return $this->entityManager->getRepository(Payment::class);
    }

    /**
     * @param UserInterface $user
     * @return array|null
     */
    public function getPaymentsList(UserInterface $user): ?array
    {
        return $this->getPaymentsRepo()->getPaymentsList($user->getId());
    }

    /**
     * @param UserInterface $user
     * @param string $status
     * @param Invoice $invoice
     * @param string $description
     * @return void
     */
    public function save(UserInterface $user, string $status, Invoice $invoice, string $description)
    {
        /** @var Payment $payment */
        $payment = $this->getPaymentsRepo()->getFreshEntity();
        $payment
            ->setUser($user)
            ->setStatus($status)
            ->setInvoice($invoice)
            ->setDescription($description);

        $this->getPaymentsRepo()->save($payment);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return void
     * @throws Exception
     */
    public function pay(Request $request, UserInterface $user): void
    {
        $amount = round($request->request->get('amount') * 100);
        $currency = Currency::tryFrom($request->request->get('currency'));
        $source = $request->request->get('stripeToken');
        $description = $request->request->get('description');
        $invoice = $this->invoiceService->getInvoiceById($request->request->get('invoice'));

        try {
            Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
            Charge::create([
                "amount" => $amount,
                "currency" => $currency,
                "source" => $source,
                "description" => $description
            ]);

            $this->save($user, InvoiceStatus::COMPLETE->value, $invoice, $description);
        } catch (\Throwable $e) {
            $this->save($user, InvoiceStatus::ERROR->value, $invoice, $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return void
     * @throws ApiErrorException
     */
    public function subscribe(Request $request, UserInterface $user)
    {
        $card = $this->cardService->getActiveCard($user);
        $invoice = $this->invoiceService->getInvoiceById($request->request->get('invoice'));
        $token = $request->request->get('stripeToken');
        $currency = Currency::tryFrom($request->request->get('currency'));
        $email = $user->getUserIdentifier();
        $amount = round($request->request->get('amount') * 100);
        $plan = SubscriptionPlan::tryFrom($request->request->get('plan'));
        $errors = [];

        Stripe::setApiKey($_ENV["STRIPE_KEY"]);
        $customer = Customer::create([
            'email' => $email,
            'source' => $token
        ]);

        try {
            $plan = Plan::create([
                'product' => [
                    'name' => $plan->getSubscriptionPlan()['name']
                    ],
                'amount' => $amount,
                'currency' => $currency->value,
                'interval' => $plan->getSubscriptionPlan()['interval'],
                'interval_count' => 1
            ]);
        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors) && isset($plan)) {
            try {
                $subscription = Subscription::create([
                    'customer' => $customer->id,
                    'items' => [
                        ['plan' => $plan->id]
                    ],
                ]);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (empty($errors) && isset($subscription)) {
            $data = $subscription->jsonSerialize();

            if ($data['status'] === 'active') {
                $invoice->setIsBind(true);
                $invoice->setCard($card);
                $this->invoiceService->getInvoiceRepo()->save($invoice);
                $this->save($user, $data['status'], $invoice, 'subscribed successfully');
            } else {
                $invoice->setIsBind(false);
                $this->invoiceService->getInvoiceRepo()->save($invoice);
                $this->save($user, $data['status'], $invoice, 'subscribed failed');
            }
        }
    }

    /**
     * @return void
     */
    public function cancelSubscribe(Request $request)
    {
        //todo:
        //        Логика отмены подписки для пользователя
        // (обращение support, запрос через api - какие поля нужны будут для этого,
        // либо автоматеческая отмена по cron - по каким критериям).
        // Желательно с примерами, но можно и пояснить словами.
    }

    /**
     * @param int $paymentId
     * @return Payment
     */
    public function getPaymentById(int $paymentId): Payment
    {
        return $this->getPaymentsRepo()->findOneBy(['id' => $paymentId]);
    }


    public function customPay()
    {
        // todo:
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function customSubscription(Request $request, UserInterface $user): void
    {
        $invoice = $this->invoiceService->getInvoiceById($request->request->get('invoice'));
        $currency = Currency::tryFrom($request->request->get('currency'));
        $customPassword = '123456';
        $email = $user->getUserIdentifier();
        $amount = $request->request->get('amount');
        $type = $request->request->get('type');
        $description = $request->request->get('description');
        $card = $this->cardService->getActiveCard($user);

        if ($type === 'subscription') {
            $plan = SubscriptionPlan::tryFrom($request->request->get('plan'));
        }

        $auth = $this->client->request(
            'POST',
            'https://custom-pay/api/auth',
            [
                'json' => [
                    'email' => $email,
                    'password' => $customPassword,
                ]
            ]
        );

        $auth = $auth->toArray();

        if ($auth['status'] === 'success') {
            $response = $this->client->request(
                'POST',
                'https://custom-pay/api/pay',
                [
                    'json' => [
                        'token'    => $auth['token'],
                        'payType'  => $type,
                        'plan'     => $plan ?? null,
                        'amount'   => $amount,
                        'currency' => $currency,
                        'card' => [
                            'pan'        => $card->getPAN(),
                            'expiration' => $card->getExpiration(),
                            'cvv'        => $card->getCvv()
                        ]
                    ]
                ]
            );

            $status = $response->toArray()['status'];
            $this->save($user, $status, $invoice, $description);
        } else {
            $this->save($user, InvoiceStatus::ERROR->value, $invoice, $description);
        }
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function cancelCustomSubscription(Request $request, UserInterface $user): void
    {
        $invoice = $this->invoiceService->getInvoiceById($request->request->get('invoice'));
        $card = $this->cardService->getActiveCard($user);
        $customPassword = '123456';
        $email = $user->getUserIdentifier();

        $auth = $this->client->request(
            'POST',
            'https://custom-pay/api/auth',
            [
                'json' => [
                    'email' => $email,
                    'password' => $customPassword,
                ]
            ]
        );

        $auth = $auth->toArray();

        if ($auth['status'] === 'success') {
            $response = $this->client->request(
                'POST',
                'https://custom-pay/api/cancel-subscription',
                [
                    'json' => [
                        'token'    => $auth['token'],
                        'card' => [
                            'pan'        => $card->getPAN(),
                            'expiration' => $card->getExpiration(),
                            'cvv'        => $card->getCvv()
                        ]
                    ]
                ]
            );

            $status = $response->toArray()['status'];
            $this->save($user, $status, $invoice, 'subscription canceled');
        } else {
            $message = 'failed to cancel subscription';
            $this->save($user, InvoiceStatus::ERROR->value, $invoice, $message);
            throw new Exception($message);
        }
    }
}
