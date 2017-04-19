<?php

namespace Dywee\OrderBundle\Gateway;

use Doctrine\ORM\EntityManager;
use Dywee\OrderBundle\Entity\Shipment;

class MondialRelay
{

    private $em;
    private $mailer;

    public function __construct(EntityManager $entityManager, \Swift_Mailer $mailer)
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
    }

    public function getLabel(Shipment $shipment)
    {
        if ($shipment) {
            $order = $shipment->getOrder();
            $shippingAddress = $order->getShippingAddress() ?? $order->getBillingAddress();

            $client = new \nusoap_client('http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL', true);

            $params = array(
                'Enseigne' => "BEBLCBLC",
                'ModeCol' => 'REL',
                'ModeLiv' => $shipment->getShippingMethod()->getType(),
                'Expe_Langage' => 'FR',
                'Expe_Ad1' => 'La Belgique une Fois',
                'Expe_Ad3' => 'Vieux chemin de nivelles 124',
                'Expe_Ville' => 'Braine-le-chateau',
                'Expe_CP' => '1440',
                'Expe_Pays' => 'BE',
                'Expe_Tel1' => '+330635932525',
                'Expe_Mail' => 'info@labelgiqueunefois.com',

                'Dest_Langage' => $shipment->getOrder()->getLocale(),

                'Dest_Ad1' => $shippingAddress->getFirstName() . ' ' . $shippingAddress->getLastName(),
                'Dest_Ad3' => $shippingAddress->getAddress1(),
                'Dest_Ad4' => $shippingAddress->getAddress2(),
                'Dest_Ville' => $shippingAddress->getCityString(),
                'Dest_CP' => $shippingAddress->getZip(),
                'Dest_Pays' => $shippingAddress->getCountry()->getIso(),
                'Dest_Tel1' => $shippingAddress->getPhone(),
                'Dest_Mail' => $shippingAddress->getEmail(),

                'Poids' => $shipment->getWeight(),
                'NbColis' => '1',
                'CRT_Valeur' => '0',
                'COL_Rel_Pays' => 'BE',
                'COL_Rel' => '006412'
            );

            if ($shippingAddress->getCompany() !== '')
                $param['Dest_Ad2'] = $shippingAddress->getCompany();

            if ($shipment->getShippingMethod()->getType() === '24R') {
                $relai = explode('-', $order->getDeliveryInfo());
                $params['LIV_Rel_Pays'] = $relai[0];
                $params['LIV_Rel'] = $relai[1];
            }

            $security = '';
            foreach ($params as $param)
                $security .= $param;
            $security .= 'xgG1mpth';

            $params['Security'] = strtoupper(md5($security));

            $result = $client->call('WSI2_CreationEtiquette', $params, 'http://www.mondialrelay.fr/webservice/', 'http://www.mondialrelay.fr/webservice/WSI2_CreationEtiquette');


            if (array_key_exists('STAT', $result['WSI2_CreationEtiquetteResult']) && is_numeric($result['WSI2_CreationEtiquetteResult']['STAT']) && $result['WSI2_CreationEtiquetteResult']['STAT'] === 0) {
                $shipment->setTracingInfos($result['WSI2_CreationEtiquetteResult']['ExpeditionNum']);
                $shipment->setState(Shipment::STATE_SHIPPING);

                //Envoi de l'email disant que le colis a quitté l'entrepot
                if (!$shipment->getMailStep() !== Shipment::MAIL_STEP_SHIPPED) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('La Belgique une fois - Votre colis est parti')
                        ->setFrom('info@labelgiqueunefois.be')
                        ->setTo($shipment->getOrder()->getBillingAddress()->getEmail())
                        ->setBody($this->renderView('DyweeOrderBundle:Email:mail-step3.html.twig', array('order' => $shipment->getOrder())));
                    $message->setContentType("text/html");

                    $this->mailer->send($message);
                    if ($shipment->getOrder()->isGift() === 1) {
                        $message = \Swift_Message::newInstance()
                            ->setSubject('La Belgique une fois - Un colis pour vous est parti')
                            ->setFrom('info@labelgiqueunefois.be')
                            ->setTo($shipment->getOrder()->getShippingAddress()->getEmail())
                            ->setBody($this->renderView('DyweeOrderBundle:Email:mail-step4.html.twig', array('order' => $shipment->getOrder())));
                        $message->setContentType("text/html");
                        $this->get('mailer')->send($message);
                    }
                    $shipment->setMailStep(Shipment::MAIL_STEP_SHIPPED);

                }

                $this->em->persist($shipment);
                $this->em->flush();

                return $this->redirect('http://www.mondialrelay.be/' . $result['WSI2_CreationEtiquetteResult']['URL_Etiquette']);
            }
            throw new \Exception($this->createNotFoundException('erreur: ' . $result['WSI2_CreationEtiquetteResult']['STAT']));
        }
    }

    public function getStatus(Shipment $shipment)
    {
        $client = new \nusoap_client("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL", true);
        $client->soap_defencoding = 'utf-8';

        $params = array(
            'Enseigne' => "BEBLCBLC",
            'Expedition' => $shipment->getTracingInfos(),
            'Langue' => 'FR'
        );
        $security = '';
        foreach ($params as $param)
            $security .= $param;
        $security .= 'xgG1mpth';
        $params['Security'] = strtoupper(md5($security));

        $result = $client->call(
            'WSI2_TracingColisDetaille',
            $params,
            'http://www.mondialrelay.fr/webservice/',
            'http://www.mondialrelay.fr/webservice/WSI2_CreationEtiquette'
        );

        if (array_key_exists('STAT', $result['WSI2_TracingColisDetailleResult']) && is_numeric($result['WSI2_TracingColisDetailleResult']['STAT'])) {
            if ($result['WSI2_TracingColisDetailleResult']['STAT'] === 80) {
                if ($shipment->getState() !== 6) {
                    $shipment->setState(6);
                }
            } else if ($result['WSI2_TracingColisDetailleResult']['STAT'] === 81) {
                $shipment->setState(Shipment::STATE_SHIPPING);

            } else if ($result['WSI2_TracingColisDetailleResult']['STAT'] === 82 && $shipment->getState() !== Shipment::STATE_ARRIVED) {
                $shipment->setState(Shipment::STATE_ARRIVED);
                if (!$shipment->getMailStep() !== Shipment::MAIL_STEP_ARRIVED) {
                    if ($shipment->getOrder()->isGift() === true) {

                        $locale = $shipment->getOrder()->getLocale() ? ($shipment->getOrder()->getLocale() . '.') : '';
                        $emailTemplate5 = 'DyweeOrderBundle:Email:mail-step5.' . $locale . 'html.twig';

                        $message = \Swift_Message::newInstance()
                            ->setSubject('La Belgique une fois - Le colis a été réceptionné')
                            ->setFrom('info@labelgiqueunefois.com')
                            ->setTo($shipment->getOrder()->getBillingAddress()->getEmail())
                            ->setBody($this->renderView($emailTemplate5, array('order' => $shipment->getOrder())));
                        $message->setContentType("text/html");

                        $this->mailer->send($message);
                    }
                    $shipment->setMailStep(Shipment::MAIL_STEP_ARRIVED);
                }
            }
        }

        $this->em->persist($shipment);
        $this->em->flush();
    }

}