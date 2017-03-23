<?php

use AlexaCRM\CRMToolkit\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase {

    private static $emptyOptions = [ ];

    private static $contosoOptions = [
        'serverUrl' => 'https://contoso.crm4.dynamics.com',
        'username'  => 'portal@contoso.onmicrosoft.com',
        'password'  => 'portalPassword',
        'authMode'  => 'OnlineFederation',
    ];

    private static $fourthCoffeeOptions = [
        'serverUrl' => 'https://crm.fourthcoffee.com',
        'username'  => 'portal@fourthcoffee.com',
        'password'  => 'portalPassword',
        'authMode'  => 'Federation',
    ];

    /**
     * Throw InvalidArgumentException when no options supplied to Settings
     */
    public function testEmptyOptions() {
        $this->expectException( InvalidArgumentException::class );
        $options  = [ ];
        $settings = new Settings( static::$emptyOptions );
    }

    public function testOnlineFederationOptions() {
        $settings = new Settings( static::$contosoOptions );

        $crmRegion           = 'crmemea:dynamics.com';
        $discoveryUrl        = 'https://disco.crm4.dynamics.com/XRMServices/2011/Discovery.svc';
        $organizationUrl     = 'https://contoso.api.crm4.dynamics.com/XRMServices/2011/Organization.svc';

        $this->assertEquals( $crmRegion, $settings->crmRegion, 'CRM Region doesn\'t map the given Server URL.' );
        $this->assertEquals( $discoveryUrl, $settings->discoveryUrl, 'CRM Discovery URL doesn\'t match the given Server URL.' );
        $this->assertEquals( $organizationUrl, $settings->organizationUrl, 'CRM Organization URL doesn\'t match the given Server URL.' );
    }

    public function testFederationOptions() {
        $settings = new Settings( static::$fourthCoffeeOptions );

        $discoveryUrl        = 'https://crm.fourthcoffee.com/XRMServices/2011/Discovery.svc';
        $organizationUrl     = 'https://crm.fourthcoffee.com/XRMServices/2011/Organization.svc';

        $this->assertNull( $settings->crmRegion, 'CRM On-Premises can\'t have a CRM Region.' );
        $this->assertEquals( $discoveryUrl, $settings->discoveryUrl, 'CRM Discovery URL doesn\'t match the given Server URL.' );
        $this->assertEquals( $organizationUrl, $settings->organizationUrl, 'CRM Organization URL doesn\'t match the given Server URL.' );
    }
}
