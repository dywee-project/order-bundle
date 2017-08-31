<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\BaseOrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderAdminController extends Controller
{
    /**
     * @param string $state
     * @param int    $limit
     * @param int    $offset
     *
     * @return Response
     */
    public function listAction($state = BaseOrder::STATE_IN_PROGRESS, $limit = 10, $offset = 0)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');

        $os = $or->findBy(
            ['state' => $state],
            ['createdAt' => 'desc'],
            $limit,
            $offset
        );

        return $this->render('DyweeOrderBundle:Admin:orderTableRaw.html.twig', ['orderList' => $os]);
    }

    public function viewAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Order:view.html.twig', ['order' => $order]);
    }
}