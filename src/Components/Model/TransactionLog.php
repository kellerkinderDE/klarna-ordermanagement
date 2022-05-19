<?php

namespace BestitKlarnaOrderManagement\Components\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): TransactionLog
    {
        $this->id = $id;

        return $this;
    }

    public function getKlarnaOrderId(): string
    {
        return $this->klarnaOrderId;
    }

    /**
     * @param string $klarnaOrderId
     */
    public function setKlarnaOrderId($klarnaOrderId): TransactionLog
    {
        $this->klarnaOrderId = $klarnaOrderId;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action): TransactionLog
    {
        $this->action = $action;

        return $this;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @param bool $successful
     */
    public function setIsSuccessful($successful): TransactionLog
    {
        $this->isSuccessful = $successful;

        return $this;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode($errorCode): TransactionLog
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param array $errorMessages
     */
    public function setErrorMessages($errorMessages): TransactionLog
    {
        $this->errorMessages = $errorMessages;

        return $this;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    /**
     * @param string $correlationId
     */
    public function setCorrelationId($correlationId): TransactionLog
    {
        $this->correlationId = $correlationId;

        return $this;
    }

    public function getCents(): int
    {
        return $this->cents;
    }

    /**
     * @param int $cents
     */
    public function setCents($cents): TransactionLog
    {
        $this->cents = $cents;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt): TransactionLog
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
