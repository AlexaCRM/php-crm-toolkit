<?php

if (!class_exists("AlexaSDK_Contact")) :
    
    class AlexaSDK_Contact{
        public $authentication;
        
        function __construct($authentication){
            $this->authentication = $authentication;
        }
        
        public function Login($username, $password) {

            $header = $this->authentication->getHeader('Execute');

            $request = '
                <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                ' . $header . '                  
                     <s:Body>
                        <Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                            <request i:type="c:ExecuteFetchRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:c="http://schemas.microsoft.com/crm/2011/Contracts">
                                <b:Parameters xmlns:d="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                    <b:KeyValuePairOfstringanyType>
                                        <d:key>FetchXml</d:key>
                                        <d:value i:type="e:string" xmlns:e="http://www.w3.org/2001/XMLSchema">&lt;fetch version="1.0" output-format="xml-platform" mapping="logical" page="1" count="250"&gt;&lt;entity name="contact"&gt;&lt;all-attributes/&gt;&lt;filter type="and" &gt;&lt;condition attribute="new_alexa_username" operator="eq" value="' . $username . '" /&gt;&lt;condition attribute="new_alexa_password" operator="eq" value="' . $password . '" /&gt;&lt;/filter&gt;&lt;/entity&gt;&lt;/fetch&gt;</d:value>
                                    </b:KeyValuePairOfstringanyType>
                                </b:Parameters>
                                <b:RequestId i:nil="true"/>
                                <b:RequestName>ExecuteFetch</b:RequestName>
                            </request>
                        </Execute>
                    </s:Body>
                </s:Envelope>
            ';

            $response = $this->authentication->GetSOAPResponse($this->authentication->authentication->organizationUrl, $request);

            $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $response));

            $soap = simplexml_load_string($returnValue);

            if (!isset($soap->Body->Fault) && isset($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value)) {
                $results = simplexml_load_string($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value);

                $result = json_decode(json_encode($results),TRUE);

                if (isset($result["result"])){
                    return $result["result"];
                }else{
                    return false;
                }
            } else { 
                return false;
            }
        }
        
       
        public function setPassword($id, $password) {
            
            $header = $this->authentication->getHeader('Update');
            
                $request = '
                    <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                        ' . $header . '       
                         <s:Body>
                            <Update xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                                <entity xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                    <b:Attributes xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                        <b:KeyValuePairOfstringanyType>
                                            <c:key>new_alexa_password</c:key>
                                            <c:value i:type="d:string" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$password.'</c:value>
                                        </b:KeyValuePairOfstringanyType>
                                    </b:Attributes>
                                    <b:EntityState i:nil="true"/>
                                    <b:FormattedValues xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic"/>
                                    <b:Id>'.$id.'</b:Id>
                                    <b:LogicalName>contact</b:LogicalName>
                                    <b:RelatedEntities xmlns:c="http://schemas.datacontract.org/2004/07/System.Collections.Generic"/>
                                </entity>
                            </Update>
                        </s:Body>
                    </s:Envelope>
                ';
                
                $response = $this->authentication->GetSOAPResponse($this->authentication->authentication->organizationUrl, $request);
                
                $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $response));

                $soap = simplexml_load_string($returnValue);
                
                if (isset($soap->Body->Fault)) {
                    return false;
                }else{
                    return true;
                }
                
        }
        
        
        public function SearchContact($username, $email) {

            $header = $this->authentication->getHeader('Execute');

            $request = '
                <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                ' . $header . '                  
                     <s:Body>
                        <Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                            <request i:type="c:ExecuteFetchRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:c="http://schemas.microsoft.com/crm/2011/Contracts">
                                <b:Parameters xmlns:d="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                    <b:KeyValuePairOfstringanyType>
                                        <d:key>FetchXml</d:key>
                                        <d:value i:type="e:string" xmlns:e="http://www.w3.org/2001/XMLSchema">&lt;fetch version="1.0" output-format="xml-platform" mapping="logical" page="1" count="250"&gt;&lt;entity name="contact"&gt;&lt;all-attributes/&gt;&lt;filter type="and" &gt;&lt;condition attribute="new_alexa_username" operator="eq" value="' . $username . '" /&gt;&lt;condition attribute="emailaddress1" operator="eq" value="' . $email . '" /&gt;&lt;/filter&gt;&lt;/entity&gt;&lt;/fetch&gt;</d:value>
                                    </b:KeyValuePairOfstringanyType>
                                </b:Parameters>
                                <b:RequestId i:nil="true"/>
                                <b:RequestName>ExecuteFetch</b:RequestName>
                            </request>
                        </Execute>
                    </s:Body>
                </s:Envelope>
            ';

            $response = $this->authentication->GetSOAPResponse($this->authentication->authentication->organizationUrl, $request);
            
            $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $response));

            $soap = simplexml_load_string($returnValue);

            if (isset($soap->Body->Fault)) {
                echo "Invalid request";
                return false;
            } else {
                if (isset($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value)) {
                    $results = simplexml_load_string($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value);

                    if (isset($results->result->contactid) && isset($results->result->new_alexa_password)) {
                        $res["user_id"] = (string)$results->result->contactid;
                        $res["password"] = (string)$results->result->new_alexa_password;
                        $res["email"] = (string)$results->result->emailaddress1;
                        return $res;
                    } 
                }                 
                return false;
            }
        }
        
        
        public function getContact($user_id, $full = false) {

            $header = $this->authentication->getHeader('Execute');

            $request = '
                <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                ' . $header . '                  
                     <s:Body>
                        <Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                            <request i:type="c:ExecuteFetchRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:c="http://schemas.microsoft.com/crm/2011/Contracts">
                                <b:Parameters xmlns:d="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                    <b:KeyValuePairOfstringanyType>
                                        <d:key>FetchXml</d:key>
                                        <d:value i:type="e:string" xmlns:e="http://www.w3.org/2001/XMLSchema">&lt;fetch version="1.0" output-format="xml-platform" mapping="logical" page="1" count="250"&gt;&lt;entity name="contact"&gt;&lt;all-attributes/&gt;&lt;filter type="and" &gt;&lt;condition attribute="contactid" operator="eq" value="' . $user_id . '" /&gt;&lt;/filter&gt;&lt;/entity&gt;&lt;/fetch&gt;</d:value>
                                    </b:KeyValuePairOfstringanyType>
                                </b:Parameters>
                                <b:RequestId i:nil="true"/>
                                <b:RequestName>ExecuteFetch</b:RequestName>
                            </request>
                        </Execute>
                    </s:Body>
                </s:Envelope>
            ';

            $response = $this->authentication->GetSOAPResponse($this->authentication->settings->organizationUrl, $request);
            
            $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $response));

            $soap = simplexml_load_string($returnValue);

            if (isset($soap->Body->Fault)) {
                return false;
            } else {
                if (isset($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value)) {
                    $results = simplexml_load_string($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value);

                    if ($full){
                        return $results;
                    }
                    
                    if (isset($results->result->contactid) && isset($results->result->new_alexa_password)) {
                        $res["user_id"] = (string)$results->result->contactid;
                        $res["password"] = (string)$results->result->new_alexa_password;
                        $res["name"] = (string)$results->result->new_alexa_username;
                        $res["email"] = (string)$results->result->emailaddress1;
                        return $res;
                    } 
                }                 
                return false;
            }
        }
        
        
        public function getContactByName($contact_name, $full = false) {

            $header = $this->authentication->getHeader('Execute');

            $request = '
                <s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:a="http://www.w3.org/2005/08/addressing" xmlns:u="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                ' . $header . '                  
                     <s:Body>
                        <Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services">
                            <request i:type="c:ExecuteFetchRequest" xmlns:b="http://schemas.microsoft.com/xrm/2011/Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:c="http://schemas.microsoft.com/crm/2011/Contracts">
                                <b:Parameters xmlns:d="http://schemas.datacontract.org/2004/07/System.Collections.Generic">
                                    <b:KeyValuePairOfstringanyType>
                                        <d:key>FetchXml</d:key>
                                        <d:value i:type="e:string" xmlns:e="http://www.w3.org/2001/XMLSchema">&lt;fetch version="1.0" output-format="xml-platform" mapping="logical" page="1" count="250"&gt;&lt;entity name="contact"&gt;&lt;all-attributes/&gt;&lt;filter type="and" &gt;&lt;condition attribute="firstname" operator="eq" value="' . $contact_name . '" /&gt;&lt;/filter&gt;&lt;/entity&gt;&lt;/fetch&gt;</d:value>
                                    </b:KeyValuePairOfstringanyType>
                                </b:Parameters>
                                <b:RequestId i:nil="true"/>
                                <b:RequestName>ExecuteFetch</b:RequestName>
                            </request>
                        </Execute>
                    </s:Body>
                </s:Envelope>
            ';

            $response = $this->authentication->GetSOAPResponse($this->authentication->authentication->organizationUrl, $request);
            
            $returnValue = preg_replace('/(<)([a-z]:)/', '<', preg_replace('/(<\/)([a-z]:)/', '</', $response));

            $soap = simplexml_load_string($returnValue);

            if (isset($soap->Body->Fault)) {
                return false;
            } else {
                if (isset($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value)) {
                    $results = simplexml_load_string($soap->Body->ExecuteResponse->ExecuteResult->Results->KeyValuePairOfstringanyType->value);

                    if ($full){
                        return $results;
                    }
                    
                    if (isset($results->result->contactid)) {
                        $res["user_id"] = (string)$results->result->contactid;
                        $res["password"] = (string)$results->result->new_alexa_password;
                        $res["name"] = (string)$results->result->new_alexa_username;
                        $res["email"] = (string)$results->result->emailaddress1;
                        return $res;
                    } 
                }                 
                return false;
            }
        }
        
        
        
    }
    
    
endif;