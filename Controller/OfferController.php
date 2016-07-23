<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\Offer;
use Dywee\OrderBundle\Form\OfferRentType;
use Dywee\OrderBundle\Form\OfferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{
    public function listAction()
    {
        return new Response('Offer:list');
    }

    public function tableAction()
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:Offer');
        $os = $or->findAll();

        return $this->render('DyweeOrderBundle:Offer:table.html.twig', array('offerList' => $os));
    }

    public function viewAction(Offer $offer)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('DyweeOrderBundle:Offer:view.html.twig', array('offer' => $o));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $offer = new Offer();
        $offer->setCreatedBy($this->getUser());
        //$offer->setIsPriceTTC($this->container->getParameter('dywee_order_bundle.isPriceTTC'));

        $sellType = $this->container->getParameter('dywee_order_bundle.sellType');
        if($sellType == 'default')
        {
            $offer->setSellType(1);
            $form = $this->get('form.factory')->create(new OfferType(), $offer);
            $template = 'DyweeOrderBundle:Offer:add.html.twig';
        }
        else if($sellType == 'rent')
        {
            $offer->setSellType(2);
            $form = $this->get('form.factory')->create(new OfferRentType(), $offer);
            $template = 'DyweeOrderBundle:Offer:addRent.html.twig';
        }

        if($form->handleRequest($request)->isValid())
        {
            $counter = $this->container->get('dywee_order.counter');
            $reference = $counter->getNextOfferReference();
            $offer->setReference($reference);
            $em->persist($offer);
            $em->flush();

            return $this->redirect($this->generateUrl('dywee_offer_view', array('id' => $offer->getId())));
        }
        return $this->render($template, array('form' => $form->createView()));
    }

    public function updateAction(Offer $offer, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($offer->getState() != 2)
        {
            $form = $this->get('form.factory')->create(new OfferType(), $offer);

            if($form->handleRequest($request)->isValid())
            {
                if($offer->getState() < 2)
                {
                    $em->persist($offer);
                    $em->flush();

                    return $this->redirect($this->generateUrl('dywee_offer_view', array('id' => $offer->getId())));
                }
                else if($offer->getState() == 2)
                {
                    $order = new BaseOrder();
                    $order->setFromOffer($offer);

                    $em->persist($order);
                    $em->flush();

                    return $this->redirect($this->generateUrl('dywee_order_view', array('id' => $order->getId())));
                }
            }

            return $this->render('DyweeOrderBundle:Offer:edit.html.twig', array('offer' => $offer, 'form' => $form->createView()));
        }
        $this->get('session')->getFlashBag()->add('warning', 'Vous ne pouvez pas modifier une offre acceptée. Essayez de modifier la commande');
        return $this->redirect($this->generateUrl('dywee_offer_table'));
    }

    public function deleteAction(Offer $offer)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($offer);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'offre bien effacée');

        return $this->redirect($this->generateUrl('dywee_offer_table'));

    }

    public function invoiceDownloadAction(Offer $offer)
    {
        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('dywee_invoice_view', array('idOffer' => $offer->getId()), true)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="invoice07.pdf"'
            )
        );
    }

    public function invoiceViewAction(Offer $offer)
    {
        $this->container->get('profiler')->disable();

        return $this->render('DyweeOrderBundle:Offer:invoice.html.twig', array('Offer' => $offer));

    }

    public function confirmationOfferAction(Offer $offer)
    {
        //return $this->render('DyweeOrderBundle:Offer:mail-confirmation.html.twig', array('Offer' => $Offer));
        return $this->render('DyweeOrderBundle:Offer:mail-confirmation2.html.twig');

    }

    public function printAction(Offer $offer)
    {
        $name = 'Offre '.$offer->getReference();
        return new Response(
            $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('dywee_offer_rough', array('id' => $offer->getId()), true)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$name.'.pdf"'
            )
        );
    }

    public function roughAction(Offer $offer)
    {
        $this->container->get('profiler')->disable();

        return $this->render('DyweeOrderBundle:Offer:print.html.twig', array('offer' => $offer));
    }

    public function sendEmailAction(Offer $offer)
    {
        $data = array(
            'subject'   =>  'Fox Sound - Offre '.$offer->getReference(),
            'to'        =>  $offer->getAddress()->getEmail(),
            'body'      =>  $this->renderView('DyweeOrderBundle:Offer:mail_body.html.twig', array('offer' => $offer))
        );

        $form = $this->createFormBuilder($data)
                ->add('subject',    'text')
                ->add('from',       'choice',   array('choices' => array('info@foxsound.be' => 'info@foxsound.be', $this->getUser()->getProfessionalEmail() => $this->getUser()->getProfessionalEmail())))
                ->add('to',         'email')
                ->add('body',       'textarea')
                ->add('send',       'submit')
                ->getForm();

        return $this->render('DyweeOrderBundle:Offer:mail_form.html.twig', array('form' => $form->createView()));

    }

}