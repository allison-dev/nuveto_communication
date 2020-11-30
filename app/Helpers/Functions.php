<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

if (!function_exists('apiCall')) {
	function apiCall($header, $endpoint, $method = 'get', $parameters = false)
	{

        $baseUrl = 'https://app-atl.five9.com/appsvcs/rs/svc/';

        $url = $baseUrl . $endpoint;

		$data = [
			'headers' => $header,
        ];
        
		if ($parameters) {
			$data['parameters'] = json_encode($parameters);
        }
        
		$client = new Client();
		$error = false;
		$msg = false;
		try {
			$response = $client->{$method}($url, $data);
		} catch (ClientException $e) {
			$error = true;
			$msg = $e->getMessage();
		} catch (ServerException $e) {
			$error = true;
			$msg = $e->getMessage();
		} catch (RequestException $e) {
			$error = true;
			$msg = $e->getMessage();
		}
		if ($error) {
			return [
				'success' => false,
				'body'    => $msg,
			];
		}
		$content = json_decode($response->getBody(), true);
		if ($response->getStatusCode() != 200) {
			return [
				'success' => false,
				'code'    => $response->getStatusCode(),
				'body'    => $content,
			];
		}
		return $content;
	}
}