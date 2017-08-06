<?php

namespace Concrete\Package\SlifeCommunityStore;

use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Package\Package as BasePackage;
use Concrete\Core\Support\Facade\Package;

class Controller extends BasePackage
{
    protected $pkgHandle = 'slife_community_store';
    protected $appVersionRequired = '8.2';
    protected $pkgVersion = '0.9.1';
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
        if (!$this->isSlifeInstalled()) {
            return;
        }

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

    public function validate_install($data = [])
    {
        $error = $this->app->make('error');

        if (!$this->isSlifeInstalled()) {
            $error->add(
                t(
                    "Installation requires <a href='%s' target='_blank'>Slife</a> to be installed.",
                    "https://www.concrete5.org/marketplace/addons/slife/"
                )
            );
        }

        return $error;
    }

    /**
     * @return bool
     */
    protected function isSlifeInstalled()
    {
        $basePackage = Package::getByHandle('slife');
        return is_object($basePackage);
    }

    protected function installEvents()
    {
        $th = $this->app->make('helper/text');

        foreach ($this->supportedEvents as $eventHandle) {
            $className = $th->camelcase($eventHandle);
            $eventClass = $this->app->make('SlifeC5Events\Event\\'.$className, [
                'package' => $this->getPackageEntity(),
            ]);

            $eventClass->install();
        }
    }
}
