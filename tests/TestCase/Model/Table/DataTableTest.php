<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DataTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DataTable Test Case
 */
class DataTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DataTable
     */
    public $Data;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.data',
        'app.loc_types',
        'app.locs',
        'app.categories',
        'app.sources'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Data') ? [] : ['className' => 'App\Model\Table\DataTable'];
        $this->Data = TableRegistry::get('Data', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Data);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
