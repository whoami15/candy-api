<?php

namespace GetCandy\Api\Installer;

use GetCandy\Api\Installer\Runners\AssetRunner;
use GetCandy\Api\Installer\Runners\AssociationGroupRunner;
use GetCandy\Api\Installer\Runners\AttributeRunner;
use GetCandy\Api\Installer\Runners\ChannelRunner;
use GetCandy\Api\Installer\Runners\CountryRunner;
use GetCandy\Api\Installer\Runners\CurrencyRunner;
use GetCandy\Api\Installer\Runners\CustomerGroupRunner;
use GetCandy\Api\Installer\Runners\LanguageRunner;
use GetCandy\Api\Installer\Runners\PreflightRunner;
use GetCandy\Api\Installer\Runners\ProductFamilyRunner;
use GetCandy\Api\Installer\Runners\SettingsRunner;
use GetCandy\Api\Installer\Runners\TaxRunner;
use GetCandy\Api\Installer\Runners\UserRunner;
use Illuminate\Console\Command;

class GetCandyInstaller
{
    protected $command;

    protected $runners = [
        'preflight' => PreflightRunner::class,
        'settings' => SettingsRunner::class,
        'customer_groups' => CustomerGroupRunner::class,
        'languages' => LanguageRunner::class,
        'taxes' => TaxRunner::class,
        'attributes' => AttributeRunner::class,
        'currencies' => CurrencyRunner::class,
        'assets' => AssetRunner::class,
        'association_groups' => AssociationGroupRunner::class,
        'countries' => CountryRunner::class,
        'users' => UserRunner::class,
        'channels' => ChannelRunner::class,
        'product_families' => ProductFamilyRunner::class,
    ];

    /**
     * Sets the command instance for running the installer.
     *
     * @param Command $command
     * @return self
     */
    public function onCommand(Command $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Run the installer.
     *
     * @return void
     */
    public function run()
    {
        if (! $this->command) {
            throw new \Exception('You must attach a command instance to the installer');
        }
        $this->getRunners()->each(function ($runner) {
            $runner = app()->make($runner);
            $runner->run();
            $runner->after();
        });
    }

    public function getRunners()
    {
        return collect(array_merge(
            $this->runners,
            config('getcandy.installer.runners', [])
        ));
    }
}
