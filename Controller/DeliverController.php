<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\Deliver;
use Dywee\OrderBundle\Form\DeliverType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliverController extends AbstractController
{
    /**
     * @Route(name="deliver_table", path="admin/deliver")
     *
     * @return Response
     */
    public function tableAction()
    {
        $dr = $this->getDoctrine()->getRepository(Deliver::class);
        $deliverList = $dr->findAll();

        return $this->render('DyweeOrderBundle:Deliver:table.html.twig', array('delivers' => $deliverList));
    }

    /**
     * @Route(name="deliver_view", path="admin/deliver/{id}")
     */
    public function viewAction(Deliver $deliver)
    {
        return $this->render('DyweeOrderBundle:Deliver:view.html.twig', array('deliver' => $deliver));
    }

    /**
     * @Route(name="deliver_add", path="admin/deliver/create")
     */
    public function addAction(Request $request)
    {
    }

    /**
     * @Route(name="deliver_update", path="admin/deliver/{id}/update")
     */
    public function updateAction(Deliver $deliver, Request $request)
    {
        $form = $this->createForm(DeliverType::class, $deliver);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($deliver);
            $em->flush();

            return $this->redirectToRoute('deliver_table');
        }

        return $this->render('DyweeOrderBundle:Deliver:edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route(name="deliver_delete", path="admin/deliver/{id}/delete")
     */
    public function deleteAction()
    {
        return new Response('delete');
    }
}
