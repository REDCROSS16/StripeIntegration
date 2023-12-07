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
use App\Utils\Converter\DataConverter;
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
    private const PAY_SIMPLE = 'pay_simple';
    private const PAY_SUBSCRIPTION = 'pay_subscription';


    private EntityManagerInterface $entityManager;
    private InvoiceService $invoiceService;
    private HttpClientInterface $client;

    public function __construct(EntityManagerInterface $entityManager, InvoiceService $invoiceService, HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->invoiceService = $invoiceService;
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
    public function simplePay(Request $request, UserInterface $user): void
    {
        $invoice = $this->invoiceService->getInvoiceById((int) $request->request->get('invoiceId'));
        $amount = (int) $request->request->get('amount');
        $email = $request->request->get('email');
        $currency = Currency::tryFrom($request->request->get('currency'));
        $source = $request->request->get('stripeToken');
        $description = $request->request->get('description');

        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        $errors = [];

        try {
            $customer = Customer::create(array(
                'email' => $email,
                'source'  => $source
            ));

        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors) && isset($customer)) {
            try {
                $charge = Charge::create(array(
                    'customer' => $customer->id,
                    'amount'   => $amount,
                    'currency' => $currency->value,
                    'description' => $description
                ));

                $chargeJson = $charge->jsonSerialize();

                // save information about payments
                $this->save($user, InvoiceStatus::COMPLETE->value, $invoice, $description);

            } catch (Exception $e) {

                // save information about payments
                $this->save($user, InvoiceStatus::ERROR->value, $invoice, $e->getMessage());
                throw new Exception($e->getMessage());
            }
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
        $invoice = $this->invoiceService->getInvoiceById((int) $request->request->get('invoiceId'));
        $token = $request->request->get('stripeToken');
        $currency = Currency::tryFrom($request->request->get('currency'));
        $email = $user->getUserIdentifier();
        $amount = (int) $request->request->get('amount');
        $plan = SubscriptionPlan::tryFrom($request->request->get('plan')) ?? SubscriptionPlan::PLAN_WEEK->value;
        $errors = [];

        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
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
                'interval' => $plan->value,
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
//            $invoice->saveData(json_encode($data));
//            $data['id'] - айди подписки
            if ($invoice === null) {
                return;
            }

            if ($data['status'] === 'active') {
                $invoice->setStatus(InvoiceStatus::SUBSCRIBED->value);
                $this->invoiceService->getInvoiceRepo()->save($invoice);
                $this->save($user, $data['status'], $invoice, 'subscribed successfully');
            } else {
                $invoice->setStatus(InvoiceStatus::ERROR->value);
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

        // Set your secret key. Remember to switch to your live secret key in production.
// See your keys here: https://dashboard.stripe.com/apikeys
//        $stripe = new \Stripe\StripeClient('sk_test_51OEXP3ETEPPS0c4EfmJ1IJ8EeWY569YMJV57pQwnLQ0YsHEdQkuRdwvoBw0EqEJVeqcmZKsgeDaC1E3LEv1T77in00kiMLgB42');

//        $stripe->subscriptions->cancel('sub_49ty4767H20z6a', []);

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
}
