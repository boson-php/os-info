<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Tests\Factory\Driver;

use Boson\Component\OsInfo\Factory\Driver\EnvDriver;
use Boson\Component\OsInfo\Standard;
use Boson\Component\OsInfo\Tests\TestCase;
use Boson\Contracts\OsInfo\FamilyInterface;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Group;

#[Group('boson-php/os-info')]
final class EnvDriverTest extends TestCase
{
    private readonly EnvDriver $driver;

    private array $originalServer;

    #[Before]
    protected function setUpEnvDriver(): void
    {
        $this->driver = new EnvDriver();
    }

    #[Before]
    protected function setUpEnvVariables(): void
    {
        $this->originalServer = $_SERVER;
    }

    #[After]
    protected function rollbackEnvVariables(): void
    {
        $_SERVER = $this->originalServer;
    }

    public function testConstructorWithDefaultParameters(): void
    {
        $driver = new EnvDriver();

        self::assertInstanceOf(EnvDriver::class, $driver);
    }

    public function testConstructorWithCustomParameters(): void
    {
        $driver = new EnvDriver(
            nameEnvVariableNames: ['CUSTOM_NAME'],
            versionEnvVariableNames: ['CUSTOM_VERSION'],
            codenameEnvVariableNames: ['CUSTOM_CODENAME'],
            editionEnvVariableNames: ['CUSTOM_EDITION'],
            standardsEnvVariableNames: ['CUSTOM_STANDARDS'],
        );

        self::assertInstanceOf(EnvDriver::class, $driver);
    }

    public function testCreateForOverrideEnvVariables(): void
    {
        $driver = EnvDriver::createForOverrideEnvVariables();

        self::assertInstanceOf(EnvDriver::class, $driver);
    }

    public function testTryGetNameWithEnvironmentVariable(): void
    {
        $_SERVER['TEST_NAME'] = 'TestOS';
        $driver = new EnvDriver(nameEnvVariableNames: ['TEST_NAME']);
        $family = $this->createMock(FamilyInterface::class);

        $name = $driver->tryGetName($family);

        self::assertSame('TestOS', $name);
    }

    public function testTryGetNameWithMultipleEnvironmentVariables(): void
    {
        $_SERVER['TEST_NAME1'] = '';
        $_SERVER['TEST_NAME2'] = 'TestOS';
        $driver = new EnvDriver(nameEnvVariableNames: ['TEST_NAME1', 'TEST_NAME2']);
        $family = $this->createMock(FamilyInterface::class);

        $name = $driver->tryGetName($family);

        self::assertSame('TestOS', $name);
    }

    public function testTryGetNameWithNoEnvironmentVariable(): void
    {
        $driver = new EnvDriver(nameEnvVariableNames: ['NONEXISTENT']);
        $family = $this->createMock(FamilyInterface::class);

        $name = $driver->tryGetName($family);

        self::assertNull($name);
    }

    public function testTryGetVersionWithEnvironmentVariable(): void
    {
        $_SERVER['TEST_VERSION'] = '1.0.0';
        $driver = new EnvDriver(versionEnvVariableNames: ['TEST_VERSION']);
        $family = $this->createMock(FamilyInterface::class);

        $version = $driver->tryGetVersion($family);

        self::assertSame('1.0.0', $version);
    }

    public function testTryGetVersionWithNoEnvironmentVariable(): void
    {
        $driver = new EnvDriver(versionEnvVariableNames: ['NONEXISTENT']);
        $family = $this->createMock(FamilyInterface::class);

        $version = $driver->tryGetVersion($family);

        self::assertNull($version);
    }

    public function testTryGetCodenameWithEnvironmentVariable(): void
    {
        $_SERVER['TEST_CODENAME'] = 'TestCodename';
        $driver = new EnvDriver(codenameEnvVariableNames: ['TEST_CODENAME']);
        $family = $this->createMock(FamilyInterface::class);

        $codename = $driver->tryGetCodename($family);

        self::assertSame('TestCodename', $codename);
    }

