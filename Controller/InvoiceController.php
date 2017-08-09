<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Form\BaseOrderRentType;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{

    /**
     * @Route(name="invoice_admin_view", path="admin/invoice/{id}")
     */
    public function viewAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Order:invoice.html.twig', [
            'order' => $order
        ]);
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
        $fileName = str_replace(' ', '_', 'files/invoices/' . $order->getInvoiceReference()) . '.pdf';

        if (file_exists($fileName))
            unlink($fileName);

        if (true) {
            $bill = $this->renderView('DyweeOrderBundle:Order:invoice.html.twig', [
                'order' => $order
            ],
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
                    $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('invoice_view', array('idOrder' => $idOrder), true)),
                    200,
                    array(
                        'Content-Type'          => 'application/pdf',
                        'Content-Disposition'   => 'attachment; filename="LB1X '.$order->getInvoiceReference().'.pdf"'
                    )
                );
            }*/
        }
    }
}