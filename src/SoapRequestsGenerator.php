<?php
/**
 * Copyright (c) 2016 AlexaCRM.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Lesser Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace AlexaCRM\CRMToolkit;

use DOMDocument;
use DOMNode;
use DOMText;

/**
 * AlexaCRM\CRMToolkit\AlexaSDK_SoapRequestsGenerator.class.php
 * This file defines the AlexaCRM\CRMToolkit\AlexaSDK_SoapRequestsGenerator class that used to generate the body of SOAP requests messages
 *
 * @author alexacrm.com
 * @version 1.0
 * @package AlexaCRM\CRMToolkit\AlexaSDK
 */
class SoapRequestsGenerator {

    /**
     * Generate a Create Request
     *
     * @param Entity $entity
     *
     * @return DOMNode
     */
    public static function generateCreateRequest( Entity $entity ) {
        /* Generate the CreateRequest message */
        $createRequestDOM = new DOMDocument();
        $createNode       = $createRequestDOM->appendChild( $createRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Create' ) );
        $createNode->appendChild( $createRequestDOM->importNode( $entity->getEntityDOM(), true ) );

        /* Return the DOMNode */

        return $createNode;
    }

    /**
     * Generate an Update Request
     *
     * @ignore
     */
    public static function generateUpdateRequest( Entity $entity ) {
        /* Generate the UpdateRequest message */
        $updateRequestDOM = new DOMDocument();
        $updateNode       = $updateRequestDOM->appendChild( $updateRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Update' ) );
        $updateNode->appendChild( $updateRequestDOM->importNode( $entity->getEntityDOM(), true ) );

        /* Return the DOMNode */

        return $updateNode;
    }

    /**
     * Generate an Update Request
     *
     * @ignore
     */
    public static function generateUpsertRequest( Entity $entity ) {
        /* Generate the ExecuteAction message */
        $executeActionRequestDOM = new DOMDocument();

        $executeActionNode = $executeActionRequestDOM->appendChild( $executeActionRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute' ) );
        $executeActionNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance' );

        $requestNode = $executeActionNode->appendChild( $executeActionRequestDOM->createElement( 'request' ) );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        /* Set request type */
        $requestNode->setAttribute( 'i:type', 'b:UpsertRequest' );

        $parametersNode = $requestNode->appendChild( $executeActionRequestDOM->createElement( 'b:Parameters' ) );
        $parametersNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

        /* Create a Key/Value Pair of String/Any Type */
        $propertyNode = $parametersNode->appendChild( $executeActionRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        /* Set the Property Name */
        $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:key', "Target" ) );

        /* Now create the XML Node for the Value */
        $mainValueNode = $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:value' ) );
        /* Set the Type of the Value */
        $mainValueNode->setAttribute( 'i:type', 'b:Entity' );

        $attributeNode = $mainValueNode->appendChild( $executeActionRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts', 'b:Attributes' ) );
        $attributeNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

        foreach ( $entity->properties as $property => $propertyDetails ) {
            /* Only include changed properties */
            if ( $entity->propertyValues[ $property ]['Changed'] ) {
                /* Create a Key/Value Pair of String/Any Type */
                $propertyNode = $attributeNode->appendChild( $executeActionRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
                /* Set the Property Name */
                $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:key', $property ) );
                /* Check the Type of the Value */
                if ( $propertyDetails->isLookup ) {
                    /* Special handling for Lookups - use an AlexaCRM\CRMToolkit\Entity\EntityReference, not the AttributeType */
                    $valueNode = $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:value' ) );

                    if ( $entity->propertyValues[ $property ]['Value'] != null ) {
                        $valueNode->setAttribute( 'i:type', 'b:EntityReference' );
                        $valueNode->appendChild( $executeActionRequestDOM->createElement( 'b:Id', ( $entity->propertyValues[ $property ]['Value'] ) ? $entity->propertyValues[ $property ]['Value']->ID : "" ) );
                        $valueNode->appendChild( $executeActionRequestDOM->createElement( 'b:LogicalName', ( $entity->propertyValues[ $property ]['Value'] ) ? $entity->propertyValues[ $property ]['Value']->logicalname : "" ) );
                        $valueNode->appendChild( $executeActionRequestDOM->createElement( 'b:Name' ) )->setAttribute( 'i:nil', 'true' );
                    } else {
                        $valueNode->setAttribute( 'i:nil', 'true' );
                    }
                } else if ( strtolower( $propertyDetails->type ) == "money" ) {

                    $valueNode = $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:value' ) );

                    if ( $entity->propertyValues[ $property ]['Value'] ) {
                        $valueNode->setAttribute( 'i:type', 'b:Money' );
                        $valueNode->appendChild( $executeActionRequestDOM->createElement( 'b:Value', $entity->propertyValues[ $property ]['Value'] ) );
                    } else {
                        $valueNode->setAttribute( 'i:nil', 'true' );
                    }
                } else if ( strtolower( $propertyDetails->type ) == "datetime" ) {

                    $valueNode = $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:value' ) );

                    if ( $entity->propertyValues[ $property ]['Value'] ) {
                        $valueNode->setAttribute( 'i:type', 'd:dateTime' );
                        $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema' );
                        $valueNode->appendChild( new DOMText( gmdate( "Y-m-d\TH:i:s\Z", $entity->propertyValues[ $property ]['Value'] ) ) );
                    } else {
                        $valueNode->setAttribute( 'i:nil', 'true' );
                    }
                } else if ( strtolower( $propertyDetails->type ) == "picklist" ) {
                    $valueNode = $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:value' ) );

                    if ( $entity->propertyValues[ $property ]['Value'] ) {
                        $valueNode->setAttribute( 'i:type', 'd:OptionSetValue' );
                        $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
                        $valueNode->appendChild( $executeActionRequestDOM->createElement( 'b:Value', $entity->propertyValues[ $property ]['Value'] ) );
                    } else {
                        $valueNode->setAttribute( 'i:nil', 'true' );
                    }
                } else {
                    /* Determine the Type, Value and XML Namespace for this field */
                    $xmlValue      = $entity->propertyValues[ $property ]['Value'];
                    $xmlValueChild = null;
                    $xmlType       = strtolower( $propertyDetails->type );
                    $xmlTypeNS     = 'http://www.w3.org/2001/XMLSchema';
                    /* Special Handing for certain types of field */
                    switch ( strtolower( $propertyDetails->type ) ) {
                        case 'memo':
                            /* Memo - This gets treated as a normal String */
                            $xmlType = 'string';
                            break;
                        case 'integer':
                            /* Integer - This gets treated as an "int" */
                            $xmlType = 'int';
                            break;
                        case 'uniqueidentifier':
                            /* Uniqueidentifier - This gets treated as a guid */
                            $xmlType = 'guid';
                            break;
                        case 'state':
                        case 'status':
                            /* OptionSetValue - Just get the numerical value, but as an XML structure */
                            $xmlType       = 'OptionSetValue';
                            $xmlTypeNS     = 'http://schemas.microsoft.com/xrm/2011/Contracts';
                            $xmlValue      = null;
                            $xmlValueChild = $executeActionRequestDOM->createElement( 'b:Value', $entity->propertyValues[ $property ]['Value'] );
                            break;
                        case 'boolean':
                            /* Boolean - Just get the numerical value */
                            $xmlValue = $entity->propertyValues[ $property ]['Value'];
                            break;
                        case 'string':
                        case 'int':
                        case 'decimal':
                        case 'double':
                        case 'guid':
                            /* No special handling for these types */
                            break;
                        default:
                            /* If we're using Default, Warn user that the XML handling is not defined */
                            trigger_error( 'No Create/Update handling implemented for type ' . $propertyDetails->type . ' used by field ' . $property, E_USER_WARNING );
                    }
                    /* Now create the XML Node for the Value */
                    $valueNode = $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:value' ) );
                    /* Set the Type of the Value */
                    $valueNode->setAttribute( 'i:type', 'd:' . $xmlType );
                    $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', $xmlTypeNS );
                    /* If there is a child node needed, append it */
                    if ( $xmlValueChild != null ) {
                        $valueNode->appendChild( $xmlValueChild );
                    }
                    /* If there is a value, set it */
                    if ( $xmlValue != null ) {
                        $valueNode->appendChild( new DOMText( $xmlValue ) );
                    }
                }
            }
        }
        /* Entity State */
        $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:EntityState' ) )->setAttribute( 'i:nil', 'true' );
        /* Formatted Values */
        $formattedValuesNode = $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:FormattedValues' ) );
        $formattedValuesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );
        /* Entity ID */
        $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:Id', $entity->id ) );
        /* Logical Name */
        $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:LogicalName', $entity->logicalName ) );
        /* Related Entities */
        $relatedEntitiesNode = $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:RelatedEntities' ) );
        $relatedEntitiesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

        if ( $entity->keyAttribute && $entity->keyAttribute->keys ) {
            $keyAttributesNode = $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:KeyAttributes' ) );
            $keyAttributesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/7.1/Contracts' );

            foreach ( $entity->keyAttribute->keys as $keyAttribute ) {
                $keyAttributesNode = $mainValueNode->appendChild( $executeActionRequestDOM->createElement( 'b:KeyAttributes' ) );
                $keyAttributesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/7.1/Contracts' );

                $keyValuePairOfstringanyTypeNode = $keyAttributesNode->appendChild( $executeActionRequestDOM->createElement( 'd:KeyValuePairOfstringanyType' ) );

                $keyValuePairOfstringanyTypeNode->appendChild( $executeActionRequestDOM->createElement( 'c:key', $keyAttribute->key ) );

                $alternativeKeyValueNode = $keyValuePairOfstringanyTypeNode->appendChild( $executeActionRequestDOM->createElement( 'c:value', $keyAttribute->value ) );
                $alternativeKeyValueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:e', 'http://www.w3.org/2001/XMLSchema' );
                $alternativeKeyValueNode->setAttribute( 'i:type', 'e:string' );
            }
        }

        $requiestIdNode = $requestNode->appendChild( $executeActionRequestDOM->createElement( 'b:RequestId' ) );
        $requiestIdNode->setAttribute( 'i:nil', 'true' );
        $requestNode->appendChild( $executeActionRequestDOM->createElement( 'b:RequestName', "Upsert" ) );

        return $executeActionNode;
    }

    /**
     * Generate a Delete Request
     *
     * @param Entity $entity the Entity to delete
     *
     * @ignore
     */
    public static function generateDeleteRequest( Entity $entity ) {
        /* Generate the DeleteRequest message */
        $deleteRequestDOM = new DOMDocument();
        $deleteNode       = $deleteRequestDOM->appendChild( $deleteRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Delete' ) );
        $deleteNode->appendChild( $deleteRequestDOM->createElement( 'entityName', $entity->logicalName ) );
        $deleteNode->appendChild( $deleteRequestDOM->createElement( 'id', $entity->ID ) );

        /* Return the DOMNode */

        return $deleteNode;
    }

    /**
     * @todo Unfinished method
     * @return type
     */
    public static function generateRetrieveMetadataChangesRequest() {
        /* Generate the ExecuteAction message */
        $retrieveMetadataChangesRequestDom = new DOMDocument();

        $retrieveMetadataNode = $retrieveMetadataChangesRequestDom->appendChild( $retrieveMetadataChangesRequestDom->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute' ) );
        $retrieveMetadataNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance' );

        $requestNode = $retrieveMetadataNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'request' ) );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );

        $parametersNode = $requestNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'b:Parameters' ) );
        $parametersNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

        $propertyNode = $parametersNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'b:KeyValuePairOfstringanyType' ) );
        /* Set the Property Name */
        $propertyNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'c:key', "Query" ) );
        /* Now create the XML Node for the Value */
        $valueNode = $propertyNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'c:value' ) );
        /* Set the Type of the Value */
        $valueNode->setAttribute( 'i:type', 'd:EntityQueryExpression' );
        $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Metadata/Query' );
        $valueNode->appendChild( new DOMText( "" ) );

        /* $propertyNode = $parametersNode->appendChild($retrieveMetadataChangesRequestDom->createElement('b:KeyValuePairOfstringanyType'));
          $propertyNode->appendChild($retrieveMetadataChangesRequestDom->createElement('c:key', "ClientVersionStamp"));
          $valueNode = $propertyNode->appendChild($retrieveMetadataChangesRequestDom->createElement('c:value'));
          $valueNode->setAttribute('i:type', 'd:string');
          $valueNode->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema');
          $valueNode->appendChild(new DOMText("395720!05/11/2015 17:04:05"));
         */

        $requiestIdNode = $requestNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'b:RequestId' ) );
        $requiestIdNode->setAttribute( 'i:nil', 'true' );
        $requestNode->appendChild( $retrieveMetadataChangesRequestDom->createElement( 'b:RequestName', "RetrieveMetadataChanges" ) );

        return $retrieveMetadataNode;
    }

    /**
     * Generate a Retrieve Request
     *
     * @ignore
     */
    public static function generateRetrieveRequest( $entityType, $entityId, $columnSet ) {
        /* Generate the RetrieveRequest message */
        $retrieveRequestDOM = new DOMDocument();
        $retrieveNode       = $retrieveRequestDOM->appendChild( $retrieveRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Retrieve' ) );
        $retrieveNode->appendChild( $retrieveRequestDOM->createElement( 'entityName', $entityType ) );
        $retrieveNode->appendChild( $retrieveRequestDOM->createElement( 'id', $entityId ) );
        $columnSetNode = $retrieveNode->appendChild( $retrieveRequestDOM->createElement( 'columnSet' ) );
        $columnSetNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        $columnSetNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance' );
        /* Add the columns requested, if specified */
        if ( $columnSet != null && count( $columnSet ) > 0 ) {
            $columnSetNode->appendChild( $retrieveRequestDOM->createElement( 'b:AllColumns', 'false' ) );
            $columnsNode = $columnSetNode->appendChild( $retrieveRequestDOM->createElement( 'b:Columns' ) );
            $columnsNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.microsoft.com/2003/10/Serialization/Arrays' );
            foreach ( $columnSet as $columnName ) {
                $columnsNode->appendChild( $retrieveRequestDOM->createElement( 'c:string', strtolower( $columnName ) ) );
            }
        } else {
            /* No columns specified, request all of them */
            $columnSetNode->appendChild( $retrieveRequestDOM->createElement( 'b:AllColumns', 'true' ) );
        }

        /* Return the DOMNode */

        return $retrieveNode;
    }

    public static function generateExecuteRetrieveRequest( $entityType, KeyAttributes $keyAttributes, $columnSet ) {
        $retrieveRequestDOM = new DOMDocument();

        $executeNode = $retrieveRequestDOM->appendChild( $retrieveRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute' ) );
        $requestNode = $executeNode->appendChild( $retrieveRequestDOM->createElement( 'request' ) );
        /* Set request type */
        $requestNode->setAttribute( 'i:type', 'b:RetrieveRequest' );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance' );

        $parametersNode = $requestNode->appendChild( $retrieveRequestDOM->createElement( 'b:Parameters' ) );
        $parametersNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

        /* Create a Key/Value Pair of String/Any Type */
        $propertyNode = $parametersNode->appendChild( $retrieveRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        /* Set the Property Name */
        $propertyNode->appendChild( $retrieveRequestDOM->createElement( 'c:key', "Target" ) );
        /* Determine the Type, Value and XML Namespace for this field */

        $valueNode = $propertyNode->appendChild( $retrieveRequestDOM->createElement( 'c:value' ) );
        /* Set the Type of the Value */
        $valueNode->setAttribute( 'i:type', 'b:EntityReference' );

        $valueNode->appendChild( $retrieveRequestDOM->createElement( 'b:Id', AbstractClient::EmptyGUID ) );
        $keyAttributesNode = $valueNode->appendChild( $retrieveRequestDOM->createElement( 'b:KeyAttributes' ) );

        $keyAttributesNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/7.1/Contracts' );

        foreach ( $keyAttributes->getKeys() as $keyAttributeKey => $keyAttributeValue ) {
            /* Create a Key/Value Pair of String/Any Type */
            $keyValuePairNode = $keyAttributesNode->appendChild( $retrieveRequestDOM->createElement( 'd:KeyValuePairOfstringanyType' ) );
            /* Set the Property Name */
            $keyValuePairNode->appendChild( $retrieveRequestDOM->createElement( 'c:key', $keyAttributeKey ) );
            /* Now create the XML Node for the Value */
            $keyAttributeValueNode = $keyValuePairNode->appendChild( $retrieveRequestDOM->createElement( 'c:value', $keyAttributeValue ) );
            $keyAttributeValueNode->setAttribute( 'i:type', 'e:string' );
            $keyAttributeValueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:e', 'http://www.w3.org/2001/XMLSchema' );
        }
        /* Create a LogicalName with entity type */
        $valueNode->appendChild( $retrieveRequestDOM->createElement( 'b:LogicalName', $entityType ) );
        $valueNode->appendChild( $retrieveRequestDOM->createElement( 'b:Name' ) )->setAttribute( 'i:nil', 'true' );
        $valueNode->appendChild( $retrieveRequestDOM->createElement( 'b:RowVersion' ) )->setAttribute( 'i:nil', 'true' );
        /* Create a Key/Value Pair of String/Any Type */
        $propertyNode1 = $parametersNode->appendChild( $retrieveRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        /* Set the Property Name */
        $propertyNode1->appendChild( $retrieveRequestDOM->createElement( 'c:key', "ColumnSet" ) );
        /* Now create the XML Node for the Value */
        $valueNode1 = $propertyNode1->appendChild( $retrieveRequestDOM->createElement( 'c:value' ) );
        $valueNode1->setAttribute( 'i:type', 'b:ColumnSet' );
        /* Add the columns requested, if specified */
        if ( $columnSet != null && count( $columnSet ) > 0 ) {
            $valueNode1->appendChild( $retrieveRequestDOM->createElement( 'b:AllColumns', 'false' ) );
            $columnsNode = $valueNode1->appendChild( $retrieveRequestDOM->createElement( 'b:Columns' ) );
            $columnsNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/2003/10/Serialization/Arrays' );
            foreach ( $columnSet as $columnName ) {
                $columnsNode->appendChild( $retrieveRequestDOM->createElement( 'd:string', strtolower( $columnName ) ) );
            }
        } else {
            /* No columns specified, request all of them */
            $valueNode1->appendChild( $retrieveRequestDOM->createElement( 'b:AllColumns', 'true' ) );
        }

        $requiestIdNode = $requestNode->appendChild( $retrieveRequestDOM->createElement( 'b:RequestId' ) );
        $requiestIdNode->setAttribute( 'i:nil', 'true' );
        $requestNode->appendChild( $retrieveRequestDOM->createElement( 'b:RequestName', "Retrieve" ) );

        /* Return the DOMNode */

        return $executeNode;
    }

    /**
     * Utility function to generate the XML for a Retrieve Organization request
     * This XML can be sent as a SOAP message to the Discovery Service to determine all Organizations
     * available on that service.
     *
     * @return DOMNode containing the XML for a RetrieveOrganizationRequest message
     * @ignore
     */
    public static function generateRetrieveOrganizationRequest() {
        $retrieveOrganizationRequestDOM = new DOMDocument();
        $executeNode                    = $retrieveOrganizationRequestDOM->appendChild( $retrieveOrganizationRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Discovery', 'Execute' ) );
        $requestNode                    = $executeNode->appendChild( $retrieveOrganizationRequestDOM->createElement( 'request' ) );
        $requestNode->setAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'RetrieveOrganizationsRequest' );
        $requestNode->appendChild( $retrieveOrganizationRequestDOM->createElement( 'AccessType', 'Default' ) );
        $requestNode->appendChild( $retrieveOrganizationRequestDOM->createElement( 'Release', 'Current' ) );

        return $executeNode;
    }

    /**
     * Generate a Retrieve Multiple Request
     *
     * @ignore
     */
    public static function generateRetrieveMultipleRequest( $queryXML, $pagingCookie = null, $limitCount = null, $pageNumber = null ) {
        /* Turn the queryXML into a DOMDocument so we can manipulate it */
        $queryDOM = new DOMDocument();
        $queryDOM->loadXML( $queryXML );

        $newPage = 1;
        if ( $queryDOM->documentElement->hasAttribute( 'page' ) ) {
            $newPage = (int)$queryDOM->documentElement->getAttribute( 'page' );
        }

        if ( $pagingCookie !== null ) {
            $newPage = Client::getPageNo( $pagingCookie ) + 1;
            $queryDOM->documentElement->setAttribute( 'paging-cookie', $pagingCookie );
        } elseif ( $pageNumber !== null ) {
            $newPage = $pageNumber;
        }

        /* Modify the query that we send: Add the Page number */
        $queryDOM->documentElement->setAttribute( 'page', $newPage );

        /* Find the current limit, if there is one */
        $currentLimit = Client::getMaximumRecords() + 1;
        if ( $queryDOM->documentElement->hasAttribute( 'count' ) ) {
            $currentLimit = $queryDOM->documentElement->getAttribute( 'count' );
        }

        /* Determine the preferred limit (passed by argument, or 5000 if not set) */
        $preferredLimit = ( $limitCount == null ) ? $currentLimit : $limitCount;
        if ( $preferredLimit > Client::getMaximumRecords() ) {
            $preferredLimit = Client::getMaximumRecords();
        }

        /* If the current limit is not set, or is greater than the preferred limit, override it */
        if ( $currentLimit > $preferredLimit ) {
            /* Modify the query that we send: Change the Count */
            $queryDOM->documentElement->setAttribute( 'count', $preferredLimit );
            /* Update the Query XML with the new structure */
        }

        $queryXML = $queryDOM->saveXML( $queryDOM->documentElement );

        /* Generate the RetrieveMultipleRequest message */
        $retrieveMultipleRequestDOM = new DOMDocument();
        $retrieveMultipleNode       = $retrieveMultipleRequestDOM->appendChild( $retrieveMultipleRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'RetrieveMultiple' ) );
        $queryNode                  = $retrieveMultipleNode->appendChild( $retrieveMultipleRequestDOM->createElement( 'query' ) );
        $queryNode->setAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'b:FetchExpression' );
        $queryNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        $queryNode->appendChild( $retrieveMultipleRequestDOM->createElement( 'b:Query', trim( htmlentities( $queryXML ) ) ) );

        /* Return the DOMNode */

        return $retrieveMultipleNode;
    }

    /**
     * Generate a Retrieve Entity Request
     *
     * @ignore
     */
    public static function generateRetrieveEntityRequest( $entityType, $entityId = null, $entityFilters = null, $showUnpublished = false ) {
        /* We can use either the entityType (Logical Name), or the entityId, but not both. */
        /* Use ID by preference, if not set, default to 0s */
        if ( $entityId != null ) {
            $entityType = null;
        } else {
            $entityId = Client::EmptyGUID;
        }

        /* If no entityFilters are supplied, assume "All" */
        if ( $entityFilters == null ) {
            $entityFilters = 'Entity Attributes Privileges Relationships';
        }

        /* Generate the RetrieveEntityRequest message */
        $retrieveEntityRequestDOM = new DOMDocument();
        $executeNode              = $retrieveEntityRequestDOM->appendChild( $retrieveEntityRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute' ) );
        $requestNode              = $executeNode->appendChild( $retrieveEntityRequestDOM->createElement( 'request' ) );
        $requestNode->setAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'b:RetrieveEntityRequest' );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        $parametersNode = $requestNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:Parameters' ) );
        $parametersNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );
        /* EntityFilters */
        $keyValuePairNode1 = $parametersNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        $keyValuePairNode1->appendChild( $retrieveEntityRequestDOM->createElement( 'c:key', 'EntityFilters' ) );
        $valueNode1 = $keyValuePairNode1->appendChild( $retrieveEntityRequestDOM->createElement( 'c:value', $entityFilters ) );
        $valueNode1->setAttribute( 'i:type', 'd:EntityFilters' );
        $valueNode1->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Metadata' );
        /* MetadataId */
        $keyValuePairNode2 = $parametersNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        $keyValuePairNode2->appendChild( $retrieveEntityRequestDOM->createElement( 'c:key', 'MetadataId' ) );
        $valueNode2 = $keyValuePairNode2->appendChild( $retrieveEntityRequestDOM->createElement( 'c:value', $entityId ) );
        $valueNode2->setAttribute( 'i:type', 'd:guid' );
        $valueNode2->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/2003/10/Serialization/' );
        /* RetrieveAsIfPublished */
        $keyValuePairNode3 = $parametersNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        $keyValuePairNode3->appendChild( $retrieveEntityRequestDOM->createElement( 'c:key', 'RetrieveAsIfPublished' ) );
        $valueNode3 = $keyValuePairNode3->appendChild( $retrieveEntityRequestDOM->createElement( 'c:value', ( $showUnpublished ? 'true' : 'false' ) ) );
        $valueNode3->setAttribute( 'i:type', 'd:boolean' );
        $valueNode3->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema' );
        /* LogicalName */
        $keyValuePairNode4 = $parametersNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        $keyValuePairNode4->appendChild( $retrieveEntityRequestDOM->createElement( 'c:key', 'LogicalName' ) );
        $valueNode4 = $keyValuePairNode4->appendChild( $retrieveEntityRequestDOM->createElement( 'c:value', $entityType ) );
        $valueNode4->setAttribute( 'i:type', 'd:string' );
        $valueNode4->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema' );
        /* Request ID and Name */
        $requestNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:RequestId' ) )->setAttribute( 'i:nil', 'true' );
        $requestNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:RequestName', 'RetrieveEntity' ) );

        /* Return the DOMNode */

        return $executeNode;
    }

    /**
     * Generate a Retrieve Entity Request
     *
     * @ignore
     */
    public static function generateRetrieveAllEntitiesRequest( $entityFilters = null, $showUnpublished = false ) {
        /* If no entityFilters are supplied, assume "All" */
        if ( $entityFilters == null ) {
            $entityFilters = 'Entity Attributes Privileges Relationships';
        }
        /* Generate the RetrieveAllEntitiesRequest message */
        $retrieveEntityRequestDOM = new DOMDocument();
        $executeNode              = $retrieveEntityRequestDOM->appendChild( $retrieveEntityRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute' ) );
        $requestNode              = $executeNode->appendChild( $retrieveEntityRequestDOM->createElement( 'request' ) );
        $requestNode->setAttributeNS( 'http://www.w3.org/2001/XMLSchema-instance', 'i:type', 'b:RetrieveAllEntitiesRequest' );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        $parametersNode = $requestNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:Parameters' ) );
        $parametersNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );
        /* EntityFilters */
        $keyValuePairNode1 = $parametersNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        $keyValuePairNode1->appendChild( $retrieveEntityRequestDOM->createElement( 'c:key', 'EntityFilters' ) );
        $valueNode1 = $keyValuePairNode1->appendChild( $retrieveEntityRequestDOM->createElement( 'c:value', $entityFilters ) );
        $valueNode1->setAttribute( 'i:type', 'd:EntityFilters' );
        $valueNode1->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://schemas.microsoft.com/xrm/2011/Metadata' );
        /* RetrieveAsIfPublished */
        $keyValuePairNode3 = $parametersNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
        $keyValuePairNode3->appendChild( $retrieveEntityRequestDOM->createElement( 'c:key', 'RetrieveAsIfPublished' ) );
        $valueNode3 = $keyValuePairNode3->appendChild( $retrieveEntityRequestDOM->createElement( 'c:value', ( $showUnpublished ? 'true' : 'false' ) ) );
        $valueNode3->setAttribute( 'i:type', 'd:boolean' );
        $valueNode3->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', 'http://www.w3.org/2001/XMLSchema' );
        /* Request ID and Name */
        $requestNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:RequestId' ) )->setAttribute( 'i:nil', 'true' );
        $requestNode->appendChild( $retrieveEntityRequestDOM->createElement( 'b:RequestName', 'RetrieveAllEntities' ) );

        /* Return the DOMNode */

        return $executeNode;
    }

    /**
     * Generate a ExecuteAction Request
     *
     * @param string $requestName name of Action to request
     * @param Array (optional)
     *
     * @ignore
     */
    public static function generateExecuteActionRequest( $requestName, $parameters = null, $requestType = null ) {
        /* Generate the ExecuteAction message */
        $executeActionRequestDOM = new DOMDocument();

        $executeActionNode = $executeActionRequestDOM->appendChild( $executeActionRequestDOM->createElementNS( 'http://schemas.microsoft.com/xrm/2011/Contracts/Services', 'Execute' ) );
        $executeActionNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:i', 'http://www.w3.org/2001/XMLSchema-instance' );

        $requestNode = $executeActionNode->appendChild( $executeActionRequestDOM->createElement( 'request' ) );
        $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:b', 'http://schemas.microsoft.com/xrm/2011/Contracts' );
        /* Set request type */
        if ( $requestType ) {
            $requestNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:e', 'http://schemas.microsoft.com/crm/2011/Contracts' );
            $requestNode->setAttribute( 'i:type', 'e:' . $requestType );
        }

        $parametersNode = $requestNode->appendChild( $executeActionRequestDOM->createElement( 'b:Parameters' ) );
        $parametersNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:c', 'http://schemas.datacontract.org/2004/07/System.Collections.Generic' );

        if ( $parameters != null && is_array( $parameters ) ) {

            foreach ( $parameters as $parameter ) {
                /* Create a Key/Value Pair of String/Any Type */
                $propertyNode = $parametersNode->appendChild( $executeActionRequestDOM->createElement( 'b:KeyValuePairOfstringanyType' ) );
                /* Set the Property Name */
                $propertyNode->appendChild( $executeActionRequestDOM->createElement( 'c:key', $parameter["key"] ) );
                /* Determine the Type, Value and XML Namespace for this field */
                $xmlValue      = $parameter["value"];
                $xmlValueChild = null;
                $xmlType       = strtolower( $parameter["type"] );
                $xmlTypeNS     = 'http://www.w3.org/2001/XMLSchema';
                /* Special Handing for certain types of field */
                switch ( $xmlType ) {
                    case 'entityreference':
                        /* AlexaCRM\CRMToolkit\Entity\EntityReference - Get a entity xml structure */
                        $xmlType = 'EntityReference';
                        /** @var Entity $entity */
                        $entity      = $xmlValue;
                        $xmlTypeNS   = 'http://schemas.microsoft.com/xrm/2011/Contracts';
                        $entityValue = $executeActionRequestDOM->createElement('c:value');
                        $entityValue->appendChild($executeActionRequestDOM->createElement('b:Id', $entity->ID));
                        $entityValue->appendChild($executeActionRequestDOM->createElement('b:LogicalName', $entity->LogicalName));
                        $xmlValue = null;
                        break;
                    
                    case 'arrayofguid':
						$xmlType = 'ArrayOfguid';
						$arrayOfguids = $xmlValue;
						$xmlTypeNS = 'http://schemas.microsoft.com/2003/10/Serialization/Arrays';
						$entityValue = $executeActionRequestDOM->createElement('c:value');
						foreach ( $arrayOfguids as $contact_guid ) {
							$entityValue->appendChild($executeActionRequestDOM->createElement( 'd:guid', $contact_guid ));
						}; // end foreach
						$xmlValue = null;
						break;

                    case 'memo':
                        /* Memo - This gets treated as a normal String */
                        $xmlType = 'string';
                        break;
                    case 'integer':
                        /* Integer - This gets treated as an "int" */
                        $xmlType = 'int';
                        break;
                    case 'uniqueidentifier':
                        /* Uniqueidentifier - This gets treated as a guid */
                        $xmlType = 'guid';
                        break;
                    case 'money':
                        $xmlType = 'Money';
                        //$xmlTypeNS = NULL;
                        $xmlValue = $executeActionRequestDOM->createElement( 'c:Value', $parameter["value"] );
                        break;
                    case 'picklist':
                    case 'state':
                    case 'status':
                        /* OptionSetValue - Just get the numerical value, but as an XML structure */
                        $xmlType       = 'OptionSetValue';
                        $xmlTypeNS     = 'http://schemas.microsoft.com/xrm/2011/Contracts';
                        $xmlValue      = null;
                        $xmlValueChild = $executeActionRequestDOM->createElement( 'b:Value', $parameter["value"] );
                        break;
                    case 'boolean':
                        /* Boolean - Just get the numerical value */
                        $xmlValue = ( $parameter["value"] ) ? "true" : "false";
                        break;
                    case 'guid':
                        $xmlType   = 'guid';
                        $xmlTypeNS = 'http://schemas.microsoft.com/2003/10/Serialization/';
                        break;
                    case 'base64binary':
                        $xmlType = 'base64Binary';
                        break;
                    case 'string':
                    case 'int':
                    case 'decimal':
                    case 'double':
                        /* No special handling for these types */
                        break;
                    default:
                        /* If we're using Default, Warn user that the XML handling is not defined */
                        trigger_error( 'No Create/Update handling implemented for type ' . $xmlType . ' used by field ' . $parameter["key"], E_USER_WARNING );
                }
                /* Now create the XML Node for the Value */
                $valueNode = isset($entityValue) ? $propertyNode->appendChild($entityValue) :
                    $propertyNode->appendChild($executeActionRequestDOM->createElement('c:value'));
                $entityValue = null;
                /* Set the Type of the Value */
                $valueNode->setAttribute( 'i:type', 'd:' . $xmlType );
                $valueNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/', 'xmlns:d', $xmlTypeNS );
                /* If there is a child node needed, append it */
                if ( $xmlValueChild != null ) {
                    $valueNode->appendChild( $xmlValueChild );
                }
                /* If there is a value, set it */
                if ( $xmlValue != null ) {
                    $valueNode->appendChild( new DOMText( $xmlValue ) );
                }
            }
        }
        $requestIdNode = $requestNode->appendChild( $executeActionRequestDOM->createElement( 'b:RequestId' ) );
        $requestIdNode->setAttribute( 'i:nil', 'true' );
        $requestNode->appendChild( $executeActionRequestDOM->createElement( 'b:RequestName', $requestName ) );

        return $executeActionNode;
    }

}
