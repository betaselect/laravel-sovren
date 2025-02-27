<?php

namespace Inwave\LaravelSovren\Tests;

use GuzzleHttp\Client;
use Inwave\LaravelSovren\Sovren;
use Orchestra\Testbench\TestCase;
use Inwave\LaravelSovren\SovrenServiceProvider;
use Inwave\LaravelSovren\Facade\Sovren as SovrenFacade;

class SovrenTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SovrenServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['laravel-sovren' => Sovren::class];
    }

    /**
     * @vcr get_account.yml
     * @test
     */
    public function get_account_info(): void
    {
        $response = SovrenFacade::getAccount();
        $this->assertEquals('Success', $response['Info']['Code']);
    }

    /**
     * @vcr parse_resume.yml
     * @test
     */
    public function parse_resume(): void
    {
        $file = file_get_contents('tests/files/resume.pdf');
        $resume = SovrenFacade::parse($file);

        $this->assertCount(2, $resume);
        $this->assertArrayHasKey('Info', $resume);
        $this->assertArrayHasKey('Value', $resume);
        $this->assertEquals('Success', $resume['Info']['Code']);
    }

    /**
     * @vcr wrong_account_parse_resume.yml
     * @test
     */
    public function wrong_account_parse_resume(): void
    {
        $file = file_get_contents('tests/files/resume.pdf');

        $client = new Client([
            'base_uri' => config('sovren.sovren-base-uri'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Sovren-AccountId' => '123456789',
                'Sovren-ServiceKey' => config('sovren.sovren-servicekey'),
                'User-Agent' => 'Laravel'
            ]
        ]);

        $resume = (new Sovren($client))->parse($file);
        $this->assertNotEquals('Success', $resume['Info']['Code']);
    }
}
