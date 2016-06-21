<?php

namespace ReCaptcha;

/**
 * reCAPTCHA client.
 */
class ReCaptcha {

  /**
   * Version of this client library.
   * @const string
   */
  const VERSION = 'php_1.1.1';

  /**
   * Shared secret for the site.
   * @var type string
   */
  private $secret;

  /**
   * @var Client
   */
  protected $client;
  protected $config = [];
  protected $parameters = [];

  /**
   * Create a configured instance to use the reCAPTCHA service.
   *
   * @param string $secret shared secret between site and reCAPTCHA server.
   * @param  $client method used to send the request. Defaults to POST.
   */
  public function __construct($secret) {
    if (empty($secret)) {
      throw new \RuntimeException('No secret provided');
    }

    if (!is_string($secret)) {
      throw new \RuntimeException('The provided secret must be a string');
    }

    $this->secret = $secret;


    $ua = sprintf('Wordpress/1.0; cURL/%s; (+http://ramirez-portfolio.com)', curl_version()['version']);

    $this->config = [
        'api_protocol' => 'https',
        'api_host' => 'www.google.com/recaptcha/api/siteverify',
        'http_timeout' => 6,
        'http_connect_timeout' => 2,
        'api_version' => '3.0',
        'format' => 'json',
        'headers' => [
            'user-agent' => $ua,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]
    ];

    $this->client = new \GuzzleHttp\Client([
        'base_uri' => $this->buildBasePath(),
        'timeout' => $this->config['http_timeout'],
        'headers' => $this->config['headers']
    ]);
  }

  /**
   * Adds the api_version to the beginning of the path
   * @param string $path
   * @param bool $add_version
   * @return string Returns the corrected path
   */
  protected function buildPath($path, $add_version = false) {
    $path = sprintf('/%s/', $path);
    $path = preg_replace('/[\/]{2,}/', '/', $path);
    if ($add_version && !preg_match('/^\/v1/', $path)) {
      $path = $this->config['api_version'] . $path;
    }
    return $path;
  }

  /**
   * 
   * @return string url base path
   */
  protected function buildBasePath() {
    return $this->config['api_protocol'] . "://" . $this->config["api_host"];
  }

  /**
   * Calls the reCAPTCHA siteverify API to verify whether the user passes
   * CAPTCHA test.
   *
   * @param string $response The value of 'g-recaptcha-response' in the submitted form.
   * @param string $remoteIp The end user's IP address.
   * @return Response Response from the service.
   */
  public function verify($response, $remoteIp = null) {
    // Discard empty solution submissions
    if (empty($response)) {
      $recaptchaResponse = new Response(false, array('missing-input-response'));
      return $recaptchaResponse;
    }
    $q = [];
    $q['secret'] = $this->secret;
    $q['response'] = $response;
    $q['remoteip'] = $remoteIp;
    try {
      $recaptchaResponse = $this->client->request("POST", null, [
          'form_params' => $q
      ]);
    } catch (\GuzzleHttp\Exception\RequestException $ex) {
      $recaptchaResponse = $ex->getResponse();
    }
    return Response::fromJson($recaptchaResponse->getBody()->getContents());
  }

}