    public function testTryGetCodenameWithNoEnvironmentVariable(): void
    {
        $driver = new EnvDriver(codenameEnvVariableNames: ['NONEXISTENT']);
        $family = $this->createMock(FamilyInterface::class);

        $codename = $driver->tryGetCodename($family);

        self::assertNull($codename);
    }

    public function testTryGetEditionWithEnvironmentVariable(): void
    {
        $_SERVER['TEST_EDITION'] = 'TestEdition';
        $driver = new EnvDriver(editionEnvVariableNames: ['TEST_EDITION']);
        $family = $this->createMock(FamilyInterface::class);

        $edition = $driver->tryGetEdition($family);

        self::assertSame('TestEdition', $edition);
    }

    public function testTryGetEditionWithNoEnvironmentVariable(): void
    {
        $driver = new EnvDriver(editionEnvVariableNames: ['NONEXISTENT']);
        $family = $this->createMock(FamilyInterface::class);

        $edition = $driver->tryGetEdition($family);

        self::assertNull($edition);
    }

    public function testTryGetStandardsWithValidStandard(): void
    {
        $_SERVER['TEST_STANDARDS'] = 'posix';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(1, $standards);
        self::assertContains(Standard::Posix, $standards);
    }

    public function testTryGetStandardsWithInvalidStandard(): void
    {
        $_SERVER['TEST_STANDARDS'] = 'invalid';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertNull($standards);
    }

    public function testTryGetStandardsWithMultipleStandards(): void
    {
        $_SERVER['TEST_STANDARDS'] = 'posix;posix';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(2, $standards);
        self::assertContains(Standard::Posix, $standards);
    }

    public function testTryGetStandardsWithUnixSeparator(): void
    {
        $_SERVER['TEST_STANDARDS'] = 'posix:posix';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(2, $standards);
        self::assertContains(Standard::Posix, $standards);
    }

    public function testTryGetStandardsWithMixedSeparators(): void
    {
        $_SERVER['TEST_STANDARDS'] = 'posix;posix:posix';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertIsArray($standards);
        self::assertCount(3, $standards);
        self::assertContains(Standard::Posix, $standards);
    }

    public function testTryGetStandardsWithEmptyString(): void
    {
        $_SERVER['TEST_STANDARDS'] = '';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertNull($standards);
    }

    public function testTryGetStandardsWithWhitespaceOnly(): void
    {
        $_SERVER['TEST_STANDARDS'] = '   ';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertNull($standards);
    }

    public function testTryGetStandardsWithNoEnvironmentVariable(): void
    {
        $driver = new EnvDriver(standardsEnvVariableNames: ['NONEXISTENT']);
        $family = $this->createMock(FamilyInterface::class);

        $standards = $driver->tryGetStandards($family);

        self::assertNull($standards);
    }

    public function testTryGetStandardsFromEnvironmentAsString(): void
    {
        $_SERVER['TEST_STANDARDS'] = 'posix';
        $driver = new EnvDriver(standardsEnvVariableNames: ['TEST_STANDARDS']);

        $standardsString = $driver->tryGetStandardsFromEnvironmentAsString();

        self::assertSame('posix', $standardsString);
    }

    public function testTryGetStandardsFromEnvironmentAsStringWithNoVariable(): void
    {
        $driver = new EnvDriver(standardsEnvVariableNames: ['NONEXISTENT']);

        $standardsString = $driver->tryGetStandardsFromEnvironmentAsString();

        self::assertNull($standardsString);
    }

    public function testConstantsAreDefined(): void
    {
        self::assertSame('BOSON_OS_NAME', EnvDriver::DEFAULT_OVERRIDE_ENV_NAME);
        self::assertSame('BOSON_OS_VERSION', EnvDriver::DEFAULT_OVERRIDE_ENV_VERSION);
        self::assertSame('BOSON_OS_CODENAME', EnvDriver::DEFAULT_OVERRIDE_ENV_CODENAME);
        self::assertSame('BOSON_OS_EDITION', EnvDriver::DEFAULT_OVERRIDE_ENV_EDITION);
        self::assertSame('BOSON_OS_STANDARDS', EnvDriver::DEFAULT_OVERRIDE_ENV_STANDARDS);
    }
}
