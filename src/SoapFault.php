<?php

namespace AlexaCRM\CRMToolkit;

/**
 * Represents a SOAP fault.
 */
class SoapFault extends \Exception {

    public $faultcode;

    public $faultstring;

    public $faultactor;

    public $detail;

    public $faultname;

    public $headerfault;

    public function __construct($faultcode, $faultstring, $faultactor = null, $detail = null, $faultname = null, $headerfault = null) {
        $this->faultcode = $faultcode;
        $this->faultstring = $faultstring;
        $this->faultactor = $faultactor;
        $this->detail = $detail;
        $this->faultname = $faultname;
        $this->headerfault = $headerfault;

        // propagate values into the \Exception properties
        $this->message = $faultstring;
        $this->code = $faultcode;
    }

}
