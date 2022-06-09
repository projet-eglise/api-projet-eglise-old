<?php

declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChurchesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChurchesTable Test Case
 */
class ChurchesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChurchesTable
     */
    protected $Churches;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Churches',
        'app.Users',
        'app.Addresses',
        'app.Roles',
        'app.RoleOptions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Churches') ? [] : ['className' => ChurchesTable::class];
        $this->Churches = $this->getTableLocator()->get('Churches', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Churches);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ChurchesTable::validationDefault()
     */
    public function testAddingACascadingChurchWithNewPastor(): void
    {
        $data = [
            'name' => 'Test church',
            'uid' => uniqid(),
            'main_administrator_id' => 2,
            'pastor' => [
                'uid' => uniqid(),
                'firstname' => 'I am',
                'lastname' => 'The Pastor',
                'email' => 'iam@thepastor.fr',
                'password' => 'X',
                'phone_number' => '+00 0 00 00 00 00',
                'birthdate' => '1900-01-01',
                'has_profile_picture' => false,
            ],
            'address' => [
                'uid' => uniqid(),
                'address' => '1 Paradise Street',
                'postal_code' => '00000',
                'city' => 'The Kingdom of God',
            ]
        ];
        $church = $this->Churches->newEntity($data, [
            'associated' => [
                'Address',
                'Pastor',
            ]
        ]);

        $church = $this->Churches->save($church);
        $this->assertNotFalse($church);
        $actualChurch = $this->Churches->get($church->church_id, [
            'associated' => [
                'Address',
                'Pastor',
            ]
        ]);

        $this->assertEquals('Test church', $actualChurch->name);
        $this->assertEquals($church->uid, $actualChurch->uid);
        $this->assertEquals(2, $actualChurch->main_administrator_id);

        $this->assertNotNull($actualChurch->address_id);
        $actualChurch->hydrate('address');
        $church->hydrate('address');
        $this->assertEquals('1 Paradise Street', $actualChurch->address->address);
        $this->assertEquals('00000', $actualChurch->address->postal_code);
        $this->assertEquals('The Kingdom of God', $actualChurch->address->city);
        $this->assertEquals($church->address->uid, $actualChurch->address->uid);

        $this->assertNotNull($actualChurch->pastor_id);
        $actualChurch->hydrate('pastor');
        $church->hydrate('pastor');
        $this->assertEquals($church->pastor->uid, $actualChurch->pastor->uid);
        $this->assertEquals('I am', $actualChurch->pastor->firstname);
        $this->assertEquals('The Pastor', $actualChurch->pastor->lastname);
        $this->assertEquals('iam@thepastor.fr', $actualChurch->pastor->email);
        $this->assertEquals('X', $actualChurch->pastor->password);
        $this->assertEquals('+00 0 00 00 00 00', $actualChurch->pastor->phone_number);
    }
}
