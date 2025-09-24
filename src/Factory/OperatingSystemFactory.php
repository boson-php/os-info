<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory;

use Boson\Component\OsInfo\Factory\Driver\CodenameDriverInterface;
use Boson\Component\OsInfo\Factory\Driver\EditionDriverInterface;
use Boson\Component\OsInfo\Factory\Driver\NameDriverInterface;
use Boson\Component\OsInfo\Factory\Driver\StandardsDriverInterface;
use Boson\Component\OsInfo\Factory\Driver\VersionDriverInterface;
use Boson\Component\OsInfo\Family\Factory\FamilyFactoryInterface;
use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\OperatingSystem;
use Boson\Component\OsInfo\StandardInterface;

final readonly class OperatingSystemFactory implements OperatingSystemFactoryInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_UNKNOWN_NAME = 'unknown';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_UNKNOWN_VERSION = '1.0';

    /**
     * @var list<object>
     */
    private array $drivers;

    /**
     * @param iterable<mixed, object> $drivers
     * @param non-empty-string $unknownName
     * @param non-empty-string $unknownVersion
     */
    public function __construct(
        private FamilyFactoryInterface $familyFactory,
        iterable $drivers = [],
        private string $unknownName = self::DEFAULT_UNKNOWN_NAME,
        private string $unknownVersion = self::DEFAULT_UNKNOWN_VERSION,
    ) {
        $this->drivers = \iterator_to_array($drivers, false);
    }

    public function createOperatingSystem(): OperatingSystem
    {
        $family = $this->familyFactory->createFamily();

        return new OperatingSystem(
            family: $family,
            name: $this->getName($family),
            version: $this->getVersion($family),
            codename: $this->getCodename($family),
            edition: $this->getEdition($family),
            standards: $this->getStandards($family),
        );
    }

    /**
     * @template TArg of object
     * @template TResult of mixed
     *
     * @param class-string<TArg> $type
     * @param \Closure(TArg):(TResult|null) $then
     *
     * @return TResult|null
     */
    private function filter(string $type, \Closure $then): mixed
    {
        foreach ($this->drivers as $driver) {
            if (!$driver instanceof $type) {
                continue;
            }

            $result = $then($driver);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @return non-empty-string
     */
    private function getName(FamilyInterface $family): string
    {
        return $this->filter(
            type: NameDriverInterface::class,
            then: fn(NameDriverInterface $driver): ?string => $driver->tryGetName($family),
        ) ?? $this->unknownName;
    }

    /**
     * @return non-empty-string
     */
    private function getVersion(FamilyInterface $family): string
    {
        return $this->filter(
            type: VersionDriverInterface::class,
            then: fn(VersionDriverInterface $driver): ?string => $driver->tryGetVersion($family),
        ) ?? $this->unknownVersion;
    }

    /**
     * @return non-empty-string|null
     */
    private function getEdition(FamilyInterface $family): ?string
    {
        return $this->filter(
            type: EditionDriverInterface::class,
            then: fn(EditionDriverInterface $driver): ?string => $driver->tryGetEdition($family),
        );
    }

    /**
     * @return non-empty-string|null
     */
    private function getCodename(FamilyInterface $family): ?string
    {
        return $this->filter(
            type: CodenameDriverInterface::class,
            then: fn(CodenameDriverInterface $driver): ?string => $driver->tryGetCodename($family),
        );
    }

    /**
     * @return iterable<array-key, StandardInterface>
     */
    private function getStandards(FamilyInterface $family): iterable
    {
        return $this->filter(
            type: StandardsDriverInterface::class,
            then: fn(StandardsDriverInterface $driver): ?iterable => $driver->tryGetStandards($family),
        ) ?? [];
    }
}
