<?php

namespace Dywee\OrderBundle\Controller;

use Dywee\CoreBundle\Controller\ParentController;
use Dywee\OrderBundle\Entity\BaseOrder;
use Dywee\OrderBundle\Entity\Shipment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShipmentController extends ParentController
{
    /**
     * @Route(name="shipment_table", path="admin/order/{id}/shipments")
     */
    public function shipmentTableAction(BaseOrder $order)
    {
        return $this->render('DyweeOrderBundle:Shipment:table.html.twig', array('shipments' => $order->getShipments()));
    }

    /**
     * @Route(name="shipment_view", path="admin/shipment/{id}", requirements={"id": "\d+"})
     */
    public function shipmentViewAction(Shipment $shipment, $parameters = null)
    {
        return parent::viewAction($shipment, $parameters);
    }

    /**
     * @Route(name="shipment_edit", path="admin/shipment/{id}/edit")
     */
    public function shipmentUpdateAction(Shipment $object, Request $request, $parameters = null)
    {
        $parameters['redirectTo'] = 'shipment_table';
        $parameters['routingArgs']['id'] = $object->getOrder()->getId();
        return parent::updateAction($object, $request, $parameters);
    }

    public function downloadAction(Shipment $shipment)
    {
        $fileName = /*'files/sendNotes/*/'envoi_'.$shipment->getId().'.pdf';
        if (!file_exists($fileName))
        {
            $note = $this->renderView('DyweeOrderBundle:Shipment:note.html.twig', array('shipment' => $shipment));

            return $this->render('DyweeOrderBundle:Shipment:note.html.twig', array('shipment' => $shipment));

            $pdfGenerator = $this->get('spraed.pdf.generator');

            $pdfGenerator->generatePDF($note, 'UTF-8');

            return new Response($pdfGenerator->generatePDF($note),
                200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$fileName.'"'
                )
            );//*/

            $this->get('knp_snappy.pdf')->generateFromHtml(
                $note,
                $fileName
            );//*/
        }

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
            $this->get('knp_snappy.pdf')->getOutput($this->generateUrl('dywee_shipment_note_view', array('idShipment' => $idShipment), true)),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="invoice07.pdf"'
            )
        );*/
    }

    public function viewNoteAction(Shipment $shipment)
    {
        $this->container->get('profiler')->disable();
        return $this->render('DyweeOrderBundle:Shipment:note.html.twig', array('shipment' => $shipment));
    }

    public function sendAction(Shipment $shipment)
    {

    }
}
