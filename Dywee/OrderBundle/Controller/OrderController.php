<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\NotificationBundle\Entity\Notification;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Form\BaseOrderType;
use Dywee\OrderBundle\Form\BaseOrderRentType;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function tableAction($state, $page, Request $request)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');

        $query = $or->FindAllForPagination($state);

        //print_r($request->query); exit;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', $page)/*page number*/,
            20/*limit per page*/
        );

        return $this->render('DyweeOrderBundle:Order:table.html.twig', array('pagination' => $pagination));
    }

    public function viewAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Order:view.html.twig', array('order' => $order));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $order = new BaseOrder();
        $order->setIsPriceTTC($this->container->getParameter('dywee_order_bundle.isPriceTTC'));

        $sellType = $this->container->getParameter('dywee_order_bundle.sellType');
        if($sellType == 'default')
        {
            $order->setSellType(1);
            $form = $this->get('form.factory')->create(new BaseOrderType(), $order);
            $template = 'DyweeOrderBundle:Order:add.html.twig';
        }
        else if($sellType == 'rent')
        {
            $order->setSellType(2);
            $form = $this->get('form.factory')->create(new BaseOrderRentType(), $order);
            $template = 'DyweeOrderBundle:Order:addRent.html.twig';
        }


        if($form->handleRequest($request)->isValid())
        {
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('dywee_order_view', array('id' => $order->getId())));
        }
        return $this->render($template, array('form' => $form->createView()));
    }

    public function updateAction(BaseOrder $order, Request $request)
    {
        $form = $this->get('form.factory')->create(new BaseOrderType(), $order);

        if($form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('dywee_order_view', array('id' => $order->getId())));
        }

        return $this->render('DyweeOrderBundle:Order:edit.html.twig', array('order' => $order, 'form' => $form->createView()));
    }

    public function deleteAction(BaseOrder $order)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($order);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', 'Commande Bien effacée');

        return $this->redirect($this->generateUrl('dywee_order_admin_table'));
    }

    public function invoiceViewAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Order:invoice.html.twig', array(
            'order'  => $order
        ));
    }

    public function invoiceDownloadAction(BaseOrder $order)
    {

            /*$html = $this->renderView('DyweeOrderBundle:Order:invoice.html.twig', array(
                'order'  => $order
            ));
            $pdfGenerator = $this->get('spraed.pdf.generator');
            $pdfGenerator->generatePDF($html, 'UTF-8');

            return new Response($pdfGenerator->generatePDF($html),
                200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="out.pdf"'
                )
            );

            */
            $fileName = str_replace(' ', '_', 'files/invoices/'.$order->getInvoiceReference()).'.pdf';

            if(file_exists($fileName))
                unlink($fileName);

            if (true)
            {
                $bill = $this->renderView('DyweeOrderBundle:Order:invoice.html.twig', array(
                    'order'  => $order
                ),
                    true);

                $this->get('knp_snappy.pdf')->generateFromHtml(
                    $bill,
                    $fileName
                );
            //}

            //else{
                $response = new Response();

                // Set headers
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', mime_content_type($fileName));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($fileName) . '";');
                $response->headers->set('Content-length', filesize($fileName));

                // Send headers before outputting anything
                $response->sendHeaders();

                $response->setContent(readfile($fileName));
                /*return new Response(
                        $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('dywee_invoice_view', array('idOrder' => $idOrder), true)),
                        200,
                        array(
                            'Content-Type'          => 'application/pdf',
                            'Content-Disposition'   => 'attachment; filename="LB1X '.$order->getInvoiceReference().'.pdf"'
                        )
                    );
                }*/
            }
    }

    public function exportToCSVAction($type, $data = false)
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');
        $os = $or->findByMonth('current', $type);

        //return $this->render('DyweeOrderBundle:Order:CSVExport.html.twig', array('orderList' => $orders));

        $response = $this->render('DyweeOrderBundle:Order:CSVExport.html.twig', array('orderList' => $os));
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    public function currentTableAction($state)
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $os = $or->findByMonth('current', $state);

        return $this->render('DyweeOrderBundle:Order:table.html.twig', array('orderList' => $os));
    }

    public function paypalConfirmationAction($type, $reference, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $order = $or->findOneBy(
            array(
                'payementInfos' => $request->query->get('paymentId'),
                'reference'     => $reference
            )
        );

        if($order)
        {
            $this->get('session')->set('validatedOrderReference', $order->getReference());
            $data = array('order' => $order);

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

            $paymentId = $request->query->get('paymentId');
            $payment = Payment::get($paymentId, $apiContext);

            // PaymentExecution object includes information necessary
            // to execute a PayPal account payment.
            // The payer_id is added to the request query parameters
            // when the user is redirected from paypal back to your site
            $execution = new PaymentExecution();
            $execution->setPayerId($request->query->get('PayerID'));

            try {
                // Execute the payment
                // (See bootstrap.php for more on `ApiContext`)
                $payment->execute($execution, $apiContext);
            try {
                $payment = Payment::get($paymentId, $apiContext);
            } catch (Exception $ex) {
                throw $this->createException('Something went wrong (Paypal Exception)');
            }
        } catch (Exception $ex) {
                throw $this->createException('Something went wrong (Paypal Exception)');
            }

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
            }

            $message = \Swift_Message::newInstance()
                ->setSubject('Confirmation de votre commande')
                ->setFrom('info@labelgiqueunefois.be')
                ->setTo($order->getBillingAddress()->getEmail())
                ->setBody($this->renderView('DyweeOrderBundle:Email:mail-step1.html.twig', $data))
            ;
            $message->setContentType("text/html");

            $order->setState(1);

            $notification = new Notification();
            $notification->setContent('Une nouvelle commande a été passée');
            $notification->setArgument1($order->getId());
            $notification->setType('order');

            $em->persist($order);
            $em->persist($notification);
            $em->flush();

            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('dywee_order_validated'));
        }
        else throw $this->createNotFoundException('Commande introuvable');

    }

    public function validatedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $or = $em->getRepository('DyweeOrderBundle:BaseOrder');

        $order = new BaseOrder();

        $em->persist($order);
        $em->flush();

        $this->get('session')->set('order', $order);

        $reference = $this->get('session')->get('validatedOrderReference');

        //echo $this->get('session')->get('validatedOrderReference'); exit;

        if($reference)
            return $this->render('DyweeOrderBundle:Order:validated.html.twig',
                array(
                    'order' => $order,
                    'validatedOrderReference' => $reference
                )
            );

        else throw $this->createNotFoundException('Commande introuvable');

        return new Response('OrderController:validatedAction');
    }

    public function invoiceUserViewAction($invoiceReference)
    {
        $or = $this->getDoctrine()->getManager()->getRepository('DyweeOrderBundle:BaseOrder');
        $order = $or->findOneByInvoiceReference($invoiceReference);

        if($order != null)
        {
            return $this->render('DyweeOrderBundle:Order:invoice.html.twig', array(
                'order'  => $order
            ));
        }
        throw $this->createNotFoundException('Commande introuvable');
    }
}