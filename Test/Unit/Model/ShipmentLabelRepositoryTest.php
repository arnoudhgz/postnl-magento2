<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Unit\Model;

use TIG\PostNL\Model\ShipmentLabelRepository;
use TIG\PostNL\Test\TestCase;

class ShipmentLabelRepositoryTest extends TestCase
{
    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance(array $args = [])
    {
        return $this->objectManager->getObject(ShipmentLabelRepository::class, $args);
    }

    public function testSave()
    {
        $shipmentLabelFactoryMock = $this->getMock(
            '\TIG\PostNL\Model\ShipmentLabelInterface',
            ['getIdentities', 'save']
        );

        $saveExpects = $shipmentLabelFactoryMock->expects($this->once());
        $saveExpects->method('save');

        $instance = $this->getInstance();
        $result = $instance->save($shipmentLabelFactoryMock);

        $this->assertEquals($shipmentLabelFactoryMock, $result);
    }

    public function testgetById()
    {
        $id = rand(1000, 2000);

        $shipmentLabelFactoryMock = $this->getFakeMock('\TIG\PostNL\Model\ShipmentLabelFactory');
        $shipmentLabelFactoryMock->setMethods(['create', 'load', 'getId']);
        $shipmentLabelFactoryMock = $shipmentLabelFactoryMock->getMock();

        $createExpects = $shipmentLabelFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $loadExpects = $shipmentLabelFactoryMock->expects($this->once());
        $loadExpects->method('load');
        $loadExpects->willReturnSelf();

        $getIdExpects = $shipmentLabelFactoryMock->expects($this->once());
        $getIdExpects->method('getId');
        $getIdExpects->willReturn($id);

        $instance = $this->getInstance(['objectFactory' => $shipmentLabelFactoryMock]);
        $result = $instance->getById($id);

        $this->assertInstanceOf('\TIG\PostNL\Model\ShipmentLabelFactory', $result);
        $this->assertEquals($shipmentLabelFactoryMock, $result);
    }

    public function testDelete()
    {
        $shipmentLabelFactoryMock = $this->getMock(
            '\TIG\PostNL\Model\ShipmentLabelInterface',
            ['getIdentities', 'delete']
        );

        $saveExpects = $shipmentLabelFactoryMock->expects($this->once());
        $saveExpects->method('delete');

        $instance = $this->getInstance();
        $result = $instance->delete($shipmentLabelFactoryMock);

        $this->assertTrue($result);
    }
}
