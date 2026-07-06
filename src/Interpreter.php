<?php

namespace Meng\Soap;

class Interpreter
{
    /** @var Soap */
    private $soap;

    /**
     * @param mixed  $wsdl              URI of the WSDL file or NULL if working in non-WSDL mode.
     * @param array  $options           Supported options: location, uri, style, use, soap_version, encoding,
     *                                  exceptions, classmap, typemap, cache_wsdl and features.
     */
    public function __construct($wsdl, array $options = [])
    {
        $this->soap = new Soap($wsdl, $options);
    }

    /**
     * Interpret the given method and arguments to a SOAP request message.
     *
     * @param string $function_name     The name of the SOAP function to interpret.
     * @param array  $arguments         An array of the arguments to $function_name.
     * @param array  $options           An associative array of options.
     *                                  The location option is the URL of the remote Web service.
     *                                  The uri option is the target namespace of the SOAP service.
     *                                  The soapaction option is the action to call.
     * @param mixed  $input_headers     An array of headers to be interpreted along with the SOAP request.
     * @return SoapRequest
     */
    public function request($function_name, array $arguments = [], ?array $options = null, $input_headers = null): SoapRequest
    {
        return $this->soap->request($function_name, $arguments, $options, $input_headers);
    }

    /**
     * Interpret a SOAP response message to PHP values.
     *
     * @param string $response          The SOAP response message.
     * @param string $function_name     The name of the SOAP function to interpret.
     * @param array  $output_headers    If supplied, this array will be filled with the headers from the SOAP response.
     * @return mixed
     * @throws \SoapFault
     */
    public function response($response, $function_name, ?array &$output_headers = null)
    {
        return $this->soap->response($response, $function_name, $output_headers);
    }
}
