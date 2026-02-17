<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Resources\BranchesApi;
use Bluetech\Sdk\Resources\CurrenciesApi;
use Bluetech\Sdk\Resources\CustomersApi;
use Bluetech\Sdk\Resources\EmissionPointsApi;
use Bluetech\Sdk\Resources\ProductsApi;
use Bluetech\Sdk\Resources\UsersApi;
use Bluetech\Sdk\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class MasterDataResourcesTest extends TestCase
{
    public function testUsersCompaniesEndpoint(): void
    {
        $api = $this->makeUsersApi();
        $api->companies();
        $this->assertLastRequestPath('/api/v1/usuario/empresas');
    }

    public function testCurrenciesAllEndpoint(): void
    {
        $api = $this->makeCurrenciesApi();
        $api->all();
        $this->assertLastRequestPath('/api/v1/monedas/todas');
    }

    public function testCustomersAllEndpoint(): void
    {
        $api = $this->makeCustomersApi();
        $api->all(['idEmpresa' => 10]);
        $this->assertLastRequestPath('/api/v1/cliente/todos');
    }

    public function testCustomersSearchEndpoint(): void
    {
        $api = $this->makeCustomersApi();
        $api->search(['idEmpresa' => 10, 'cliente' => 'acme']);
        $this->assertLastRequestPath('/api/v1/cliente/buscar');
    }

    public function testBranchesAllEndpoint(): void
    {
        $api = $this->makeBranchesApi();
        $api->all(['idEmpresa' => 10]);
        $this->assertLastRequestPath('/api/v1/sucursal/todas');
    }

    public function testEmissionPointsByBranchEndpoint(): void
    {
        $api = $this->makeEmissionPointsApi();
        $api->byBranch(5, ['idEmpresa' => 10]);
        $this->assertLastRequestPath('/api/v1/puntoemision/sucursal/5');
    }

    public function testProductsVariantsEndpoint(): void
    {
        $api = $this->makeProductsApi();
        $api->variants(['idEmpresa' => 10]);
        $this->assertLastRequestPath('/api/v1/producto/variantes');
    }

    public function testProductsSearchEndpoint(): void
    {
        $api = $this->makeProductsApi();
        $api->search(['idEmpresa' => 10, 'producto' => 'plan']);
        $this->assertLastRequestPath('/api/v1/producto/buscar');
    }

    private FakeHttpClient $httpClient;

    private function makeClient(): ApiClient
    {
        $this->httpClient = new FakeHttpClient([new Response(200, [], json_encode([]))]);
        $factory = new HttpFactory();
        return new ApiClient(new Config('https://example.test'), $this->httpClient, $factory, $factory);
    }

    private function makeUsersApi(): UsersApi
    {
        return new UsersApi($this->makeClient());
    }

    private function makeCurrenciesApi(): CurrenciesApi
    {
        return new CurrenciesApi($this->makeClient());
    }

    private function makeCustomersApi(): CustomersApi
    {
        return new CustomersApi($this->makeClient());
    }

    private function makeBranchesApi(): BranchesApi
    {
        return new BranchesApi($this->makeClient());
    }

    private function makeEmissionPointsApi(): EmissionPointsApi
    {
        return new EmissionPointsApi($this->makeClient());
    }

    private function makeProductsApi(): ProductsApi
    {
        return new ProductsApi($this->makeClient());
    }

    private function assertLastRequestPath(string $expectedPath): void
    {
        $this->assertCount(1, $this->httpClient->requests);
        $request = $this->httpClient->requests[0];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame($expectedPath, $request->getUri()->getPath());
    }
}
