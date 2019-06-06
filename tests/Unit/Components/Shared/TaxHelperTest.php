<?php

namespace BestitKlarnaOrderManagement\Tests\Unit\Components\Shared;

use BestitKlarnaOrderManagement\Components\Shared\TaxHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test Class for TaxHelper
 *
 * @package BestitKlarnaOrderManagement\Tests\Unit\Components\Shared;
 */
class TaxHelperTest extends TestCase
{
    /**
     * @param array $userData
     * @param array $expected
     *
     * @dataProvider provideUserData
     */
    public function testIsTaxFreeDelivery(array $userData, $expected)
    {
        $taxHelper = new TaxHelper();
        $taxHelper->setUserdata($userData);

        $return = $taxHelper->isTaxFreeDelivery();

        $this->assertEquals($return, $expected);
    }

    /**
     * @return array
     */
    public function provideUserData()
    {
        return [
            'empty_userdata' => [
                [],
                false
            ],
            'not_empty_countryshipping_taxfree' => [
                [
                    'additional' => [
                        'countryShipping' => [
                            'taxfree' => 1
                        ]
                    ]
                ],
                true
            ],
            'empty_countryshipping_taxfree_ustid' => [
                [
                    'additional' => [
                        'countryShipping' => [
                            'taxfree_ustid' => 0
                        ]
                    ]
                ],
                false
            ],
            'taxfree_delivery' => [
                [
                    'shippingaddress' => [
                        'ustid' => ''
                    ],
                    'billingaddress' => [
                        'ustid' => '123456'
                    ],
                    'additional' => [
                        'country' => [
                            'taxfree_ustid' => 1
                        ],
                        'countryShipping' => [
                            'taxfree_ustid' => 1
                        ]
                    ]
                ],
                true
            ],
            'not_empty_shippingaddress_ustid' => [
                [
                    'shippingaddress' => [
                        'ustid' => 123456
                    ],
                    'additional' => [
                        'countryShipping' => [
                            'taxfree_ustid' => 1
                        ]
                    ]
                ],
                true
            ],
            'empty_shippingaddress_ustid' => [
                [
                    'shippingaddress' => [
                        'ustid' => ''
                    ],
                    'additional' => [
                        'countryShipping' => [
                            'taxfree_ustid' => 1
                        ]
                    ]
                ],
                false
            ]
        ];
    }
}
