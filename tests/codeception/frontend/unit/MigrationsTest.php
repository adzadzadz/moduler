<?php

use frontend\migrations\FncTables;

class MigrationsTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {   
        $tables = new FncTables([
            'db' => 'strepz_test_fnc_db_01',
            'company_id' => mt_rand()
        ]);
        var_dump($tables->up());
        $this->assertTrue($tables->up());
    }

}