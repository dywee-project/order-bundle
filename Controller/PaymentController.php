<?php
/**
 * Created by PhpStorm.
 * User: Olivier
 * Date: 17/04/17
 * Time: 11:34
 */

namespace Dywee\OrderBundle\Controller;


use Dywee\OrderBundle\Entity\BaseOrderInterface;
use FOS\RestBundle\Controller\Annotations\Route;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $order = $this->get('dywee_order_cms.order_session_handler')->getOrderFromSession();

        $payment = $storage->create();
        $payment->setNumber(uniqid('payment', true));
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount($order->getTotalPrice() * 100); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId($this->getUser()->getId());
        $payment->setClientEmail($this->getUser()->getEmail());

        $storage->update($payment);
        $order->addPayment($payment);
        $order->setPaymentStatus(BaseOrderInterface::PAYMENT_WAITING_VALIDATION);

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
     * @return Response
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
        /** @var PaymentInterface $payment */
        $payment = $status->getFirstModel();
        /** @var BaseOrderInterface $order */
        $order = $payment->getOrder();

        $em = $this->getDoctrine()->getManager();

        if ((float)$payment->getTotalAmount() !== ((float)$order->getTotalPrice()) * 100) {
            throw new \LogicException('amount of the payment is not equal to the amount of the order (' . $payment->getTotalAmount() . '!=' . $order->getTotalPrice());
        }

        $this->get('dywee_order.status_manager')->changePaymentStatus($order, $status);

        $request->getSession()->set('order', null);
        $request->getSession()->set('validatedOrderId', $order->getId());
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('checkout_confirmation');
    }
}