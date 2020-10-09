<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;

if (!function_exists('menuActive')) {

	function menuActive($list)
	{
		$class = '';

		if (in_array(str_replace('-', '_', request()->segment(2)), $list, true)) {
			$class = 'active open';
		}
		return $class;
	}
}

if (!function_exists('cashFormat')) {

	function cashFormat($value, $decimals = 2, $p = ',', $p2 = '.')
	{
		return number_format($value, $decimals, $p, $p2);
	}
}

if (!function_exists('formatMask')) {

	function formatMask($val, $mask)
	{
		$maskared = '';
		$k = 0;
		for ($i = 0; $i <= strlen($mask) - 1; $i++) {
			if ($mask[$i] === '#') {
				if (isset($val[$k])) {
					$maskared .= $val[$k++];
				}
			} else if (isset($mask[$i])) {
				$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}
}
if (!function_exists('daysWeek')) {
	function daysWeek()
	{
		return [
			[
				'value' => 'Sunday',
				'name'  => 'Domingo',
			],
			[
				'value' => 'Monday',
				'name'  => 'Segunda-Feira',
			],
			[
				'value' => 'Tuesday',
				'name'  => 'Terça-Feira',
			],
			[
				'value' => 'Wednesday',
				'name'  => 'Quarta-feira',
			],
			[
				'value' => 'Thursday',
				'name'  => 'Quinta-feira',
			],
			[
				'value' => 'Friday',
				'name'  => 'Sexta-feira',
			],
			[
				'value' => 'Saturday',
				'name'  => 'Sábado',
			],
		];
	}
}

if (!function_exists('apiCall')) {
	function apiCall($header, $url, $method = 'get', $parameters = false)
	{
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
