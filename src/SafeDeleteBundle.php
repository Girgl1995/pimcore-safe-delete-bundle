<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;

class SafeDeleteBundle extends AbstractPimcoreBundle
{
    /**
     * @param ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    /**
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new DependencyInjection\Extension();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    /**
     * @return InstallerInterface|null
     */
    public function getInstaller(): ?InstallerInterface
    {
        return $this->container->get(Installer::class);
    }
}
