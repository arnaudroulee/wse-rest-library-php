<?php

/**
 * Created by PhpStorm.
 * User: arnaudroulee
 * Date: 05/11/2017
 * Time: 18:46
 */

namespace Com\Wowza\Test;

use Com\Wowza\Application;
use Com\Wowza\Entities\Application\Helpers\Settings;
use Com\Wowza\Entities\Application\Modules;
use Com\Wowza\Entities\Application\SecurityConfig;
use Com\Wowza\Entities\Application\StreamConfig;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct() {
		$setting = new Settings();

		$application = new Application($setting, "live");

		$this->assertEquals("live", $application->getName());
		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $application->getBaseUrl());

	}

	public function testGet(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$result = $application->get();

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("GET", $debugData["method"]);
		$this->assertContains("\"settings\":{}", $debugData["data"]);
	}

	public function testCreate(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$streamConfig =new StreamConfig();
		$streamConfig->setStreamType("live");
		$streamConfig->setLiveStreamPacketizer(array("sanjosestreamingpacketizer","cupertinostreamingpacketizer"));

		$result = $application->create($streamConfig);

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("POST", $debugData["method"]);
		$this->assertContains("\"appType\":\"Live\",\"name\":\"live\",\"clientStreamReadAccess\":\"*\",\"clientStreamWriteAccess\":\"*\",\"description\":\"*\"", $debugData["data"]);
	}

	public function testCreateWithSecurity(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$streamConfig =new StreamConfig();
		$streamConfig->setStreamType("live");
		$streamConfig->setLiveStreamPacketizer(array("sanjosestreamingpacketizer","cupertinostreamingpacketizer"));

		// example setting up a security configuration element
		$securityConfig = new SecurityConfig();
		$securityConfig->secureTokenVersion = "0";
		$securityConfig->clientStreamWriteAccess = "*";
		$securityConfig->publishRequirePassword = "true";
		$securityConfig->publishPasswordFile = "";
		$securityConfig->publishRTMPSecureURL = "";
		$securityConfig->publishIPBlackList = "";
		$securityConfig->publishIPWhiteList = "";
		$securityConfig->publishBlockDuplicateStreamNames = "false";
		$securityConfig->publishValidEncoders = "";
		$securityConfig->publishAuthenticationMethod = "digest";
		$securityConfig->playMaximumConnections = "0";
		$securityConfig->playRequireSecureConnection = "false";
		$securityConfig->secureTokenSharedSecret = "";
		$securityConfig->secureTokenUseTEAForRTMP = "false";
		$securityConfig->secureTokenIncludeClientIPInHash = "false";
		$securityConfig->secureTokenHashAlgorithm = "";
		$securityConfig->secureTokenQueryParametersPrefix = "";
		$securityConfig->secureTokenOriginSharedSecret = "";
		$securityConfig->playIPBlackList = "";
		$securityConfig->playIPWhiteList = "";
		$securityConfig->playAuthenticationMethod = "none";

		$result = $application->create($streamConfig, $securityConfig);

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("POST", $debugData["method"]);
		$this->assertContains("\"securityConfig\":{\"secureTokenVersion\":\"0\",\"clientStreamWriteAccess\":\"*\",\"publishRequirePassword\":\"true\",\"publishPasswordFile\":\"\",\"publishRTMPSecureURL\":\"\",\"publishIPBlackList\":\"\",\"publishIPWhiteList\":\"\",\"publishBlockDuplicateStreamNames\":\"false\",\"publishValidEncoders\":\"\",\"publishAuthenticationMethod\":\"digest\",\"playMaximumConnections\":\"0\",\"playRequireSecureConnection\":\"false\",\"secureTokenSharedSecret\":\"\",\"secureTokenUseTEAForRTMP\":\"false\",\"secureTokenIncludeClientIPInHash\":\"false\",\"secureTokenHashAlgorithm\":\"\",\"secureTokenQueryParametersPrefix\":\"\",\"secureTokenOriginSharedSecret\":\"\",\"playIPBlackList\":\"\",\"playIPWhiteList\":\"\",\"playAuthenticationMethod\":\"none\",\"restURI\":\"http:\/\/localhost:8087\/v2\/servers\/_defaultServer_\/vhosts\/_defaultVHost_\/applications\/live\/security\"}", $debugData["data"]);
	}


	public function testCreateWithModule(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$streamConfig =new StreamConfig();
		$streamConfig->setStreamType("live");
		$streamConfig->setLiveStreamPacketizer(array("sanjosestreamingpacketizer","cupertinostreamingpacketizer"));

		$modules = new Modules();
		$modules->moduleList[] = $modules->getModuleItem("ModuleCoreSecurity", "ModuleCoreSecurity", "com.wowza.wms.security.ModuleCoreSecurity");

		$result = $application->create($streamConfig, null, $modules);

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("POST", $debugData["method"]);
		$this->assertContains("\"modules\":{\"moduleList\":[{\"order\":0,\"name\":\"base\",\"description\":\"Base\",\"class\":\"com.wowza.wms.module.ModuleCore\"},{\"order\":1,\"name\":\"logging\",\"description\":\"Client Logging\",\"class\":\"com.wowza.wms.module.ModuleClientLogging\"},{\"order\":2,\"name\":\"flvplayback\",\"description\":\"FLVPlayback\",\"class\":\"com.wowza.wms.module.ModuleFLVPlayback\"},{\"order\":3,\"name\":\"ModuleCoreSecurity\",\"description\":\"ModuleCoreSecurity\",\"class\":\"com.wowza.wms.security.ModuleCoreSecurity\"}],\"restURI\":\"http:\/\/localhost:8087\/v2\/servers\/_defaultServer_\/vhosts\/_defaultVHost_\/applications\/live\/streamconfiguration\"}", $debugData["data"]);
	}

	public function testGetAll(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$result = $application->getAll();

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications", $debugData["url"]);
		$this->assertEquals("GET", $debugData["method"]);
		$this->assertContains("\"settings\":{}", $debugData["data"]);
	}

	public function testRemove(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$result = $application->remove();

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("DELETE", $debugData["method"]);
		$this->assertContains("\"settings\":{}", $debugData["data"]);
	}

	public function testUpdate(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$streamConfig =new StreamConfig();
		$streamConfig->setStreamType("live");
		$streamConfig->setLiveStreamPacketizer(array("sanjosestreamingpacketizer","cupertinostreamingpacketizer"));

		$result = $application->update($streamConfig);

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("PUT", $debugData["method"]);
		$this->assertContains("\"appType\":\"Live\",\"name\":\"live\",\"clientStreamReadAccess\":\"*\",\"clientStreamWriteAccess\":\"*\",\"description\":\"*\"", $debugData["data"]);
	}

	public function testUpdateWithSecurity(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$streamConfig =new StreamConfig();
		$streamConfig->setStreamType("live");
		$streamConfig->setLiveStreamPacketizer(array("sanjosestreamingpacketizer","cupertinostreamingpacketizer"));

		// example setting up a security configuration element
		$securityConfig = new SecurityConfig();
		$securityConfig->secureTokenVersion = "0";
		$securityConfig->clientStreamWriteAccess = "*";
		$securityConfig->publishRequirePassword = "true";
		$securityConfig->publishPasswordFile = "";
		$securityConfig->publishRTMPSecureURL = "";
		$securityConfig->publishIPBlackList = "";
		$securityConfig->publishIPWhiteList = "";
		$securityConfig->publishBlockDuplicateStreamNames = "false";
		$securityConfig->publishValidEncoders = "";
		$securityConfig->publishAuthenticationMethod = "digest";
		$securityConfig->playMaximumConnections = "0";
		$securityConfig->playRequireSecureConnection = "false";
		$securityConfig->secureTokenSharedSecret = "";
		$securityConfig->secureTokenUseTEAForRTMP = "false";
		$securityConfig->secureTokenIncludeClientIPInHash = "false";
		$securityConfig->secureTokenHashAlgorithm = "";
		$securityConfig->secureTokenQueryParametersPrefix = "";
		$securityConfig->secureTokenOriginSharedSecret = "";
		$securityConfig->playIPBlackList = "";
		$securityConfig->playIPWhiteList = "";
		$securityConfig->playAuthenticationMethod = "none";

		$result = $application->update($streamConfig, $securityConfig);

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("PUT", $debugData["method"]);
		$this->assertContains("\"securityConfig\":{\"secureTokenVersion\":\"0\",\"clientStreamWriteAccess\":\"*\",\"publishRequirePassword\":\"true\",\"publishPasswordFile\":\"\",\"publishRTMPSecureURL\":\"\",\"publishIPBlackList\":\"\",\"publishIPWhiteList\":\"\",\"publishBlockDuplicateStreamNames\":\"false\",\"publishValidEncoders\":\"\",\"publishAuthenticationMethod\":\"digest\",\"playMaximumConnections\":\"0\",\"playRequireSecureConnection\":\"false\",\"secureTokenSharedSecret\":\"\",\"secureTokenUseTEAForRTMP\":\"false\",\"secureTokenIncludeClientIPInHash\":\"false\",\"secureTokenHashAlgorithm\":\"\",\"secureTokenQueryParametersPrefix\":\"\",\"secureTokenOriginSharedSecret\":\"\",\"playIPBlackList\":\"\",\"playIPWhiteList\":\"\",\"playAuthenticationMethod\":\"none\",\"restURI\":\"http:\/\/localhost:8087\/v2\/servers\/_defaultServer_\/vhosts\/_defaultVHost_\/applications\/live\/security\"}", $debugData["data"]);
	}


	public function testUpdateWithModule(){
		$setting = new Settings();

		$application = new Application($setting, "live");

		$streamConfig =new StreamConfig();
		$streamConfig->setStreamType("live");
		$streamConfig->setLiveStreamPacketizer(array("sanjosestreamingpacketizer","cupertinostreamingpacketizer"));

		$modules = new Modules();
		$modules->moduleList[] = $modules->getModuleItem("ModuleCoreSecurity", "ModuleCoreSecurity", "com.wowza.wms.security.ModuleCoreSecurity");

		$result = $application->update($streamConfig, null, $modules);

		$debugData = $setting->getDebugRequest();

		$this->assertEquals("http://localhost:8087/v2/servers/_defaultServer_/vhosts/_defaultVHost_/applications/live", $debugData["url"]);
		$this->assertEquals("PUT", $debugData["method"]);
		$this->assertContains("\"modules\":{\"moduleList\":[{\"order\":0,\"name\":\"base\",\"description\":\"Base\",\"class\":\"com.wowza.wms.module.ModuleCore\"},{\"order\":1,\"name\":\"logging\",\"description\":\"Client Logging\",\"class\":\"com.wowza.wms.module.ModuleClientLogging\"},{\"order\":2,\"name\":\"flvplayback\",\"description\":\"FLVPlayback\",\"class\":\"com.wowza.wms.module.ModuleFLVPlayback\"},{\"order\":3,\"name\":\"ModuleCoreSecurity\",\"description\":\"ModuleCoreSecurity\",\"class\":\"com.wowza.wms.security.ModuleCoreSecurity\"}],\"restURI\":\"http:\/\/localhost:8087\/v2\/servers\/_defaultServer_\/vhosts\/_defaultVHost_\/applications\/live\/streamconfiguration\"}", $debugData["data"]);
	}
}