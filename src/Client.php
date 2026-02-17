<?php

namespace Bluetech\Sdk;

use Bluetech\Sdk\Resources\RecurringBillingApi;
use Bluetech\Sdk\Resources\SubscriptionsApi;
use Bluetech\Sdk\Resources\UsageApi;
use Bluetech\Sdk\Resources\PriceBooksApi;
use Bluetech\Sdk\Resources\WebhooksApi;
use Bluetech\Sdk\Resources\AuthApi;
use Bluetech\Sdk\Resources\ReferenceDataApi;
use Bluetech\Sdk\Resources\UsersApi;
use Bluetech\Sdk\Resources\CurrenciesApi;
use Bluetech\Sdk\Resources\CustomersApi;
use Bluetech\Sdk\Resources\BranchesApi;
use Bluetech\Sdk\Resources\EmissionPointsApi;
use Bluetech\Sdk\Resources\ProductsApi;
use Bluetech\Sdk\Resources\ComprobantesApi;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client
{
    private ApiClient $apiClient;

    public function __construct(
        Config $config,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->apiClient = new ApiClient($config, $httpClient, $requestFactory, $streamFactory);
    }

    public function api(): ApiClient
    {
        return $this->apiClient;
    }

    public function auth(): AuthApi
    {
        return new AuthApi($this->apiClient);
    }

    public function subscriptions(): SubscriptionsApi
    {
        return new SubscriptionsApi($this->apiClient);
    }

    public function recurringBilling(): RecurringBillingApi
    {
        return new RecurringBillingApi($this->apiClient);
    }

    public function usage(): UsageApi
    {
        return new UsageApi($this->apiClient);
    }

    public function priceBooks(): PriceBooksApi
    {
        return new PriceBooksApi($this->apiClient);
    }

    public function webhooks(): WebhooksApi
    {
        return new WebhooksApi($this->apiClient);
    }

    public function referenceData(): ReferenceDataApi
    {
        return new ReferenceDataApi($this->apiClient);
    }

    public function users(): UsersApi
    {
        return new UsersApi($this->apiClient);
    }

    public function currencies(): CurrenciesApi
    {
        return new CurrenciesApi($this->apiClient);
    }

    public function customers(): CustomersApi
    {
        return new CustomersApi($this->apiClient);
    }

    public function branches(): BranchesApi
    {
        return new BranchesApi($this->apiClient);
    }

    public function emissionPoints(): EmissionPointsApi
    {
        return new EmissionPointsApi($this->apiClient);
    }

    public function products(): ProductsApi
    {
        return new ProductsApi($this->apiClient);
    }

    public function comprobantes(): ComprobantesApi
    {
        return new ComprobantesApi($this->apiClient);
    }
}
