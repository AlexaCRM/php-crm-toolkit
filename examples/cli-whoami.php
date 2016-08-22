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

use AlexaCRM\CRMToolkit\Client;
use AlexaCRM\CRMToolkit\Entity\MetadataCollection;
use AlexaCRM\CRMToolkit\Settings;

/*
 * Enable autoloading for the toolkit.
 *
 * CRM Toolkit for PHP is a PSR-4 compliant package.
 * If you installed the package via Composer, please use
 * its own autoloader which is generally placed in vendor/autoload.php
 *
 * See: https://getcomposer.org/doc/01-basic-usage.md#autoloading
 */

require_once '../init.php';

$clientOptions  = include( 'config.php' );
$clientSettings = new Settings( $clientOptions );
$client         = new Client( $clientSettings );
$metadata       = MetadataCollection::instance( $client );

/*
 * When Client is instantiated, it connects to the CRM and retrieves
 * additional organization data. It is then stored in $clientSettings.
 */
if ( $clientSettings->hasOrganizationData() ) {
    echo "You have connected to the organization '{$clientSettings->organizationName}' [{$clientSettings->organizationUniqueName}]" . PHP_EOL;
} else {
    die( 'There was an error retrieving organization data for the CRM. Please check connection settings.' );
}

// retrieve "WhoAmI" information
$whoAmIResponse = $client->executeAction( 'WhoAmI' );

echo PHP_EOL . 'WhoAmI request response' . PHP_EOL;
echo '--------------------------' . PHP_EOL;
echo 'UserId: ' . $whoAmIResponse->UserId . PHP_EOL;
echo 'BusinessUnitId: ' . $whoAmIResponse->BusinessUnitId . PHP_EOL;
echo 'OrganizationId: ' . $whoAmIResponse->OrganizationId . PHP_EOL;
