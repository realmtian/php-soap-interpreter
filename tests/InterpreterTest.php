<?php

use Meng\Soap\Interpreter;
use Meng\Soap\SoapRequest;
use PHPUnit\Framework\Attributes\Test;

class InterpreterTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function requestWsdlArrayArguments()
    {
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl');
        $request = $interpreter->request('ConversionRate', [['FromCurrency' => 'AFA', 'ToCurrency' => 'ALL']]);
        $this->assertEquals('http://www.webservicex.net/CurrencyConvertor.asmx', $request->getEndpoint());
        $this->assertEquals('http://www.webserviceX.NET/ConversionRate', $request->getSoapAction());
        $this->assertEquals('1', $request->getSoapVersion());
        $this->assertNotEmpty($request->getSoapMessage());
        $this->assertMatchesRegularExpression('/http:\/\/schemas\.xmlsoap\.org\/soap\/envelope\//', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ConversionRate/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/FromCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/AFA/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ToCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ALL/', $request->getSoapMessage());
    }

    #[Test]
    public function requestWsdlObjectArguments()
    {
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl');
        $rate = new ConversionRate;
        $rate->FromCurrency = 'AFA';
        $rate->ToCurrency = 'ALL';
        $request = $interpreter->request('ConversionRate', [$rate]);
        $this->assertEquals('http://www.webservicex.net/CurrencyConvertor.asmx', $request->getEndpoint());
        $this->assertEquals('http://www.webserviceX.NET/ConversionRate', $request->getSoapAction());
        $this->assertEquals('1', $request->getSoapVersion());
        $this->assertNotEmpty($request->getSoapMessage());
        $this->assertMatchesRegularExpression('/http:\/\/schemas\.xmlsoap\.org\/soap\/envelope\//', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ConversionRate/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/FromCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/AFA/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ToCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ALL/', $request->getSoapMessage());
    }

    #[Test]
    public function requestWsdlInputHeaders()
    {
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl');
        $request = $interpreter->request(
            'ConversionRate',
            [['FromCurrency' => 'AFA', 'ToCurrency' => 'ALL']],
            null,
            [new SoapHeader('www.namespace.com', 'test_header', 'header_data')]
        );
        $this->assertEquals('http://www.webservicex.net/CurrencyConvertor.asmx', $request->getEndpoint());
        $this->assertEquals('http://www.webserviceX.NET/ConversionRate', $request->getSoapAction());
        $this->assertEquals('1', $request->getSoapVersion());
        $this->assertNotEmpty($request->getSoapMessage());
        $this->assertMatchesRegularExpression('/http:\/\/schemas\.xmlsoap\.org\/soap\/envelope\//', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/www\.namespace\.com/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/test_header/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/header_data/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ConversionRate/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/FromCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/AFA/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ToCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ALL/', $request->getSoapMessage());
    }

    #[Test]
    public function requestTypeMapToXML()
    {
        $interpreter = new Interpreter(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl',
            [
                'typemap' => [
                    [
                        'type_name' => 'ConversionRate',
                        'type_ns' => 'http://www.webserviceX.NET/',
                        'to_xml' => function() {
                            return "<ConversionRate><FromCurrency>OLD</FromCurrency><ToCurrency>NEW</ToCurrency></ConversionRate>";
                        }
                    ]
                ]
            ]
        );

        $request = $interpreter->request('ConversionRate', [[]]);
        $this->assertEquals('http://www.webservicex.net/CurrencyConvertor.asmx', $request->getEndpoint());
        $this->assertEquals('http://www.webserviceX.NET/ConversionRate', $request->getSoapAction());
        $this->assertEquals('1', $request->getSoapVersion());
        $this->assertNotEmpty($request->getSoapMessage());
        $this->assertMatchesRegularExpression('/http:\/\/schemas\.xmlsoap\.org\/soap\/envelope\//', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ConversionRate/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/FromCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/OLD/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/ToCurrency/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/NEW/', $request->getSoapMessage());
    }

    #[Test]
    public function responseWsdl()
    {
        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ConversionRateResponse xmlns="http://www.webserviceX.NET/">
      <ConversionRateResult>-1</ConversionRateResult>
    </ConversionRateResponse>
  </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl');
        $responseMessage = $interpreter->response($responseMessage, 'ConversionRate');
        $this->assertInstanceOf('\StdClass', $responseMessage);
        $this->assertEquals(['ConversionRateResult' => '-1'], (array)$responseMessage);
    }

    #[Test]
    public function responseWsdlOutputHeaders()
    {
        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <m:Trans xmlns:m="http://www.w3schools.com/transaction/" soap:mustUnderstand="1">
      234
    </m:Trans>
  </soap:Header>
  <soap:Body>
    <ConversionRateResponse xmlns="http://www.webserviceX.NET/">
      <ConversionRateResult>-1</ConversionRateResult>
    </ConversionRateResponse>
  </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl');
        $outputHeaders = [];
        $responseMessage = $interpreter->response($responseMessage, 'ConversionRate', $outputHeaders);
        $this->assertInstanceOf('\StdClass', $responseMessage);
        $this->assertEquals(['ConversionRateResult' => '-1'], (array)$responseMessage);
        $this->assertNotEmpty($outputHeaders);
    }

    #[Test]
    public function responseWsdlClassMap()
    {
        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ConversionRateResponse xmlns="http://www.webserviceX.NET/">
      <ConversionRateResult>-1</ConversionRateResult>
    </ConversionRateResponse>
  </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl', ['classmap' => ['ConversionRateResponse' => '\ConversionRateResponse']]);
        $responseMessage = $interpreter->response($responseMessage, 'ConversionRate');
        $this->assertInstanceOf('\ConversionRateResponse', $responseMessage);
        $this->assertEquals(['ConversionRateResult' => '-1'], (array)$responseMessage);
    }

    #[Test]
    public function responseTypeMapFromXML()
    {
        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ConversionRateResponse xmlns="http://www.webserviceX.NET/">
      <ConversionRateResult>-1</ConversionRateResult>
    </ConversionRateResponse>
  </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'currency.wsdl',
            [
                'typemap' => [
                    [
                        'type_name' => 'ConversionRateResponse',
                        'type_ns' => 'http://www.webserviceX.NET/',
                        'from_xml' => function() {
                            $rateResponse = new ConversionRateResponse;
                            $rateResponse->MockedResult = 100;
                            return $rateResponse;
                        }
                    ]
                ]
            ]
        );

        $responseMessage = $interpreter->response($responseMessage, 'ConversionRate');
        $this->assertInstanceOf('\ConversionRateResponse', $responseMessage);
        $this->assertEquals(['MockedResult' => 100], (array)$responseMessage);
    }

    #[Test]
    public function responseWsdlDisableExceptions()
    {
        $interpreter = new Interpreter(null, ['uri'=>'www.uri.com', 'location'=>'www.location.com', 'exceptions' => false]);
        $responseMessage = <<<EOD
<SOAP-ENV:Envelope
  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
   <SOAP-ENV:Body>
       <SOAP-ENV:Fault>
           <faultcode>SOAP-ENV:Server</faultcode>
           <faultstring>Server Error</faultstring>
           <detail>
               <e:myfaultdetails xmlns:e="Some-URI">
                 <message>
                   My application didn't work
                 </message>
                 <errorcode>
                   1001
                 </errorcode>
               </e:myfaultdetails>
           </detail>
       </SOAP-ENV:Fault>
   </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOD;
        $result = $interpreter->response($responseMessage, 'AnyMethod');
        $this->assertInstanceOf('\SoapFault', $result);
    }

    #[Test]
    public function requestWsdlSoapV12()
    {
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'airport.wsdl', ['soap_version' => SOAP_1_2]);
        $request = $interpreter->request('GetAirportInformationByCountry', [['country' => 'United Kingdom']]);
        $this->assertEquals('http://www.webservicex.net/airport.asmx', $request->getEndpoint());
        $this->assertEquals('http://www.webserviceX.NET/GetAirportInformationByCountry', $request->getSoapAction());
        $this->assertEquals('2', $request->getSoapVersion());
        $this->assertNotEmpty($request->getSoapMessage());
        $this->assertMatchesRegularExpression('/http:\/\/www\.w3\.org\/2003\/05\/soap-envelope/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/GetAirportInformationByCountry/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/country/', $request->getSoapMessage());
    }

    #[Test]
    public function responseWsdlSoapV12()
    {
        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <GetAirportInformationByCountryResponse xmlns="http://www.webserviceX.NET">
            <GetAirportInformationByCountryResult>&lt;NewDataSet /&gt;</GetAirportInformationByCountryResult>
        </GetAirportInformationByCountryResponse>
    </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wsdl' . DIRECTORY_SEPARATOR . 'airport.wsdl', ['soap_version' => SOAP_1_2]);
        $responseMessage = $interpreter->response($responseMessage, 'GetAirportInformationByCountry');
        $this->assertEquals(['GetAirportInformationByCountryResult' => '<NewDataSet />'], (array)$responseMessage);
    }

    #[Test]
    public function requestWithoutWsdl()
    {
        $interpreter = new Interpreter(null, ['uri'=>'www.uri.com', 'location'=>'www.location.com']);
        $request = $interpreter->request('anything', [['one' => 'two', 'three' => 'four']]);
        $this->assertEquals('www.location.com', $request->getEndpoint());
        $this->assertEquals('www.uri.com#anything', $request->getSoapAction());
        $this->assertEquals('1', $request->getSoapVersion());
        $this->assertMatchesRegularExpression('/one/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/two/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/three/', $request->getSoapMessage());
        $this->assertMatchesRegularExpression('/four/', $request->getSoapMessage());
    }

    #[Test]
    public function responseWithoutWsdl()
    {
        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <GetAirportInformationByCountryResponse xmlns="http://www.webserviceX.NET">
            <GetAirportInformationByCountryResult>&lt;NewDataSet /&gt;</GetAirportInformationByCountryResult>
        </GetAirportInformationByCountryResponse>
    </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(null, ['uri'=>'www.uri.com', 'location'=>'www.location.com', 'soap_version' => SOAP_1_2]);
        $responseMessage = $interpreter->response($responseMessage, 'GetAirportInformationByCountry');
        $this->assertEquals('<NewDataSet />', $responseMessage);

        $responseMessage = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <m:Trans xmlns:m="http://www.w3schools.com/transaction/" soap:mustUnderstand="1">
      234
    </m:Trans>
  </soap:Header>
  <soap:Body>
    <ConversionRateResponse xmlns="http://www.webserviceX.NET/">
      <ConversionRateResult>-1</ConversionRateResult>
    </ConversionRateResponse>
  </soap:Body>
</soap:Envelope>
EOD;
        $interpreter = new Interpreter(null, ['uri'=>'www.uri.com', 'location'=>'www.location.com']);
        $outputHeaders = [];
        $responseMessage = $interpreter->response($responseMessage, 'ConversionRate', $outputHeaders);
        $this->assertEquals('-1', $responseMessage);
        $this->assertNotEmpty($outputHeaders);
    }

    #[Test]
    public function faultResponseNotAffectSubsequentRequests()
    {
        $interpreter = new Interpreter(null, ['uri'=>'www.uri.com', 'location'=>'www.location.com']);
        $responseMessage = <<<EOD
<SOAP-ENV:Envelope
  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
   <SOAP-ENV:Body>
       <SOAP-ENV:Fault>
           <faultcode>SOAP-ENV:Server</faultcode>
           <faultstring>Server Error</faultstring>
           <detail>
               <e:myfaultdetails xmlns:e="Some-URI">
                 <message>
                   My application didn't work
                 </message>
                 <errorcode>
                   1001
                 </errorcode>
               </e:myfaultdetails>
           </detail>
       </SOAP-ENV:Fault>
   </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOD;
        try {
            $interpreter->response($responseMessage, 'AnyMethod');
        } catch (Exception $e) {
        }
        $request = $interpreter->request('AnyMethod');
        $this->assertTrue($request instanceof SoapRequest);
    }
}

/** Test support only */
#[\AllowDynamicProperties]
class  ConversionRate
{
}

/** Test support only */
#[\AllowDynamicProperties]
class ConversionRateResponse
{
}
