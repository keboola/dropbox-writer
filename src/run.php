<?php

use Symfony\Component\Yaml\Yaml;
use Dropbox\Client,
	Dropbox\WriteMode;
use Guzzle\Http\Client as Guzzle;

require_once(dirname(__FILE__) . "/../vendor/autoload.php");

$arguments = getopt("d::", array("data::"));
if (!isset($arguments["data"])) {
    print "Data folder not set.";
    exit(1);
}

$config = Yaml::parse(file_get_contents($arguments["data"] . "/config.yml"));

if (empty($config['parameters']['api_key'])) {
	print "Missing configuration parameter 'api_key'!";

	if (!empty($config['parameters']['credentials'])) {

		$guzzle = new Guzzle;
		try {
			$token = getenv('KBC_TOKEN');
			if (empty($token)) {
				print 'KBC_TOKEN env variable not set.';
				exit(1);
			}

			$re = $guzzle->get(
				"https://syrup-testing.keboola.com/oauth/get/wr-dropbox/{$config['parameters']['credentials']}",
				['X-StorageApi-Token' => $token]
			);

			var_dump($re);
		} catch(\Guzzle\Http\Exception\RequestException $e) {
			print "Failed retrieving token from OAuth API: " . $e->getMessage();
			exit($e->getCode() ?: 1);
		}

	} else {
		print "'api_key' or 'credentials' parameter is required";
		exit(2);
	}
} else {
	$apiKey = $config['parameters']['api_key'];
}


if (empty($config['storage']['input']['tables'])) {
	print "No input data found!";
	exit(1);
}

$client = new Client($apiKey, "Keboola Dropbox Writer/0.1");

$path = empty($config['parameters']['path_prefix']) ? "" : $config['parameters']['path_prefix'];

$mode = WriteMode::add(); // TODO configurable

foreach($config['storage']['input']['tables'] as $table) {
// TODO check if destination ain't empty
	$result = $client->uploadFile(
		'/' . $path . $table['destination'],
		$mode,
		fopen($arguments["data"] . "/in/tables/{$table['destination']}", 'r')
	);
	print "Uploaded to {$result['path']}" . PHP_EOL;
}

// if (isset($config["storage"]["input"]["tables"][0]["destination"])) {
//     $sourceFile  = $config["storage"]["input"]["tables"][0]["destination"];
// } else {
//     $sourceFile = $config["storage"]["input"]["tables"][0]["source"];
// }
exit(0);
