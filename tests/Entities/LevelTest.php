<?php

namespace WalkerChiu\MorphRank;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\MorphRank\Models\Entities\Level;
use WalkerChiu\MorphRank\Models\Entities\LevelLang;

class LevelTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\MorphRank\MorphRankServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Level.
     *
     * For WalkerChiu\MorphRank\Models\Entities\Level
     * 
     * @return void
     */
    public function testLevel()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-rank.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-rank.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-rank.soft_delete', 1);

        // Give
        $record_1 = factory(Level::class)->create();
        $record_2 = factory(Level::class)->create();
        $record_3 = factory(Level::class)->create(['is_enabled' => 0]);

        // Get records after creation
            // When
            $records = Level::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $record_2->delete();
            $records = Level::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Level::withTrashed()
                 ->find(2)
                 ->restore();
            $record_2 = Level::find(2);
            $records = Level::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Return Lang class
            // When
            $class = $record_2->lang();
            // Then
            $this->assertEquals($class, LevelLang::class);

        // Scope query on enabled records
            // When
            $records = Level::ofEnabled()
                            ->get();
            // Then
            $this->assertCount(2, $records);

        // Scope query on disabled records
            // When
            $records = Level::ofDisabled()
                            ->get();
            // Then
            $this->assertCount(1, $records);
    }
}
