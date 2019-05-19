<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function tableAction()
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');

        $state = BaseOrder::STATE_IN_PROGRESS;

        $query = $or->FindAllForPagination($state);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            1/*page number*/,
            15/*limit per page*/
        );

        return $this->render('DyweeOrderBundle:Order:miniTable.html.twig', array('pagination' => $pagination));
    }

    public function cardAction()
    {
        $count = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder')->countByState();

        return $this->render('DyweeOrderBundle:Dashboard:card.html.twig', array('count' => $count));
    }
}
