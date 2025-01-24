<?php

namespace WalkerChiu\MorphRank;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\MorphRank\Models\Entities\Status;
use WalkerChiu\MorphRank\Models\Entities\StatusLang;

class StatusLangTest extends \Orchestra\Testbench\TestCase
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
     * A basic functional test on StatusLang.
     *
     * For WalkerChiu\Core\Models\Entities\Lang
     *     WalkerChiu\MorphRank\Models\Entities\StatusLang
     *
     * @return void
     */
    public function testStatusLang()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-rank.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-rank.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-rank.soft_delete', 1);

        // Give
        factory(Status::class, 2)->create();
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'description']);
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'name']);
        factory(StatusLang::class)->create(['morph_id' => 2, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'name']);
        factory(StatusLang::class)->create(['morph_id' => 2, 'morph_type' => Status::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get records after creation
            // When
            $records = StatusLang::all();
            // Then
            $this->assertCount(6, $records);

        // Get record's morph
            // When
            $record = StatusLang::find(1);
            // Then
            $this->assertNotNull($record);
            $this->assertInstanceOf(Status::class, $record->morph);

        // Scope query on whereCode
            // When
            $records = StatusLang::ofCode('en_us')
                                 ->get();
            // Then
            $this->assertCount(4, $records);

        // Scope query on whereKey
            // When
            $records = StatusLang::ofKey('name')
                                 ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereCodeAndKey
            // When
            $records = StatusLang::ofCodeAndKey('en_us', 'name')
                                 ->get();
            // Then
            $this->assertCount(3, $records);

        // Scope query on whereMatch
            // When
            $records = StatusLang::ofMatch('en_us', 'name', 'Hello')
                                 ->get();
            // Then
            $this->assertCount(1, $records);
            $this->assertTrue($records->contains('id', 1));
    }

    /**
     * A basic functional test on StatusLang.
     *
     * For WalkerChiu\Core\Models\Entities\LangTrait
     *     WalkerChiu\MorphRank\Models\Entities\StatusLang
     *
     * @return void
     */
    public function testStatus()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-morph-rank.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-morph-rank.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-morph-rank.soft_delete', 1);

        // Give
        factory(Status::class, 2)->create();
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'description']);
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'zh_tw', 'key' => 'description']);
        factory(StatusLang::class)->create(['morph_id' => 1, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'name']);
        factory(StatusLang::class)->create(['morph_id' => 2, 'morph_type' => Status::class, 'code' => 'en_us', 'key' => 'name']);
        factory(StatusLang::class)->create(['morph_id' => 2, 'morph_type' => Status::class, 'code' => 'zh_tw', 'key' => 'description']);

        // Get lang of record
            // When
            $record_1 = Status::find(1);
            $lang_1   = StatusLang::find(1);
            $lang_4   = StatusLang::find(4);
            // Then
            $this->assertNotNull($record_1);
            $this->assertTrue(!$lang_1->is_current);
            $this->assertTrue($lang_4->is_current);
            $this->assertCount(4, $record_1->langs);
            $this->assertInstanceOf(StatusLang::class, $record_1->findLang('en_us', 'name', 'entire'));
            $this->assertEquals(4, $record_1->findLang('en_us', 'name', 'entire')->id);
            $this->assertEquals(4, $record_1->findLangByKey('name', 'entire')->id);
            $this->assertEquals(2, $record_1->findLangByKey('description', 'entire')->id);

        // Get lang's histories of record
            // When
            $histories_1 = $record_1->getHistories('en_us', 'name');
            $record_2 = Status::find(2);
            $histories_2 = $record_2->getHistories('en_us', 'name');
            // Then
            $this->assertCount(1, $histories_1);
            $this->assertCount(0, $histories_2);
    }
}
