<?php

/**
 * Methods for dealing with SOAP service.
 */
class SimpleSoapClient {

    const MODE_WSDL = 'wsdl';
    const MODE_NO_WSDL = 'no_wsdl';

    private $client;

    /**
     * SimpleSoapClient class constructor.
     *
     * @param $soapMode The SOAP mode, WSDL or non-WSDL.
     * @param $serverLocation The location of server.
     */
    public function __construct($soapMode, $serverLocation) {
        $this->initializeClient($soapMode, $serverLocation);
    }

    /**
     * Instantiates the SoapClient, depending on the specified mode.
     *
     * For WSDL, it just has to be instantiated with the location of the service, which actually has to be the
     * .wsdl location.
     *
     * For non-WSDL, the first parameter of the constructor has to be null; and the second, an array specifying
     * both location and URI (which can be the same, the important parameter is the location).
     */
    protected function initializeClient($soapMode, $serverLocation) {
        switch ($soapMode) {
            case self::MODE_WSDL:
                $this->client = new SoapClient($serverLocation);

                break;

            case self::MODE_NO_WSDL:
                $options = array(
                    'location' => $serverLocation,
                    'uri' => $serverLocation
                );

                $this->client = new SoapClient(NULL, $options);

                break;

            default:
                throw new Exception('Error: invalid SOAP mode provided.');
                break;
        }
    }

    /**
     * Inserts data remotely into the SOAP service.
     *
     * @param $data Data to insert remotely.
     * @return Response from remote service.
     */
    public function insertData($data) {
        $response = $this->client->insertData($data);

        return $response;
    }

    /**
     * Reads data from SOAP service.
     *
     * @return Data received from remote service.
     */
    public function readData() {
        return $this->client->readData();
    }
}
