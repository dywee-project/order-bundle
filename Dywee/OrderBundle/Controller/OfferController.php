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

    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $o = $em->getRepository('DyweeOrderBundle:Offer')->findOneById($id);

        if($o != null)
            return $this->render('DyweeOrderBundle:Offer:view.html.twig', array('offer' => $o));

        throw $this->createNotFoundException('Cette commande ne semble plus exister');
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $offer = new Offer();
        $offer->setCreatedBy($this->getUser());
        $offer->setIsPriceTTC($this->container->getParameter('dywee_order_bundle.isPriceTTC'));

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

    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:Offer');

        $offer = $or->findOneById($id);

        if($offer != null)
        {
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
        throw $this->createNotFoundException('Cette offre n\'est plus disponible');

    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:Offer');

        $Offer = $or->findOneById($id);

        if($Offer != null){
            $em->remove($Offer);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Commande Bien effacée');

            return $this->redirect($this->generateUrl('dywee_offer_table'));
        }
        throw $this->createNotFoundException('Commande introuvable');
    }

    public function invoiceDownloadAction($idOffer)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:Offer');
        $Offer = $or->findOneById($idOffer);

        if($Offer != null)
        {
            return new Response(
                $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('dywee_invoice_view', array('idOffer' => $idOffer), true)),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename="invoice07.pdf"'
                )
            );
        }
        throw $this->createNotFoundException('Commande introuvable');
    }

    public function invoiceViewAction($idOffer)
    {
        $this->container->get('profiler')->disable();
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:Offer');
        $Offer = $or->findOneById($idOffer);
        if($Offer != null) {
            return $this->render('DyweeOrderBundle:Offer:invoice.html.twig', array('Offer' => $Offer));
        }
        throw $this->createNotFoundException('Commande introuvable');
    }

    public function confirmationOfferAction($idOffer)
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:Offer');
        $Offer = $or->findOneById($idOffer);
        if($Offer != null)
        {
            //return $this->render('DyweeOrderBundle:Offer:mail-confirmation.html.twig', array('Offer' => $Offer));
            return $this->render('DyweeOrderBundle:Offer:mail-confirmation2.html.twig');
        }
    }

    public function printAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $o = $em->getRepository('DyweeOrderBundle:Offer')->findOneById($id);

        if($o != null)
        {
            $name = 'Offre '.$o->getReference();
            return new Response(
                $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('dywee_offer_rough', array('id' => $id), true)),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename="'.$name.'.pdf"'
                )
            );
        }
        throw $this->createNotFoundException('Cette offre ne semble plus exister');
    }

    public function roughAction($id)
    {
        $this->container->get('profiler')->disable();
        $em = $this->getDoctrine()->getManager();
        $o = $em->getRepository('DyweeOrderBundle:Offer')->findOneById($id);

        if($o != null)
            return $this->render('DyweeOrderBundle:Offer:print.html.twig', array('offer' => $o));

        throw $this->createNotFoundException('Cette offre ne semble plus exister');
    }

    public function sendEmailAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $o = $em->getRepository('DyweeOrderBundle:Offer')->findOneById($id);

        if($o != null) {
            $data = array(
                'subject'   =>  'Fox Sound - Offre '.$o->getReference(),
                'to'        =>  $o->getAddress()->getEmail(),
                'body'      =>  $this->renderView('DyweeOrderBundle:Offer:mail_body.html.twig', array('offer' => $o))
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
        throw $this->createNotFoundException('Cette offre ne semble plus exister');
    }

}