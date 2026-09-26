<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle\EventSubscriber;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminAssetSubscriber implements EventSubscriberInterface
{
    private const JS_PATHS_EVENT = 'onJsPaths';
    private const CSS_PATHS_EVENT = 'onCssPaths';

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::JS_PATHS => self::JS_PATHS_EVENT,
            BundleManagerEvents::CSS_PATHS => self::CSS_PATHS_EVENT,
        ];
    }

    /**
     * @param PathsEvent $event
     * @return void
     */
    public function onJsPaths(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/safedelete/js/safedeletepanel/dependenciesLoader.js',
            '/bundles/safedelete/js/safedeletepanel/dependencyPanel.js',
            '/bundles/safedelete/js/safedeletepanel/safeDeletePanel.js',
            '/bundles/safedelete/js/pimcore/elementService.js',
            '/bundles/safedelete/js/elementNavigator.js',
        ]);
    }

    /**
     * @param PathsEvent $event
     * @return void
     */
    public function onCssPaths(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/safedelete/css/dependencyPanel.css'
        ]);
    }
}
