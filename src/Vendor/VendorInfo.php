<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Vendor;

readonly class VendorInfo implements VendorInfoInterface, \Stringable
{
    public function __construct(
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
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
