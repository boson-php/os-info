<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Factory\Driver;

use Boson\Component\OsInfo\FamilyInterface;
use Boson\Component\OsInfo\Standard;
use Boson\Component\OsInfo\StandardInterface;

final readonly class EnvDriver implements
    NameDriverInterface,
    VersionDriverInterface,
    CodenameDriverInterface,
    EditionDriverInterface,
    StandardsDriverInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_NAME = 'BOSON_OS_NAME';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_VERSION = 'BOSON_OS_VERSION';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_CODENAME = 'BOSON_OS_CODENAME';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_EDITION = 'BOSON_OS_EDITION';

    /**
     * @var non-empty-string
     */
    public const string DEFAULT_OVERRIDE_ENV_STANDARDS = 'BOSON_OS_STANDARDS';

    public function __construct(
        /**
         * @var list<non-empty-string>
         */
        private array $nameEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $versionEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $codenameEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $editionEnvVariableNames = [],
        /**
         * @var list<non-empty-string>
         */
        private array $standardsEnvVariableNames = [],
    ) {}

    /**
     * Creates an instance configured to use the default override
     * environment variable.
     */
    public static function createForOverrideEnvVariables(): self
    {
        return new self(
            nameEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_NAME],
            versionEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_VERSION],
            codenameEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_CODENAME],
            editionEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_EDITION],
            standardsEnvVariableNames: [self::DEFAULT_OVERRIDE_ENV_STANDARDS],
        );
    }

    public function tryGetName(FamilyInterface $family): ?string
    {
        return $this->tryGetEnvironmentAsString($this->nameEnvVariableNames);
    }

    public function tryGetVersion(FamilyInterface $family): ?string
    {
        return $this->tryGetEnvironmentAsString($this->versionEnvVariableNames);
    }

    public function tryGetCodename(FamilyInterface $family): ?string
    {
        return $this->tryGetEnvironmentAsString($this->codenameEnvVariableNames);
    }

    public function tryGetEdition(FamilyInterface $family): ?string
    {
        return $this->tryGetEnvironmentAsString($this->editionEnvVariableNames);
    }

    /**
     * @return iterable<array-key, StandardInterface>|null
     */
    public function tryGetStandards(FamilyInterface $family): ?iterable
    {
        $standardStrings = $this->tryGetStandardsFromEnvironmentAsStringArray();

        if ($standardStrings === null) {
            return null;
        }

        $standardInstances = [];

        foreach ($standardStrings as $standardStringValue) {
            $standardInstance = Standard::tryFrom($standardStringValue);

            if ($standardInstance instanceof StandardInterface) {
                $standardInstances[] = $standardInstance;
            }
        }

        return $standardInstances === [] ? null : $standardInstances;
    }

    /**
     * @param iterable<mixed, non-empty-string> $envVariables
     *
     * @return non-empty-string|null
     */
    private function tryGetEnvironmentAsString(iterable $envVariables): ?string
    {
        foreach ($envVariables as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }

    /**
     * @return non-empty-string|null
     */
    public function tryGetStandardsFromEnvironmentAsString(): ?string
    {
        foreach ($this->standardsEnvVariableNames as $name) {
            $server = $_SERVER[$name] ?? null;

            if (\is_string($server) && $server !== '') {
                return $server;
            }
        }

        return null;
    }

    /**
     * @return non-empty-list<non-empty-string>|null
     */
    private function tryGetStandardsFromEnvironmentAsStringArray(): ?array
    {
        $standardsStringValue = $this->tryGetStandardsFromEnvironmentAsString();

        if ($standardsStringValue === null) {
            return null;
        }

        $standardStringValues = [];

        // The ";" is a Windows separator
        foreach (\explode(';', $standardsStringValue) as $segment) {
            // The ":" is a *nix (macOS/Linux) separator
            foreach (\explode(':', $segment) as $standardStringValue) {
                $standardStringValue = \trim($standardStringValue);

                if ($standardStringValue !== '') {
                    $standardStringValues[] = $standardStringValue;
                }
            }
        }

        if ($standardStringValues === []) {
            return null;
        }

        return $standardStringValues;
    }
}
