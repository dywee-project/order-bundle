<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\AddressBundle\Entity\Address;
use Dywee\AddressBundle\Entity\Country;
use Dywee\AddressBundle\Form\AddressType;
use Dywee\AddressBundle\Form\ShippingAddressType;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\ProductBundle\Entity\Product;
use Dywee\ShipmentBundle\Entity\ShipmentMethod;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class BasketController extends Controller
{
    public function viewAction(Request $request)
    {
        $order = $this->get('session')->get('order');

        $em = $this->getDoctrine()->getManager();
        $pr = $em->getRepository('DyweeProductBundle:Product');
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        if($order == null)
            $order = $this->newOrderAction();
        else $order = $or->findOneById($order->getId());

        $data = array('order' => $order);

        if($order)
        {
            if ($order->countProducts() > 0) {
                $nbre = $order->countProducts(1);
                if($nbre == 0 || ($nbre >= 8 && $nbre <= 18))
                {
                    $defaultData = array('step1' => 'Type your message here');
                    $form = $this->createFormBuilder($defaultData)
                        ->add('eighteenConfirmation',   'checkbox', array('label' => 'J\'affirme sur l\'honneur avoir plus de 18 ans'))
                        ->add('cuConfirmation',         'checkbox')//, array('label' => 'J\'ai lu et j\'accepte les conditions d\'utilisation'))
                        ->add('Suivant',                'submit')
                        ->add('country',                'entity',   array(
                            'class' => 'DyweeAddressBundle:Country',
                            'property' => 'name'
                        ))
                        ->getForm();

                    if ($form->handleRequest($request)->isValid()) {
                        return $this->redirect($this->generateUrl('dywee_basket_step2'));
                    }

                    $data['form'] = $form->createView();
                }
                else if($nbre > 0 && $nbre < 8)
                    $data['btn'] = '<button disabled="disabled" class="btn btn-default">Vous ne pouvez commander qu\'un minimum de 8 bières individuelles</button>';
                else if($nbre > 18)
                    $data['btn'] = '<button disabled="disabled" class="btn btn-default">Vous ne pouvez commander qu\'un maximum de 18 bières individuelles</button>';

                if($nbre > 15)
                    $this->get('session')->set('info', 'Commander jusqu\'à 18 bières sans frais de transport supplémentaires');
            }
            else {
                $data['btn'] = '<button disabled="disabled" class="btn btn-default">Votre panier est vide</button>';
            }
        }
        else {
            $data = array();
        }

        return $this->render('DyweeOrderBundle:Basket:basket.html.twig', $data);
    }

    public function navSideAction()
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');

        $session = $this->get('session');
        $order = $session->get('order');

        if($order == null)
            $order = $this->newOrderAction();

        else $order = $or->findOneById($order->getId());

        return $this->render('DyweeOrderBundle:Basket:inMenu.html.twig', array('order' => $order));
    }

    public function newOrderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $order = new BaseOrder();
        $order->setState(-1);
        $order->setIsPriceTTC($this->container->getParameter('dywee_order_bundle.isPriceTTC'));
        //$this->get('session')->getFlashBag()->set('success', 'nouvelle commande détectée');
        $em->persist($order);
        $em->flush();

        $this->get('session')->set('order', $order);

        return $order;
    }

    public function step2Action($address_id = null, Request $request)
    {
        $order = $this->get('session')->get('order');

        if($order == null || $order->getId() == null)
        {
            $this->get('session')->getFlashBag()->set('warning', 'Votre session a expirée');
            return $this->redirect($this->generateUrl('dywee_cms_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');
        $cr = $em->getRepository('DyweeAddressBundle:Country');
        $pr = $em->getRepository('DyweeProductBundle:Product');

        $order = $or->findOneById($order->getId());

        if(is_numeric($address_id))
        {
            $ar = $em->getRepository('DyweeAddressBundle:Address');
            $address = $ar->findOneBy(array('id' => $address_id));

            if($address->getUser() == $this->getUser())
            {
                $order->setBillingAddress($address);
                $order->setBillingUser($this->getUser());
                foreach($order->getOrderElements() as $orderElement)
                    $orderElement->setProduct($pr->findOneById($orderElement->getProduct()->getId()));

                $em->persist($order);
                $em->flush();

                $this->get('session')->set('order', $order);

                return $this->redirect($this->generateUrl('dywee_basket_step2'));
            }
            else throw new AccessDeniedException('Vous ne pouvez pas sélectionner cette addresse');
        }

        if($order->getBillingAddress())
            $billingAddress = $order->getBillingAddress();
        else $billingAddress = new Address();

        $form = $this->get('form.factory')->create(new AddressType(), $billingAddress);
        $form->remove('email')->remove('address2')
            ->add('email', 'repeated', array(
                'type' => 'email',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options'  => array('label' => 'Adresse e-mail'),
                'second_options' => array('label' => 'Confirmer Adresse e-mail')
                )
            );

        if($form->handleRequest($request)->isValid())
        {
            $billingAddress->setCountry($cr->findOneById($billingAddress->getCountry()->getId()));
            $order->setBillingAddress($billingAddress);

            foreach($order->getOrderElements() as $orderElement)
                $orderElement->setProduct($pr->findOneById($orderElement->getProduct()->getId()));

            $em->persist($order);
            $em->flush();

            $request->getSession()->set('order', $order);

            return $this->redirect($this->generateUrl('dywee_basket_step3'));
        }
        return $this->render('DyweeOrderBundle:Basket:facturation.html.twig',
            array(
                'form' => $form->createView(),
                'orderConnexionPermission' => $this->container->getParameter('dywee_order_bundle.orderConnexionPermission')
            )
        );
    }

    public function step3Action($address_id = null, Request $request)
    {
        $order = $this->get('session')->get('order');

        if($order == null || $order->getId() == null)
        {
            $this->get('session')->getFlashBag()->set('warning', 'Votre session a expirée');
            return $this->redirect($this->generateUrl('dywee_cms_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');
        $smr = $em->getRepository('DyweeShipmentBundle:ShipmentMethod');
        $ar = $em->getRepository('DyweeAddressBundle:Address');

        $order = $or->findOneById($order->getId());

        if(is_numeric($address_id))
        {
            $shippingAddress = $ar->findOneById($address_id);
            if($shippingAddress != null && $shippingAddress->getUser() == $this->getUser())
            {
                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');
                $order->setShippingUser($this->getUser());

                $em->persist($order);
                $em->flush();

                return $this->step4Action($order, 'HomMethod');
            }
            else throw new AccessDeniedException('Vous ne pouvez pas sélectionner cette addresse');
        }
        $billingAddress = $order->getBillingAddress();


        $liv24R = false;
        $livHOM = false;

        foreach($order->getOrderElements() as $orderElement)
        {
            $shipmentMethods = $smr->myfindBy($order->getBillingAddress()->getCountry(), $orderElement->getProduct()->getWeight());
            foreach($shipmentMethods as $shipmentMethod)
            {
                if($shipmentMethod->getType() == '24R')
                {
                    $liv24R = true;
                    $this->get('session')->set('24RMethod', $shipmentMethod);
                }

                else if($shipmentMethod->getType() == 'HOM')
                {
                    $livHOM = true;
                    $this->get('session')->set('HomMethod', $shipmentMethod);
                }
            }
        }

        //Tierce FORM
        $shippingTierceAddress = new Address();
        $formTierce = $this->get('form.factory')->create(new AddressType(), $shippingTierceAddress);

        //Tierce Form
        $formTierce->remove('email')->remove('address2');
        $formTierce->add('email', 'repeated', array(
            'type' => 'email',
            'invalid_message' => 'Les mots de passe doivent correspondre',
            'options' => array('required' => true),
            'first_options'  => array('label' => 'Adresse e-mail'),
            'second_options' => array('label' => 'Confirmer Adresse e-mail')
        ));
        $formTierce->add('shippingMessage', 'textarea', array('required' => false, 'mapped' => false));

        $data = array('tierce' => $formTierce->createView());

        // HOM FORM
        if($livHOM)
        {
            $shippingAddress = clone $billingAddress;

            $formHome = $this->get('form.factory')->create(new ShippingAddressType(), $shippingAddress);
            $formHome->remove('email')->add('email', 'repeated', array(
                'type' => 'email',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options'  => array('label' => 'Adresse e-mail'),
                'second_options' => array('label' => 'Confirmer Adresse e-mail')
            ))
                ->add('other', 'text', array('required' => false));

            if(isset($formHome) && $formHome->handleRequest($request)->isValid()) {
                $data = $formHome->getData();

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');

                return $this->step4Action($order, 'HomMethod');
            }
            else $data['home'] = $formHome->createView();
        }

        //24R FORM
        if($liv24R)
        {
            $formMR = $this->createFormBuilder(array())
                ->add('country',   'entity', array(
                    'class' => 'DyweeAddressBundle:Country',
                    'property' => 'name',
                    'required' => false,
                    'data' => $shippingAddress->getCountry()
                ))
                ->add('zip', 'text', array('required' => false, 'data' => $shippingAddress->getZip()))
                ->add('ref',   'hidden')
                ->add('mrSave', 'submit')
                ->getForm();

            if($formMR->handleRequest($request)->isValid())
            {
                $cr = $em->getRepository('DyweeAddressBundle:Country');
                $data = $formMR->getData();

                $country = $cr->findOneById($data['country']->getId());

                $shippingAddress->setCountry($country);

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('24R');
                $order->setDeliveryInfo($data['ref']);

                return $this->step4Action($order, '24RMethod');
            }
            else $data['mr'] = $formMR->createView();
        }
        if(isset($formTierce) && $formTierce->handleRequest($request)->isValid())
        {
            $order->setShippingAddress($shippingTierceAddress);
            $order->setIsGift(true);

            $order->setShippingMessage($formTierce->get('shippingMessage')->getData());

            $em->persist($order);
            $em->flush();

            $request->getSession()->set('order', $order);

            return $this->redirect($this->generateUrl('dywee_basket_step3b'));
        }
        $data['step'] = 2;
        $data['orderConnexionPermission'] = $this->container->getParameter('dywee_order_bundle.orderConnexionPermission');
        return $this->render('DyweeOrderBundle:Basket:shipping.html.twig', $data);
    }

    public function step3bAction(Request $request)
    {
        $order = $this->get('session')->get('order');

        if($order == null || $order->getId() == null)
        {
            $this->get('session')->getFlashBag()->set('warning', 'Votre session a expirée');
            return $this->redirect($this->generateUrl('dywee_cms_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');
        $smr = $em->getRepository('DyweeShipmentBundle:ShipmentMethod');

        $order = $or->findOneById($order->getId());
        $shipmentMethods = $smr->myfindBy($order->getShippingAddress()->getCountry(), $order->getWeight());

        $liv24R = false;
        $livHOM = false;


        foreach($shipmentMethods as $shipmentMethod)
        {
            if($shipmentMethod->getType() == '24R')
            {
                $liv24R = true;
                $this->get('session')->set('24RMethod', $shipmentMethod);
            }

            else if($shipmentMethod->getType() == 'HOM')
            {
                $livHOM = true;
                $this->get('session')->set('HomMethod', $shipmentMethod);
            }
        }

        // HOM FORM
        if($livHOM)
        {
            $shippingAddress = $order->getShippingAddress();

            $formHome = $this->get('form.factory')->create(new ShippingAddressType(), $shippingAddress);
            $formHome->remove('email')->add('email', 'repeated', array(
                'type' => 'email',
                'invalid_message' => 'Les mots de passe doivent correspondre',
                'options' => array('required' => true),
                'first_options'  => array('label' => 'Adresse e-mail'),
                'second_options' => array('label' => 'Confirmer Adresse e-mail')
            ))
                ->add('other', 'text', array('required' => false));

            if(isset($formHome) && $formHome->handleRequest($request)->isValid())
            {
                $data = $formHome->getData();

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('HOM');

                return $this->step4Action($order, 'HomMethod');
            }
            else $data['home'] = $formHome->createView();
        }

        //24R FORM
        if($liv24R)
        {
            $formMR = $this->createFormBuilder(array())
                ->add('country',   'entity', array(
                    'class' => 'DyweeAddressBundle:Country',
                    'property' => 'name',
                    'required' => false,
                    'data' => $shippingAddress->getCountry()
                ))
                ->add('zip', 'text', array('required' => false, 'data' => $shippingAddress->getZip()))
                ->add('ref',   'hidden')
                ->add('mrSave', 'submit')
                ->getForm();

            if($formMR->handleRequest($request)->isValid()) {
                $cr = $em->getRepository('DyweeAddressBundle:Country');
                $data = $formMR->getData();

                $country = $cr->findOneById($data['country']->getId());

                $shippingAddress->setCountry($country);

                $order->setShippingAddress($shippingAddress);
                $order->setDeliveryMethod('24R');
                $order->setDeliveryInfo($data['ref']);

                return $this->step4Action($order, '24RMethod');
            }
            else $data['mr'] = $formMR->createView();
        }
        $data['step'] = 3;
        return $this->render('DyweeOrderBundle:Basket:shipping.html.twig', $data);
    }

    public function step4Action($order, $type)
    {
        if($order == null || $order->getId() == null)
        {
            $this->get('session')->getFlashBag()->set('warning', 'Votre session a expirée');
            return $this->redirect($this->generateUrl('dywee_cms_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');
        $order = $or->findOneById($order->getId());
        $smr = $em->getRepository('DyweeShipmentBundle:ShipmentMethod');
        $dr = $em->getRepository('DyweeShipmentBundle:Deliver');

        $shipmentMethodType = $this->get('session')->get($type);

        $order->shipmentsCalculation(true);

        $options = array();

        foreach($order->getOrderElements() as $orderElement)
        {
            if($orderElement->getProduct()->getProductType() == 1)
                $shipmentMethods = $smr->myfindBy($order->getShippingAddress()->getCountry(), $orderElement->getProduct()->getWeight()*$orderElement->getQuantity());
            else $shipmentMethods = $smr->myfindBy($order->getShippingAddress()->getCountry(), $orderElement->getProduct()->getWeight());

            if($orderElement->getProduct()->getProductType() == 3)
                $coeff = $orderElement->getProduct()->getRecurrence();
            else $coeff = 1;

            foreach($shipmentMethods as $shipmentMethod)
            {
                if($shipmentMethod->getDeliver()->getId() == 2 && $shipmentMethod->getType() == '24R')
                {
                    $mondialRelay24R['id'] = $shipmentMethod->getId();
                    if($orderElement->getProduct()->getProductType() == 1)
                        $mondialRelay24R['price'] = $shipmentMethod->getPrice()*$coeff;
                    else
                        $mondialRelay24R['price'] = $shipmentMethod->getPrice()*$coeff*$orderElement->getQuantity();
                    if(array_key_exists('24R', $options))
                        $options['24R']['price'] += $mondialRelay24R['price'];
                    else $options['24R'] = $mondialRelay24R;
                }
                else if($shipmentMethod->getDeliver()->getId() == 2 && $shipmentMethod->getType() == 'HOM')
                {
                    $mondialRelayHOM['id'] = $shipmentMethod->getId();
                    $mondialRelayHOM['price'] = $shipmentMethod->getPrice()*$coeff*$orderElement->getQuantity();
                    if(array_key_exists('HOM', $options))
                        $options['HOM']['price'] += $mondialRelayHOM['price'];
                    else $options['HOM'] = $mondialRelayHOM;
                }
                else if($shipmentMethod->getDeliver->getId() == 2)
                {
                    $dpd['price'] = $shipmentMethod->getPrice()*$coeff*$orderElement->getQuantity();
                    $dpd['id'] = $shipmentMethod->getId();
                    if(array_key_exists('dpd', $options))
                        $options['dpd']['price'] += $dpd['price'];
                    else $options['dpd'] = $dpd;
                }
            }
        }

        $order->setDeliveryCost($options[$shipmentMethodType->getType()]['price']);

        if($shipmentMethodType->getType() == '24R' || $shipmentMethodType->getType() == 'HOM')
            $deliver = $dr->findOneByName('Mondial Relay');

        else $deliver = $dr->findOneByName('DPD');

        $order->setDeliver($deliver);

        $em->persist($order);
        $em->flush();

        return $this->redirect($this->generateUrl('dywee_basket_step5'));
    }

    public function step5Action()
    {
        $order = $this->get('session')->get('order');

        if($order == null || $order->getId() == null)
        {
            $this->get('session')->getFlashBag()->set('warning', 'Votre session a expirée');
            return $this->redirect($this->generateUrl('dywee_cms_homepage'));
        }

        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');
        $order = $or->findOneById($order->getId());

        $data = array('order' => $order);

        if($order->getDeliveryMethod() == '24R')
        {
            $client = new \nusoap_client('http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL', true);

            $explode = explode('-', $order->getDeliveryInfo());

            $params = array(
                'Enseigne' => "BEBLCBLC",
                'Num' => $explode[1],
                'Pays' => $explode[0]
            );

            $security = '';
            foreach($params as $param)
                $security .= $param;
            $security .= 'xgG1mpth';

            $params['Security'] = strtoupper(md5($security));

            $result = $client->call('WSI2_AdressePointRelais', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_AdressePointRelais');

            if($result['WSI2_AdressePointRelaisResult']['STAT'] == 0)
            {
                $data['relais'] = array(
                    'address1'  => $result['WSI2_AdressePointRelaisResult']['LgAdr1'],
                    'address2'  => $result['WSI2_AdressePointRelaisResult']['LgAdr3'],
                    'zip'       => $result['WSI2_AdressePointRelaisResult']['CP'],
                    'cityString' => $result['WSI2_AdressePointRelaisResult']['Ville']
                );
            }
            else throw $this->createNotFoundException('Erreur dans la recherche du point relais');
        }

        return $this->render('DyweeOrderBundle:Basket:recap.html.twig', $data);
    }

    public function removeAction($idProduct)
    {
        $order = $this->get('session')->get('order');

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $order = $or->findOneById($order->getId());

        $pr = $this->getDoctrine()->getManager()->getRepository('DyweeProductBundle:Product');
        $product = $pr->findOneById($idProduct);

        $order->addProduct($product, -1);

        $em->persist($order);
        $em->flush();

        $this->get('session')->set('order', $order);

        return $this->redirect($this->generateUrl('dywee_basket_view'));
    }

    public function addAction($idProduct)
    {
        $order = $this->get('session')->get('order');

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $order = $or->findOneById($order->getId());

        $pr = $this->getDoctrine()->getManager()->getRepository('DyweeProductBundle:Product');
        $product = $pr->findOneById($idProduct);

        $order->addProduct($product, 1);

        $em->persist($order);
        $em->flush();

        $this->get('session')->set('order', $order);
        return $this->redirect($this->generateUrl('dywee_basket_view'));
    }

    public function deleteAction($idProduct)
    {
        $order = $this->get('session')->get('order');

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $order = $or->findOneById($order->getId());

        $order->removeProduct($idProduct);

        $em->persist($order);
        $em->flush();

        $this->get('session')->set('order', $order);

        return $this->redirect($this->generateUrl('dywee_basket_view'));
    }

    /**
     * @return Payment
     */
    public function paypalCheckoutAction()
    {
        $approvalUrl = $this->paypalSetupAction();
        if(isset($approvalUrl))
            return $this->redirect($approvalUrl);
    }

    public function paypalCheckoutAjaxAction()
    {
        $request = $this->container->get('request');
        if($request->isXmlHttpRequest()) {
            $response = new Response();
            $approvalUrl = $this->paypalSetupAction();
            if (isset($approvalUrl)) {
                $response->setContent(json_encode(array(
                    'type' => 'success',
                    'url' => $approvalUrl
                )));
            } else $response->setContent(json_encode(array(
                'Error' => 'Pays invalide',
            )));

            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        else throw $this->createNotFoundException('Requete invalide');
    }

    public function paypalSetupAction()
    {
        $order = $this->get('session')->get('order');

        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $order = $or->findOneById($order->getId());
        $address = $order->getShippingAddress();

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $shippingAddress = new ShippingAddress();
        $shippingAddress->setRecipientName($address->getLastName().' '.$address->getFirstName());
        $shippingAddress->setLine1($address->getAddress1());
        $shippingAddress->setCity($address->getCityString());
        $shippingAddress->setCountryCode($address->getCountry()->getIso());
        $shippingAddress->setPostalCode($address->getZip());
        $shippingAddress->setPhone($address->getMobile());

        $items = array();
        foreach($order->getOrderElements() as $orderElement)
        {
            $item = new Item();

            $item->setName($orderElement->getProduct()->getName());
            $item->setCurrency('EUR');
            $item->setQuantity($orderElement->getQuantity());
            $item->setPrice((string)number_format($orderElement->getUnitPrice(), 2, '.', ''));

            $items[] = $item;
        }

        $itemList = new ItemList();
        $itemList->setItems($items);
        //$itemList->setShippingAddress($shippingAddress);


        $details = new Details();
        $details->setShipping((string) number_format($order->getShippingCost(), 2, '.', ''))
            ->setTax(0)
            ->setSubtotal((string) number_format($order->getPriceVatIncl(), 2, '.', ''));

        $amount = new Amount();
        $amount->setCurrency('EUR')
            ->setTotal($order->getTotalPrice())
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription('Vente au profit de la Belgique une fois');

        $baseUrl = 'http://www.labelgiqueunefois.com/'.$this->get('request')->getBasePath();
        $redirectUrls = new RedirectUrls();


        $redirectUrls->setReturnUrl('http://www.labelgiqueunefois.com/fr/'."paypal-confirmation/success/".$order->getReference())
            ->setCancelUrl('http://www.labelgiqueunefois.com/fr/'."paypal-confirmation/cancelled/".$order->getReference());

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        $payment->setExperienceProfileId($this->container->getParameter('paypal.experienceProfileId')); //live


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
            $payment->create($apiContext);
        } catch (Exception $ex) {
            throw $this->createException('Something went wrong (Paypal Exception)');
        }
        $approvalUrl = $payment->getApprovalLink();

        $this->get('session')->set('paymentId', $payment->getId());

        $order->setPayementInfos($payment->getId());
        $order->setPayementMethod(3);
        $order->setPayementState(0);

        $em->persist($order);
        $em->flush();

        if(isset($approvalUrl))
            return $approvalUrl;
    }
}
