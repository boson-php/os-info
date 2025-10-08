<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family\Factory;

use Boson\Component\OsInfo\FamilyInterface;

final readonly class DefaultFamilyFactory implements FamilyFactoryInterface
{
    private FamilyFactoryInterface $factory;

    public function __construct()
    {
        $this->factory = EnvFamilyFactory::createForOverrideEnvVariables(
            delegate: new GenericFamilyFactory(),
        );
    }

    public function createFamily(): FamilyInterface
    {
        return $this->factory->createFamily();
    }
}
