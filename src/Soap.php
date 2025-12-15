<?php

namespace Meng\Soap;

/**
 * @internal
 */
class Soap extends \SoapClient
{
    private $endpoint;
    private $soapRequest;
    private $soapResponse;
    private $soapAction;
    private $soapVersion;

    public function __construct($wsdl, array $options)
    {
        unset($options['login']);
        unset($options['password']);
        unset($options['proxy_host']);
        unset($options['proxy_port']);
        unset($options['proxy_login']);
        unset($options['proxy_password']);
        unset($options['local_cert']);
        unset($options['passphrase']);
        unset($options['authentication']);
        unset($options['compression']);
        unset($options['trace']);
        unset($options['connection_timeout']);
        unset($options['user_agent']);
        unset($options['stream_context']);
        unset($options['keep_alive']);
        unset($options['ssl_method']);
        parent::__construct($wsdl, $options);
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0, ?string $uriParserClass = null): ?string
    {
        if (null !== $this->soapResponse) {
            return $this->soapResponse;
        }

        $this->endpoint = (string)$location;
        $this->soapAction = (string)$action;
        $this->soapVersion = (string)$version;
        $this->soapRequest = (string)$request;
        return '';
    }

    public function request($function_name, $arguments, $options, $input_headers): SoapRequest
    {
        $this->__soapCall($function_name, $arguments, $options, $input_headers);
        return new SoapRequest($this->endpoint, $this->soapAction, $this->soapVersion, $this->soapRequest);
    }

    public function response($response, $function_name, &$output_headers)
    {
        $this->soapResponse = $response;
        try {
            $response = $this->__soapCall($function_name, [], null, null, $output_headers);
        } catch (\SoapFault $fault) {
            $this->soapResponse = null;
            throw $fault;
        }
        $this->soapResponse = null;
        return $response;
    }
}
