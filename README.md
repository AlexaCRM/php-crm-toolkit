# php-crm-toolkit
CRM Toolkit for PHP provides integration with Microsoft Dynamics CRM in PHP applications.

## Installation

Install the latest version with

```bash
$ composer require alexacrm/php-crm-toolkit
```

# Examples
```php

/**
 * Use init.php if you didn't install the package via Composer
 */
require_once 'vendor/autoload.php';

use AlexaCRM\CRMToolkit\Client as OrganizationService;
use AlexaCRM\CRMToolkit\Settings;

$contactId = '1d2fc62f-1c56-448b-b546-edfb6d6fec5c';
$options = [
    'serverUrl' => 'https://org.crmN.dynamics.com',
    'username' => 'portal@org.onmicrosoft.com',
    'password' => 'portalPassword',
    'authMode' => 'OnlineFederation',
];

$serviceSettings = new Settings( $options );
$service = new OrganizationService( $serviceSettings );

// retrieve a contact and update its fields
$contact = $service->entity( 'contact', $guid );
$contact->firstname = explode( '@', $contact->emailaddress1 )[0];
$contact->update();
printf( 'Info for %s %s updated.', $contact->firstname, $contact->lastname );

// create a new contact
$contact = $service->entity( 'contact' );
$contact->firstname = 'John';
$contact->lastname = 'Doe';
$contact->emailaddress1 = 'john.doe@example.com';
$contactId = $contact->create();

// delete a contact
$contact->delete();

// execute an action
$whoAmIResponse = $service->executeAction( 'WhoAmI' );
echo 'Organization ID: ' . $whoAmIResponse->OrganizationId;

// inject cache repo
// must be instance of AlexaCRM\CRMToolkit\CacheInterface
$cacheRepo = Cache::instance();
$service = new Client( $serviceSettings, $cacheRepo );
```

In `/examples/` you can find a few examples of toolkit usage. Copy `config.example.php` to `config.php`, set up credentials for your CRM and you are ready to go!

# Contributing
Pull requests are gladly accepted in the [GitHub repository](https://github.com/AlexaCRM/php-crm-toolkit).

# License
Copyright (c) 2016 AlexaCRM.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation, version 3.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Lesser Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
