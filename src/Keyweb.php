<?php
// ************************************************************************************//
// * KeyWeb AG XML API - PHP Client
// * Copyright (c) 2007-2019 Markus Goernhardt
// ************************************************************************************//
// * $Date: 2019-12-19 01:40:56 +0100 (Do, 19 Dez 2019) $
// * $Author: markus $
// * $Rev: 1356 $
// ************************************************************************************//

class Keyweb
{
	private $customer = '';
	private $username = '';
	private $password = '';
	private $url = 'https://xml.status.keyweb.de/index.xml';
	private $version = '1.0.1';
	private $debug = false;
	
	public function __construct(string $customer, string $username, string $password, string $url = null)
	{
		$this->customer = $customer;
		$this->username = $username;
		$this->password = $password;
		if ($url)
		{
			$this->url = $url;
		}
	}
	
	/**
	 * get server list
	 * 
	 * @return array
	 */
	public function GetServerList() : array
	{
		$response = $this->request('GetServerList');
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		$result['servercount'] = (int) $response->content->servercount;
		$result['serverlist'] = [];
		foreach ((array) $response->content->serverlist as $server)
		{
			$result['serverlist'][] = $server;
		}
		return $result;
	}
	
	/**
	 * get server properties
	 * 
	 * @param string $serverid
	 * @return array
	 */
	public function GetServerProperties(string $serverid) : array
	{
		$response = $this->request('GetServerProperties', [
			'serverid' => $serverid,
		]);
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		$result['serverproperties'] = [
			'autoreset'       => (int) $response->content->serverproperties->autoreset,
			'autoresettime'   => (isSet($response->content->serverproperties->autoresettime)) ? (int) $response->content->serverproperties->autoresettime : 0,
			'autoresetcount'  => (isSet($response->content->serverproperties->autoresetcount)) ? (int) $response->content->serverproperties->autoresetcount : 0,
			'cpu'             => (string) $response->content->serverproperties->cpu,
			'memory'          => (string) $response->content->serverproperties->memory,
			'hdd'             => (string) $response->content->serverproperties->hdd,
			'operatingsystem' => (string) $response->content->serverproperties->operatingsystem,
			'macaddr'         => (string) $response->content->serverproperties->macaddr,
			'extendedipcount' => (int) $response->content->serverproperties->extendedipcount,
			'extendedipaddr'  => [],
			'lizenzcount'     => (int) $response->content->serverproperties->lizenz->lizenzcount,
			'lizenz'          => [],
			'resetter'        => (int) $response->content->serverproperties->resetter,
			'serverid'        => (string) $response->content->serverproperties->serverid,
			'servername'      => (string) $response->content->serverproperties->servername,
			'serverip1'       => (string) $response->content->serverproperties->serverip1,
			'serverip2'       => (string) $response->content->serverproperties->serverip2,
			'nameserver1'     => (string) $response->content->serverproperties->nameserver1,
			'nameserver2'     => (string) $response->content->serverproperties->nameserver2,
			'reverselookup1'  => (string) $response->content->serverproperties->reverselookup1,
			'reverselookup2'  => (string) $response->content->serverproperties->reverselookup2,
			'tarif'           => (string) $response->content->serverproperties->tarif,
			'managed'         => (int) $response->content->serverproperties->managed,
		];
		foreach ((array) $response->content->serverproperties->extendedipaddr as $extendedipaddr)
		{
			$result['serverproperties']['extendedipaddr'][] = $extendedipaddr;
		}
		for ($i = 0; $i < $result['serverproperties']['lizenzcount']; $i++)
		{
			$result['serverproperties']['lizenz'][] = [
				'lizenzextinfo' => (string) $response->content->serverproperties->lizenz->{'lizenzextinfo' . $i},
				'lizenztext'    => (string )$response->content->serverproperties->lizenz->{'lizenztext' . $i},
				'lizenzurl'     => (string) $response->content->serverproperties->lizenz->{'lizenzurl' . $i},
			];
		}
		return $result;
	}
	
