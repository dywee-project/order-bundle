<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Dywee\OrderBundle\Form\BaseOrderType;
use Dywee\OrderBundle\Form\BaseOrderRentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends Controller
{
    /**
     * @Route(path="admin/orders", name="order_adminList", defaults={"page": 1})
     * @param Request $request
     *
     * @return Response
     */
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
            $or->findAllForPagination($request->query->get('state')),
            $request->query->get('page', $page), //page number
            40 // limit per page
        );



        $data = array(
            'pagination' => $pagination,
            //'searchForm' => $form->createView(),
            //'filterActive' => $filterActive,
            'activeState' => $request->query->get('state'),
            'states' => array(BaseOrderInterface::STATE_IN_SESSION, BaseOrderInterface::STATE_WAITING, BaseOrderInterface::STATE_IN_PROGRESS, BaseOrderInterface::STATE_FINALIZED),
        );

        return $this->render('DyweeOrderBundle:Order:table.html.twig', $data);
    }

    public function viewAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Order:view.html.twig', array('order' => $order));
    }

    public function addAction($type = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $order = new BaseOrder();
        $order->setIsPriceTTC(/*$this->getParameter('order_bundle_is_price_ttc')*/ true);

        if($type === 'rent')
            $order->setType(BaseOrder::TYPE_ONLY_RENT);

        $form = $this->get('form.factory')->create(BaseOrderType::class, $order);

        if($form->handleRequest($request)->isValid())
        {
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('order_view', array('id' => $order->getId())));
        }
        return $this->render('DyweeOrderBundle:Order:add.html.twig', array('form' => $form->createView()));
    }

    public function updateAction(BaseOrder $order, Request $request)
    {
        //Si c'est une commande de vente
        $form = $this->get('form.factory')->create(BaseOrderType::class, $order);

        if($form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $order->forcePriceCalculation();
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('order_view', array('id' => $order->getId())));
        }

        return $this->render('DyweeOrderBundle:Order:edit.html.twig', array('order' => $order, 'form' => $form->createView()));
    }

    public function deleteAction(BaseOrder $order)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($order);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Commande Bien effacée');

        return $this->redirect($this->generateUrl('order_admin_table'));
    }

    public function validatedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $order = new BaseOrder();

        $em->persist($order);
        $em->flush();

        $this->get('session')->set('order', $order);

        $reference = $this->get('session')->get('validatedOrderReference');

        if($reference)
            return $this->render('DyweeOrderBundle:Order:validated.html.twig',
                array(
                    'order' => $order,
                    'validatedOrderReference' => $reference
                )
            );

        else throw $this->createNotFoundException('Commande introuvable');
    }
}