<?php
require 'vendor/autoload.php';

$base_url = $argv[1];
$username = $argv[2];
$password = $argv[3];

$client = new \DKAN\Client("{$base_url}/api/dataset");

$client->login($username, $password);

$node = [
  'type' => 'dataset',
  'title' => 'Test DKAN API Client Dataset'
];

$response = $client->nodeCreate((object) $node);
print_r($response);

$nid = $response->nid;
print_r($client->nodeGet($nid));

$response = $client->nodeUpdate($nid, ['title' => 'Test DKAN API Client Dataset UPDATED']);
print_r($response);

print_r($client->nodeGet($nid));

$response = $client->nodeDelete($nid);
print_r($response);