	/**
	 * push server reset
	 * 
	 * @param string $serverid
	 * @param string $customer
	 * @return array
	 */
	public function Reset(string $serverid, string $customer = '') : array
	{
		$response = $this->request('Reset', [
			'serverid' => $serverid,
			'kundenid' => $customer,
		]);
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		
		return $result;
	}
	
	/**
	 * get reset history
	 * 
	 * @param string $serverid
	 * @return array
	 */
	public function ResetHistory(string $serverid) : array
	{
		$response = $this->request('ResetHistory', [
			'serverid' => $serverid,
		]);
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		$result['historycount'] = (int) $response->content->historycount;
		$result['history'] = [];
		for ($i = 0; $i < $result['historycount']; $i++)
		{
			$result['history'][] = [
				'datetime' => (string) $response->content->history->{'datetime' . $i},
				'komment'  => (string) $response->content->history->{'komment' . $i},
				'user'     => (string) $response->content->history->{'user' . $i},
			];
		}
		return $result;
	}
	
	/**
	 * get traffic data
	 * 
	 * @param string $serverid
	 * @param string $type
	 * @param string $date
	 * @param int $lastslots
	 * @return array
	 */
	public function Traffic(string $serverid, string $type = 'date', string $date = '', int $lastslots = 10) : array
	{
		$_dateTime = function ($date, $time)
		{
			$date_day = substr($date, 0, 2);
			$date_month = substr($date, 3, 2);
			$date_year = substr($date, 6, 4);
			$date_hour = substr($time, 0, 2);
			$date_minute = substr($time, 3, 2);
			$date_second = substr($time, 6, 2);
			
			$newdate = mktime($date_hour, $date_minute, $date_second, $date_month, $date_day, $date_year);
			if (gmdate('I', $newdate) == 1)
			{
				$newdate += 3600;
			}
			return $newdate;
		};
		if (!$date)
		{
			$date = gmdate('d.m.Y');
		}
		$response = $this->request('Traffic', [
			'serverid' => $serverid,
			'requesttype' => $type,
			'lastslots' => $lastslots,
			'date' => $date,
		]);
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		$result['requesttype'] = (string) $response->content->requesttype;
		if ($type == 'today' || $type == 'yesterday' || $type == 'weekly' || $type == 'monthly')
		{
			//$response->content->{$type}->png = str_replace(['&amp;', '&lt;', '&gt;', '&quot;'], ['&' ,'<', '>', '"'], (string) $response->content->{$type}->png);
			$result[$type]['png'] = quoted_printable_decode((string) $response->content->{$type}->png);
		}
		elseif ($type == 'date' || $type == 'lastslots')
		{
			$result['tablecount'] = (int) $response->content->tablecount;
			$result['table'] = [];
			for ($i = 0; $i < $result['tablecount']; $i++)
			{
				$result['table'][] = [
					'time'        => $_dateTime($date, (string) $response->content->table->{'time' . $i}),
					'received'    => (int) $response->content->table->{'received' . $i},
					'transmitted' => (int) $response->content->table->{'transmitted' . $i},
					'traffic'     => (int) $response->content->table->{'traffic' . $i},
				];
			}
		}
		return $result;
	}
	
	/**
	 * set reverse lookup from ipaddress
	 * 
	 * @param string $serverid
	 * @param string $ipaddr
	 * @param string $lookup
	 * @return array
	 */
	public function SetReverseLookup(string $serverid, string $ipaddr, string $lookup) : array
	{
		$response = $this->request('SetReverseLookup', [
			'serverid' => $serverid,
			'ipaddr' => $ipaddr,
			'lookup' => $lookup,
		]);
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		
		return $result;
	}
	
