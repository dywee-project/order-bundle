<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\OrderElement;
use Dywee\OrderBundle\Filter\OrderFilterType;
use Dywee\OrderBundle\Form\BaseOrderType;
use Dywee\OrderBundle\Form\BaseOrderRentType;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderAdminController extends Controller
{
    public function listAction($state = 2, $limit = 10, $offset = 0)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');

        $os = $or->findBy(
            array('state' => $state),
            array('creationDate' => 'desc'),
            $limit,
            $offset
        );
        return $this->render('DyweeOrderBundle:Admin:orderTableRaw.html.twig', array('orderList' => $os));
    }

    public function tableAction($state, $page, Request $request)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');


        $form = $this->get('form.factory')->create(new OrderFilterType())
            ->add('chercher', 'submit')
        ;

        $filterActive = false;

        if($form->handleRequest($request)->isValid())
        {
            $query = $or->FindAllForPagination();
            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $query);

            $filterActive = true;
        }
        else $query = $or->FindAllForPagination($state);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', $page), //page number
            20 // limit per page
        );

        $sellType = $this->container->getParameter('dywee_order_bundle.sellType');

        return $this->render('DyweeOrderBundle:Order:table.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $form->createView(),
            'filterActive' => $filterActive,
            'sellType' => $sellType
        ));
    }

    public function paymentOverviewAction(BaseOrder $order)
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->container->getParameter('paypal.clientID'),
                $this->container->getParameter('paypal.clientSecret')
            )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => $this->container->getParameter('paypal.mode'),
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => $this->container->getParameter('paypal.logLevel'), // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS, DEBUG for testing
                'validation.level' => 'log',
                'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            )
        );

        try {
            $payment = Payment::get($order->getPayementInfos(), $apiContext);
        } catch (PayPalConnectionException $ex) {
            throw new \Exception('Erreur dans la recherche du paiement');
        }

        echo '<pre>';
        print_r($payment);
        echo '</pre>'; exit;
    }

    public function renderLastRentingAction($productId)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');
        $os = $or->findLastRentingByProduct($productId);

        return $this->render('DyweeOrderBundle:Order:rentMiniTable.html.twig', array('orderList' => $os));
    }
}