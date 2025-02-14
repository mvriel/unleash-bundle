<?php

namespace Stogon\UnleashBundle\HttpClient;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnleashHttpClient implements LoggerAwareInterface
{
	private HttpClientInterface $httpClient;
	protected LoggerInterface $logger;
	protected string $apiUrl;
	protected string $instanceId;
	protected string $environment;

	public function __construct(HttpClientInterface $unleashClient, string $apiUrl, string $instanceId, string $environment)
	{
		$this->httpClient = $unleashClient;
		$this->apiUrl = $apiUrl;
		$this->instanceId = $instanceId;
		$this->environment = $environment;
		$this->logger = new NullLogger();
	}

	public function fetchFeatures(): array
	{
		try {
			$response = $this->httpClient->request('GET', 'client/features');
			$features = $response->toArray();
		} catch (\Throwable $th) {
			$this->logger->critical('Could not fetch features flags', [
				'exception' => $th,
			]);

			return [];
		}

		if (array_key_exists('features', $features)) {
			$this->logger->debug('Fetched feature flags from remote', [
				'feature_flags' => $features['features'],
			]);

			return $features['features'];
		}

		return [];
	}

	/**
	 * @return void|null
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}
}
