<?php

namespace BestitKlarnaOrderManagement\Subscriber\Controller\Backend;

use BestitKlarnaOrderManagement\Components\Calculator\CalculatorInterface;
use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;
use Smarty;

/**
 * Add modifiers to the smarty template ... such as toCents or toFloat function.
 *
 * @package BestitKlarnaOrderManagement\Subscriber\Controller\Backend
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class TemplateModifiers implements SubscriberInterface
{
    /** @var Smarty */
    protected $smarty;
    /** @var CalculatorInterface */
    protected $calculator;

    /**
     * @param Enlight_Template_Manager $template
     * @param CalculatorInterface      $calculator
     */
    public function __construct(Enlight_Template_Manager $template, CalculatorInterface $calculator)
    {
        $this->smarty = $template->smarty;
        $this->calculator = $calculator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Backend_BestitOrderManagement' => 'addTemplateModifiers'
        ];
    }

    /**
     * @return void
     */
    public function addTemplateModifiers()
    {
        $this->smarty->registerPlugin('modifier', 'bestitToCents', [$this->calculator, 'toCents']);
        $this->smarty->registerPlugin('modifier', 'bestitToMajorUnit', [$this->calculator, 'toMajorUnit']);
    }
}
