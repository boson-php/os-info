<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Component\OsInfo\Factory\DefaultOperatingSystemFactory;
use Boson\Component\OsInfo\Factory\InMemoryOperatingSystemFactory;
use Boson\Component\OsInfo\Factory\OperatingSystemFactoryInterface;
use Boson\Contracts\OsInfo\FamilyInterface;
use Boson\Contracts\OsInfo\OperatingSystemInterface;
use Boson\Contracts\OsInfo\StandardInterface;

final readonly class OperatingSystem implements OperatingSystemInterface
{
    /**
     * @var list<StandardInterface>
     */
    public array $standards;

    /**
     * @param iterable<mixed, StandardInterface> $standards
     */
    public function __construct(
        public FamilyInterface $family,
        /**
         * @var non-empty-string
         */
        public string $name,
        /**
         * @var non-empty-string
         */
        public string $version,
        /**
         * @var non-empty-string|null
         */
        public ?string $codename = null,
        /**
         * @var non-empty-string|null
         */
        public ?string $edition = null,
        iterable $standards = [],
    ) {
        $this->standards = \iterator_to_array($standards, false);
    }

    /**
     * @api
     */
    public static function createFromGlobals(): OperatingSystemInterface
    {
        static $factory = new InMemoryOperatingSystemFactory(
            delegate: new DefaultOperatingSystemFactory(),
        );

        /** @var OperatingSystemFactoryInterface $factory */
        return $factory->createOperatingSystemFromGlobals();
    }

    public function isSupports(StandardInterface $standard): bool
    {
        foreach ($this->standards as $actual) {
            if ($actual->is($standard)) {
                return true;
            }
        }

        return false;
    }
}
