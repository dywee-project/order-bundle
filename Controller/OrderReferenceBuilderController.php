<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\OrderReferenceBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Dywee\CoreBundle\Controller\ParentController;

class OrderReferenceBuilderController extends ParentController
{
    protected $bundleName = 'Dywee\OrderBundle';
    protected $entityName = 'OrderReferenceBuilder';
    protected $tableViewName = 'order_table';

    /**
     * @Route(name="order_reference", path="admin/order/reference/update")
     */
    public function updateAction($id = null, Request $request, $parameters = null)
    {
        $em = $this->getDoctrine()->getManager();
        $orderReferenceBuilderRepository = $em->getRepository('DyweeOrderBundle:OrderReferenceBuilder');
        $orderReferenceBuilder = $orderReferenceBuilderRepository->findById(1);

        // Create default builder if not existing
        if(!$orderReferenceBuilder)
        {
            $orderReferenceBuilder = new OrderReferenceBuilder();
            $em->persist($orderReferenceBuilder);
            $em->flush();
        }

        //Get all iterator
        $referenceIterators = $em->getRepository('DyweeOrderBundle:ReferenceIterator')->findAll();

        $parameters = [
            'redirectTo' => 'order_reference',
            'data' => ['referenceIterators' => $referenceIterators]
        ];

        return parent::updateAction(1, $request, $parameters);
    }
}