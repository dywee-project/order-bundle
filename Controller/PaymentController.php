<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 17/04/17
 * Time: 11:34
 */

namespace Dywee\OrderBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
    /**
     * @Route(path="order/payment/prepare", name="order_payment_prepare")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function prepareAction()
    {
        $gatewayName = 'offline';

        $storage = $this->get('payum')->getStorage('Dywee\OrderBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid('payment', true));
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(123); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'order_payment_done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @Route(path="order/payment/done", name="order_payment_done")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have token you can get the model from the storage directly.
        //$identity = $token->getDetails();
        //$payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        // you have order and payment status
        // so you can do whatever you want for example you can just print status and payment details.

        return new JsonResponse([
            'status'  => $status->getValue(),
            'payment' => [
                'total_amount'  => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details'       => $payment->getDetails(),
            ],
        ]);
    }
}