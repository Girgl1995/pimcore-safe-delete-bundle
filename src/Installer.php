<?php

declare(strict_types=1);

namespace Factotum\SafeDeleteBundle;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;

class Installer extends AbstractInstaller
{
    /**
     * @return bool
     */
    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function install(): void
    {
        parent::install();
    }

    /**
     * @return void
     */
    public function uninstall(): void
    {
        parent::uninstall();
    }
}
