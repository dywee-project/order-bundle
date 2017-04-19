<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\ShippingMethod;
use Dywee\OrderBundle\Form\ShippingMethodType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ShippingMethodController extends Controller
{
    /**
     * @return Response
     *
     * @Route(name="shipping_method_table", path="admin/shipment/method")
     */
    public function tableAction()
    {
        $dr = $this->getDoctrine()->getManager()->getRepository(ShippingMethod::class);
        $shipmentMethodList = $dr->findAll();

        return $this->render('DyweeOrderBundle:ShippingMethod:table.html.twig', array('shipmentMethods' => $shipmentMethodList));
    }

    /**
     * @param Request $request
     * @return mixed
     *
     * @Route(name="shipping_method_add", path="admin/shipment/method/add")
     */
    public function addAction(Request $request)
    {
        $shipmentMethod = new ShippingMethod();

        $form = $this->createForm(ShippingMethodType::class, $shipmentMethod);

        if($form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($shipmentMethod);
            $em->flush();

            return $this->redirectToRoute('shipping_method_table');
        }

        return $this->render('DyweeOrderBundle:ShippingMethod:add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param ShippingMethod $shipmentMethod
     * @return Response
     *
     * @Route(name="shipping_method_view", path="admin/shipment/method/{id}")
     */
    public function viewAction(ShippingMethod $shipmentMethod)
    {
        return $this->render('DyweeOrderBundle:ShippingMethod:view.html.twig', array('shipmentMethods' => $shipmentMethod));
    }

    /**
     * @param ShippingMethod $shipmentMethod
     * @param Request $request
     *
     * @Route(name="shipping_method_update", path="admin/shipment/method/{id}/update")
     */
    public function updateAction(ShippingMethod $shipmentMethod, Request $request)
    {
        $form = $this->createForm(ShippingMethodType::class, $shipmentMethod);

        if($form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($shipmentMethod);
            $em->flush();

            return $this->redirectToRoute('shipping_method_table');
        }

        return $this->render('DyweeOrderBundle:ShippingMethod:edit.html.twig', array('form' => $form->createView()));
    }


    public function searchAjaxAction()
    {
        $request = $this->container->get('request');
        if($request->isXmlHttpRequest())
        {
            $response = new Response();

            $country = $request->request->get('country');

            if(is_numeric($country))
            {
                $em = $this->getDoctrine()->getEntityManager();
                $smr = $em->getRepository(ShippingMethod::class);

                $order = $this->get('session')->get('order');
                $or = $em->getRepository('DyweeOrderBundle:BaseOrder');
                $order = $or->findOneById($order->getId());

                $mondialRelay24R = array('name' => 'Livraison en point relais', 'price' => false);
                $mondialRelayHOM = array('name' => 'Livraison à domicile', 'price' => false);
                $dpd = array('name' => 'Livraison à domicile', 'price' => false);

                /*foreach($order->getOrderElements as $orderElement)
                {
                    $shipmentMethods = $smr->myfindBy($country, $orderElement->getProduct()->getWeight()*$orderElement->getQuantity());
                }

                //$shipmentMethods = $smr->myfindBy($country, $order->getWeight()); //*/

                $order->shipmentsCalculation(true);

                $containsBeers = false;
                $beersWeight = 0;

                //$coeff = count($order->getShipments());
                $coeff = 0;
                $totalCoeff = 0;

                $options = array();



                foreach($order->getOrderElements() as $orderElement)
                {
                    if($orderElement->getProduct()->getProductType() == 1)
                    {
                        $coeff = 1;
                        $totalCoeff = 1;

                        if(!$containsBeers)
                        {
                            $containsBeers = true;
                            $shipmentMethods = $smr->myfindBy($country, $order->getWeight(1));
                        }
                    }
                    else $shipmentMethods = $smr->myfindBy($country, $orderElement->getProduct()->getWeight());

                    if($orderElement->getProduct()->getProductType() == 3)
                    {
                        $coeff = $orderElement->getProduct()->getRecurrence();
                        $totalCoeff += $orderElement->getProduct()->getRecurrence()*$orderElement->getQuantity();
                    }
                    else if($orderElement->getProduct()->getProductType() == 2)
                    {
                        $coeff = 1;
                        $totalCoeff = 1;
                    }

                    if(isset($shipmentMethods))
                    foreach($shipmentMethods as $shipmentMethod)
                    {
                        if($shipmentMethod->getDeliver()->getId() == 2 && $shipmentMethod->getType() == '24R')
                        {
                            $mondialRelay24R['price'] =
                                ($orderElement->getProduct()->getProductType() == 1)?
                                    $shipmentMethod->getPrice()*$coeff:
                                    $shipmentMethod->getPrice()*$coeff*$orderElement->getQuantity();
                            //echo $mondialRelay24R['price'];
                            if(array_key_exists('24R', $options))
                                $options['24R']['price'] += $mondialRelay24R['price'];
                            else $options['24R'] = $mondialRelay24R;


                        }
                        else if($shipmentMethod->getDeliver()->getId() == 2 && $shipmentMethod->getType() == 'HOM')
                        {
                            $mondialRelayHOM['price'] =
                                ($orderElement->getProduct()->getProductType() == 1)?
                                    $shipmentMethod->getPrice()*$coeff:
                                    $shipmentMethod->getPrice()*$coeff*$orderElement->getQuantity();
                            if(array_key_exists('HOM', $options))
                                $options['HOM']['price'] += $mondialRelayHOM['price'];
                            else $options['HOM'] = $mondialRelayHOM;
                        }
                        else if($shipmentMethod->getDeliver()->getId() == 3)
                        {
                            $dpd['price'] =
                                ($orderElement->getProduct()->getProductType() == 1)?
                                    $shipmentMethod->getPrice()*$coeff:
                                    $shipmentMethod->getPrice()*$coeff*$orderElement->getQuantity();
                            if(array_key_exists('dpd', $options))
                                $options['dpd']['price'] += $dpd['price'];
                            else $options['dpd'] = $dpd;
                        }
                    }
                }

                if($order->containsOnlyOneType(3))
                    foreach($options as $key => $value)
                        $options[$key]['priceByShipment'] = $options[$key]['price']/$totalCoeff;

                $response->setContent(json_encode(array(
                    'type' => 'success',
                    'shippingOptions' => $options,
                    'coeff' => $totalCoeff
                )));

            }
            else $response->setContent(json_encode(array(
                'Error' => 'Pays invalide',
            )));

            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }
}
