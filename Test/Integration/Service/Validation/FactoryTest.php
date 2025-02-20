<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Test\Integration\Service\Validation;

use TIG\PostNL\Test\Integration\TestCase;
use \TIG\PostNL\Service\Validation;

class FactoryTest extends TestCase
{
    public $instanceClass = Validation\Factory::class;

    /**
     * @var Validation\Factory
     */
    public $instance;

    public function setUp() : void
    {
        parent::setUp();

        $this->instance = $this->getInstance([
            'validators' => [
                'country' => $this->getObject(Validation\Country::class),
                'region' => $this->getObject(Validation\Region::class),
            ]
        ]);
    }

    public function testCountryValidator()
    {
        $this->assertSame('NL', $this->instance->validate('country', 'NLD'));
    }

    public function testRegionValidator()
    {
        $this->assertSame(12, $this->instance->validate('region', ['country' => 'US', 'region' => 'California']));
    }
}