	/**
	 * set server properties
	 * 
	 * @param string $serverid
	 * @param string $nameserver1
	 * @param string $nameserver2
	 * @param string $servername
	 * @return array
	 */
	public function SetServerProperties(string $serverid, string $nameserver1, string $nameserver2, string $servername = '') : array
	{
		$response = $this->request('SetServerProperties', [
			'serverid' => $serverid,
			'serverproperties' => [
				'nameserver1' => $nameserver1,
				'nameserver2' => $nameserver2,
				'servername' => $servername,
			],
		]);
		$result = $this->result($response);
		if ($result['code'] !== 0)
		{
			return $result;
		}
		$result['msg'] = (string) $response->content->command;
		
		return $result;
	}
	
	/**
	 * set debug mode
	 * 
	 * @param bool $debug
	 */
	public function debug(bool $debug)
	{
		$this->debug = !!$debug;
	}
	
	/**
	 * request command
	 * 
	 * @param string $command
	 * @param array $data
	 * @return StdClass|SimpleXMLElement
	 */
	private function request(string $command, array $data = [])
	{
		global $_SERVER;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildXML($command, $data));
		curl_setopt($ch, CURLOPT_USERAGENT, 'KeyWeb API Client ' . $this->version);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (isSet($_SERVER['SERVER_ADDR']))
		{
			// TODO: Verbindungsfehler, wenn SERVER_ADDR eine IPv6 ist, aber $url nur per IPv4 erreichbar ist
			//curl_setopt($ch, CURLOPT_INTERFACE, $_SERVER['SERVER_ADDR']);
		}
		$response = curl_exec($ch);
		if ($this->debug)
		{
			print('<h1>cURL-Request:</h1>');
			print_r(curl_getinfo($ch));
		}
		curl_close($ch);
		
		if ($this->debug)
		{
			print('<h1>Response:</h1>');
			print('<pre>' . htmlspecialchars($response) . '</pre>');
		}
		try
		{
			$xml = new SimpleXMLElement($response);
		}
		catch (Exception $e)
		{
			$xml = null;
		}
		if ($xml === null || !isSet($xml->result->status))
		{
			return (object) [
				'result' => (object) [
					'errno' => 255,
					'error' => 'There was a problem parsing the response.',
					'status' => 'ERR',
				],
			];
		}
		return $xml;
	}
	
	/**
	 * build XML request
	 * 
	 * @param string $command
	 * @param array $data
	 * @return string
	 */
	private function buildXML(string $command, array $data = []) : string
	{
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$root = $dom->createElement('request');
		$content = $dom->createElement('content');
		$content->appendChild($dom->createElement('command', $command));
		self::buildXMLData($dom, $content, $data);
		$content->appendChild($dom->createElement('version', $this->version));
		$root->appendChild($content);
		$login = $dom->createElement('login');
		$login->appendChild($dom->createElement('kundennr', $this->customer));
		$login->appendChild($dom->createElement('user', $this->username));
		$login->appendChild($dom->createElement('password', $this->password));
		$root->appendChild($login);
		$dom->appendChild($root);
		$xml = $dom->saveXML($root);
		if ($this->debug)
		{
			print('<h1>XML-Request:</h1>');
			print('<pre>' . htmlspecialchars($xml) . '</pre>');
		}
		return $xml;
	}
	
	/**
	 * build individual data to XML
	 * 
	 * @param DOMDocument $dom
	 * @param DOMNode $node
	 * @param array $data
	 */
	private static function buildXMLData(\DOMDocument $dom, \DOMNode &$node, array $data)
	{
		if (!is_array($data) && !is_object($data))
		{
			return;
		}
		foreach ($data as $key => $value)
		{
			$tmp = $dom->createElement($key);
			if (is_array($value) || is_object($value))
			{
				$tmp = $dom->createElement($key);
				self::buildXMLData($dom, $tmp, $value);
			}
			else
			{
				$tmp = $dom->createElement($key, $value);
			}
			$node->appendChild($tmp);
		}
	}
	
	/**
	 * get base result
	 * 
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	private function result(\SimpleXMLElement $xml) : array
	{
		return [
			'code'   => (int) $xml->result->errno,
			'msg'    => (string) $xml->result->error,
			'status' => (string) $xml->result->status,
		];
	}
}