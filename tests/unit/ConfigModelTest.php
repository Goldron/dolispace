<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\ConfigModel;

/**
 * @internal
 */
final class ConfigModelTest extends CIUnitTestCase
{
    private function cast(ConfigModel $model, ?string $value, string $type): mixed
    {
        $ref = new \ReflectionMethod($model, 'cast');
        $ref->setAccessible(true);

        return $ref->invoke($model, $value, $type);
    }

    public function testCastBool(): void
    {
        $model = new ConfigModel();

        $this->assertTrue($this->cast($model, 'true', 'bool'));
        $this->assertFalse($this->cast($model, 'false', 'bool'));
        $this->assertFalse($this->cast($model, '', 'bool'));
    }

    public function testCastInt(): void
    {
        $model = new ConfigModel();

        $this->assertSame(42, $this->cast($model, '42', 'int'));
        $this->assertSame(0, $this->cast($model, 'abc', 'int'));
    }

    public function testCastFloat(): void
    {
        $model = new ConfigModel();

        $this->assertSame(3.14, $this->cast($model, '3.14', 'float'));
    }

    public function testCastJson(): void
    {
        $model = new ConfigModel();

        $this->assertSame(['a' => 1], $this->cast($model, '{"a":1}', 'json'));
        $this->assertNull($this->cast($model, 'not json', 'json'));
    }

    public function testCastDefaultReturnsRawString(): void
    {
        $model = new ConfigModel();

        $this->assertSame('hello', $this->cast($model, 'hello', 'string'));
    }
}
