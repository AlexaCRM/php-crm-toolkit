<?php

if (!class_exists("AlexaSDK_Common")) :
    
    class AlexaSDK_Common{
        public $authentication;
        
        function __construct($authentication){
            $this->authentication = $authentication;
        }
        
        public function WhoAmI() {

            $header = $this->authentication->getHeader('Execute');
            
            $request = '
                <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                  ' . $header . '
                     <s:Body>
                          <Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                                    <request i:type="c:WhoAmIRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:c="http://schemas.microsoft.com/crm/2011/Contracts">
                                        <b:Parameters xmlns:d="http://schemas.datacontract.org/2004/07/System.Collections.Generic"/>
                                        <b:RequestId i:nil="true"/>
                                        <b:RequestName>WhoAmI</b:RequestName>
                                    </request>
                                </Execute>
                        </s:Body>
                </s:Envelope>
            ';
            
            $response = $this->authentication->GetSOAPResponse($this->authentication->authentication->organizationUrl, $request);
            
            $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $response));

            $soap = new DomDocument();
            $soap->loadXML($returnValue);

            try{
                $result[$soap->getElementsbyTagName("key")->item(0)->textContent] = $soap->getElementsbyTagName("value")->item(0)->textContent;
                $result[$soap->getElementsbyTagName("key")->item(1)->textContent] = $soap->getElementsbyTagName("value")->item(1)->textContent;
                $result[$soap->getElementsbyTagName("key")->item(2)->textContent] = $soap->getElementsbyTagName("value")->item(2)->textContent;
                
                return $result;
                
            }catch(Exception $e){
                
            }
            return false;
        }
        
    }
    
endif;

