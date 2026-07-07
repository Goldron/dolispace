<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UserModel;

/**
 * @internal
 */
final class UserModelTest extends CIUnitTestCase
{
    private function callHashPassword(UserModel $model, array $data): array
    {
        $ref = new \ReflectionMethod($model, 'hashPassword');
        $ref->setAccessible(true);

        return $ref->invoke($model, $data);
    }

    public function testHashPasswordHashesAndStampsUpdatedAt(): void
    {
        $model  = new UserModel();
        $result = $this->callHashPassword($model, ['data' => ['password' => 'Secret123']]);

        $this->assertNotSame('Secret123', $result['data']['password']);
        $this->assertTrue(password_verify('Secret123', $result['data']['password']));
        $this->assertArrayHasKey('password_updated_at', $result['data']);
        $this->assertSame(date('Y-m-d'), substr($result['data']['password_updated_at'], 0, 10));
    }

    public function testHashPasswordLeavesDataUntouchedWhenNoPassword(): void
    {
        $model = new UserModel();
        $input = ['data' => ['email' => 'test@example.com']];

        $this->assertSame($input, $this->callHashPassword($model, $input));
    }

    public function testIsPendingExpiredTrueWhenMissing(): void
    {
        $model = new UserModel();

        $this->assertTrue($model->isPendingExpired(['email_pending_expires_at' => null]));
        $this->assertTrue($model->isPendingExpired([]));
    }

    public function testIsPendingExpiredFalseWhenInFuture(): void
    {
        $model  = new UserModel();
        $future = date('Y-m-d H:i:s', time() + 3600);

        $this->assertFalse($model->isPendingExpired(['email_pending_expires_at' => $future]));
    }

    public function testIsPendingExpiredTrueWhenInPast(): void
    {
        $model = new UserModel();
        $past  = date('Y-m-d H:i:s', time() - 3600);

        $this->assertTrue($model->isPendingExpired(['email_pending_expires_at' => $past]));
    }

    public function testIsPasswordPendingExpiredUsesOwnField(): void
    {
        $model  = new UserModel();
        $future = date('Y-m-d H:i:s', time() + 3600);

        $this->assertFalse($model->isPasswordPendingExpired(['password_pending_expires_at' => $future]));
        $this->assertTrue($model->isPasswordPendingExpired([]));
    }
}
