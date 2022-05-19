<?php

namespace BestitKlarnaOrderManagement\Subscriber\Controller\Backend;

use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;
use Smarty;

/**
 * Add modifiers to the smarty template ... such as toCents or toFloat function.
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class TemplateModifiers implements SubscriberInterface
{
    /** @var Smarty */
    protected $smarty;
    /** @var CalculatorInterface */
    protected $calculator;

    public function __construct(Enlight_Template_Manager $template, CalculatorInterface $calculator)
    {
        $this->smarty     = $template->smarty;
        $this->calculator = $calculator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend_BestitOrderManagement' => 'addTemplateModifiers',
        ];
    }

    public function addTemplateModifiers(): void
    {
        $this->smarty->registerPlugin('modifier', 'bestitToCents', [$this->calculator, 'toCents']);
        $this->smarty->registerPlugin('modifier', 'bestitToMajorUnit', [$this->calculator, 'toMajorUnit']);
    }
}
