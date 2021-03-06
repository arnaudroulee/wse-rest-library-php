<?php
//
// This code and all components (c) Copyright 2006 - 2016, Wowza Media Systems, LLC. All rights reserved.
// This code is licensed pursuant to the Wowza Public License version 1.0, available at www.wowza.com/legal.
//

namespace Com\Wowza;

use Com\Wowza\Entities\Application\Helpers\Settings;

class Server extends Wowza
{
    public function __construct(
        Settings $settings
    ) {
        parent::__construct($settings);
        $this->baseUrl = "{$settings->getHost()}/servers/{$settings->getServerInstance()}";
    }

    public function getUsers()
    {
        $this->restURI = $this->baseUrl . "/users";

        $entities = $this->getEntites([], $this->restURI);

        return $this->sendRequest($this->preparePropertiesForRequest(self::class), [], self::VERB_GET);
    }

    public function createUser($name, $password, $groups = [])
    {
        $this->restURI = $this->baseUrl . "/users/{$name}";

        $this->addAdditionalParameter('name', $name)
            ->addAdditionalParameter('password', $password)
            ->addAdditionalParameter('groups', $groups);

        $entities = $this->getEntites([], $this->restURI);

        return $this->sendRequest($this->preparePropertiesForRequest(self::class), []);
    }

    public function removeUser($name)
    {
        $this->restURI = $this->baseUrl . "/users/{$name}";

        return $this->sendRequest($this->preparePropertiesForRequest(self::class), [], self::VERB_DELETE);
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
