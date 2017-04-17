<?php
namespace Dywee\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Payment extends BasePayment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @var BaseOrderInterface
     * @ORM\ManyToOne(targetEntity="Dywee\OrderBundle\Entity\BaseOrder", inversedBy="payments")
     */
    private $order;

    /**
     * @return BaseOrderInterface
     */
    public function getOrder() : BaseOrderInterface
    {
        return $this->order;
    }

    /**
     * @param BaseOrderInterface $order
     *
     * @return Payment
     */
    public function setOrder(BaseOrderInterface $order) : Payment
    {
        $this->order = $order;

        return $this;
    }


}