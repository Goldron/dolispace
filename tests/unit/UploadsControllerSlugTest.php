<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\UploadsController;

/**
 * @internal
 */
final class UploadsControllerSlugTest extends CIUnitTestCase
{
    private function callPrivate(object $obj, string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod($obj, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($obj, $args);
    }

    public function testSlugifyLowercasesAndStripsAccents(): void
    {
        $controller = new UploadsController();

        $this->assertSame('societe-eneve', $this->callPrivate($controller, 'slugify', ['Société Énève']));
    }

    public function testSlugifyCollapsesNonAlphanumeric(): void
    {
        $controller = new UploadsController();

        $this->assertSame('a-b-c', $this->callPrivate($controller, 'slugify', ['A!!  B__C']));
    }

    public function testSlugifyFallsBackWhenEmpty(): void
    {
        $controller = new UploadsController();

        $this->assertSame('fichier', $this->callPrivate($controller, 'slugify', ['???']));
    }

    public function testRandomTokenHasRequestedLength(): void
    {
        $controller = new UploadsController();
        $token      = $this->callPrivate($controller, 'randomToken', [9]);

        $this->assertSame(9, strlen($token));
        $this->assertMatchesRegularExpression('/^[0-9A-Z]{9}$/', $token);
    }

    public function testRandomTokenIsRandom(): void
    {
        $controller = new UploadsController();

        $a = $this->callPrivate($controller, 'randomToken', [20]);
        $b = $this->callPrivate($controller, 'randomToken', [20]);

        $this->assertNotSame($a, $b);
    }
}
