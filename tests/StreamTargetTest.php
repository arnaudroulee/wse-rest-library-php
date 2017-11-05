<?php

/**
 * Created by PhpStorm.
 * User: arnaudroulee
 * Date: 05/11/2017
 * Time: 19:26
 */

namespace Com\Wowza\Test;

use Com\Wowza\Entities\Application\Helpers\Settings;
use Com\Wowza\StreamTarget;

class StreamTargetTest extends \PHPUnit_Framework_TestCase
{
	public function testCreate() {
		$setting = new Settings();

		$application = new StreamTarget($setting, "live");

		$result = $application->create();
		$result = $application->create();
		$result = $application->create();
		$result = $application->create();
	}
}