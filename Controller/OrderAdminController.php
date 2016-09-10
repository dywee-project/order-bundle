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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderAdminController extends Controller
{
    public function listAction($state = BaseOrder::STATE_IN_PROGRESS, $limit = 10, $offset = 0)
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

    public function tableAction($page, Request $request)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');


        /*$form = $this->get('form.factory')->create(OrderFilterType::class)
            ->add('chercher', SubmitType::class)
        ;

        $filterActive = false;

        if($form->handleRequest($request)->isValid())
        {
            $query = $or->findAllForPagination($state);
            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $query);

            $filterActive = true;
        }
        else */

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $or->findAllForPagination($request->query->get('state') ?? BaseOrder::STATE_IN_PROGRESS),
            $request->query->get('page', $page), //page number
            40 // limit per page
        );



        $data = array(
            'pagination' => $pagination,
            //'searchForm' => $form->createView(),
            //'filterActive' => $filterActive,
            'states' => array(BaseOrder::STATE_IN_SESSION, BaseOrder::STATE_WAITING, BaseOrder::STATE_IN_PROGRESS, BaseOrder::STATE_FINALIZED),
        );

        return $this->render('DyweeOrderBundle:Order:table.html.twig', $data);
    }

    public function viewAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Order:view.html.twig', array('order' => $order));
    }
}