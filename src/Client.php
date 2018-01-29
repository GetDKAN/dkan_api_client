<?php

namespace DKAN;

class Client extends \GuzzleHttp\Client {

  private $csrf_token = NULL;
  private $endpoint;

  public function __construct($endpoint, array $config = []) {
    $this->endpoint = $endpoint;
    $config['cookies'] = true;
    parent::__construct($config);
  }

  public function login($username, $password) {
    $request_url = $this->endpoint . '/user/login';

    $data = array(
      'username' => $username,
      'password' => $password,
    );

    $response = $this->request("POST", $request_url, ['json' => $data]);

    if ($response->getStatusCode() == 200){
      $body = $response->getBody();
      $contents = $body->getContents();
      $decoded = json_decode($contents);
      $this->csrf_token = $decoded->token;
    }
    else {
      throw new \Exception("Unable to login");
    }
  }

  public function nodeGet($nid) {
    if (!$this->csrf_token) {
      throw new \Exception("Client is not logged in.");
    }

    $request_url = $this->endpoint . "/node/{$nid}";

    $response = $this->request("GET", $request_url,
      [
        'headers' => ['X-CSRF-Token' => $this->csrf_token]
      ]
    );

    if ($response->getStatusCode() == '200') {
      $body = json_decode($response->getBody()->getContents());
      return $body;
    }
    else {
      throw new \Exception("Node could not be created");
    }
  }

  public function nodeCreate($node) {
    if (!$this->csrf_token) {
      throw new \Exception("Client is not logged in.");
    }

    $request_url = $this->endpoint . '/node';

    $response = $this->request("POST", $request_url,
      [
        'headers' => ['X-CSRF-Token' => $this->csrf_token],
        'json' => $node
      ]
    );

    if ($response->getStatusCode() == '200') {
      $body = json_decode($response->getBody()->getContents());
      return $body;
    }
    else {
      throw new \Exception("Node could not be created");
    }
  }

  public function nodeUpdate($nid, $updates) {
    $node = $this->nodeGet($nid);

    if ($node) {
      $request_url = $this->endpoint . "/node/{$nid}";

      foreach ($updates as $field => $value) {
        $node->{$field} = $value;
      }

      $response = $this->request("PUT", $request_url, [
        'headers' => [
          'X-CSRF-Token' => $this->csrf_token,
        ],
        'json' => $node,
      ]);

      if ($response->getStatusCode() == '200') {
        $body = json_decode($response->getBody()->getContents());
        return $body;
      }
      else {
        throw new \Exception('Error when updating node.');
      }
    }
    else {
      throw new \Exception('The node could not be found.');
    }
  }

  public function nodeDelete($nid) {
    if (!$this->csrf_token) {
      throw new \Exception("Client is not logged in.");
    }

    $request_url = $this->endpoint . "/node/{$nid}";

    $response = $this->request("DELETE", $request_url, [
      'headers' => [
        'X-CSRF-Token' => $this->csrf_token,
      ]
    ]);

    if ($response->getStatusCode() == '200') {
      $body = json_decode($response->getBody()->getContents());
      return $body;
    }
    else {
      throw new \Exception("Error when trying to delete node.");
    }

  }

}