<?php

namespace Concrete\Package\SlifeCommunityStore;

use Concrete\Core\Support\Facade\Events;
use Slife\Integration\SlifePackageController;

class Controller extends SlifePackageController
{
    protected $pkgHandle = 'slife_community_store';
    protected $appVersionRequired = '8.2';
    protected $pkgVersion = '0.9.0';
    protected $pkgAutoloaderRegistries = [
        'src' => '\SlifeCommunityStore',
    ];

    /* See also https://github.com/concrete5-community-store/community_store/wiki/Events */
    protected $supportedEvents = [
        'on_community_store_order',
    ];

    public function getPackageName()
    {
        return t('Slife Community Store');
    }

    public function getPackageDescription()
    {
        return t('Slife Extension that adds and handles community store events.');
    }

    public function on_start()
    {
        $th = $this->app->make('helper/text');

        // Register event listeners
        foreach ($this->supportedEvents as $eventHandle) {
            $className = $th->camelcase($eventHandle);
            $listener = $this->app->make('SlifeCommunityStore\Event\\'.$className, [
                'package' => $this->getPackageEntity(),
            ]);
            Events::addListener($eventHandle, [$listener, 'run']);
        }
    }
}
