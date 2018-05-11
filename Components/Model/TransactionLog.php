<?php

namespace BestitKlarnaOrderManagement\Components\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="bestit_klarna_transaction_log")
 */
class TransactionLog
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="klarna_order_id", type="string", nullable=false)
     */
    private $klarnaOrderId;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", nullable=false)
     */
    private $action;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_successful", type="boolean", nullable=false)
     */
    private $isSuccessful;

    /**
     * @var string
     *
     * @ORM\Column(name="error_code", type="string", nullable=true)
     */
    private $errorCode;

    /**
     * @var array
     *
     * @ORM\Column(name="error_messages", type="simple_array", nullable=true)
     */
    private $errorMessages;

    /**
     * @var string
     *
     * @ORM\Column(name="correlation_id", type="string", nullable=true)
     */
    private $correlationId;

    /**
     * @var int
     *
     * @ORM\Column(name="cents", type="integer", nullable=true)
     */
    private $cents;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return TransactionLog
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getKlarnaOrderId()
    {
        return $this->klarnaOrderId;
    }

    /**
     * @param string $klarnaOrderId
     *
     * @return TransactionLog
     */
    public function setKlarnaOrderId($klarnaOrderId)
    {
        $this->klarnaOrderId = $klarnaOrderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return TransactionLog
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->isSuccessful;
    }

    /**
     * @param bool $successful
     *
     * @return TransactionLog
     */
    public function setIsSuccessful($successful)
    {
        $this->isSuccessful = $successful;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     *
     * @return TransactionLog
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @param array $errorMessages
     *
     * @return TransactionLog
     */
    public function setErrorMessages($errorMessages)
    {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    /**
     * @return string
     */
    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    /**
     * @param string $correlationId
     *
     * @return TransactionLog
     */
    public function setCorrelationId($correlationId)
    {
        $this->correlationId = $correlationId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCents()
    {
        return $this->cents;
    }

    /**
     * @param int $cents
     *
     * @return TransactionLog
     */
    public function setCents($cents)
    {
        $this->cents = $cents;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return TransactionLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
