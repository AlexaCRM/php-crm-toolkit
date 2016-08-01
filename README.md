# php-crm-toolkit
AlexaCRM CRM Toolkit for PHP provides integration with Microsoft Dynamics CRM.

# Examples
```
use AlexaCRM\CRMToolkit\Client;
use AlexaCRM\CRMToolkit\Settings;

$contactId = '1d2fc62f-1c56-448b-b546-edfb6d6fec5c';
$options = [
    'serverUrl' => 'https://org.crmN.dynamics.com',
    'username' => 'portal@org.onmicrosoft.com',
    'password' => 'portalPassword',
    'authMode' => 'OnlineFederation',
];

$clientSettings = new Settings( $options );
$client = new Client( $clientSettings );

// retrieve a contact and update its fields
$contact = $client->entity( 'contact', $guid );
$contact->firstname = explode( '@', $contact->emailaddress1 )[0];
$contact->update();

// create a new contact
$contact = $client->entity( 'contact' );
$contact->firstname = 'John';
$contact->lastname = 'Doe';
$contact->emailaddress1 = 'john.doe@example.com';
$contactId = $contact->create();

// delete a contact
$contact->delete();

// execute an action
$client->executeAction( 'WhoAmI' );

// inject cache repo
// must be instance of AlexaCRM\CRMToolkit\CacheInterface
$cacheRepo = Cache::instance();
$client = new Client( clientSettings, $cacheRepo );
```

# Contributing
Pull requests are gladly accepted in the GitHub repository.

# License
Copyright (c) 2016 AlexaCRM.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation, version 3.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Lesser Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
