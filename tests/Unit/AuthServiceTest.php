<?php

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    protected $authRepository;
    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $this->authService = new AuthService($this->authRepository);
    }

    public function test_register_successfully()
    {
        $userData = ['name' => 'leo messi', 'email' => 'leo@example.com', 'password' => 'password', 'role' => 'student'];
        $user = new User($userData);
        $token = 'fake-jwt-token';

        $this->authRepository->shouldReceive('createUser')
            ->once()
            ->with($userData)
            ->andReturn($user);

        $guard = Mockery::mock();
        $guard->shouldReceive('login')->once()->with($user)->andReturn($token);
        
        Auth::shouldReceive('guard')->with('api')->andReturn($guard);

        $result = $this->authService->register($userData);

        $this->assertEquals($user, $result['user']);
        $this->assertEquals($token, $result['token']);
    }

    public function test_login_successfully()
    {
        $credentials = ['email' => 'leo@example.com', 'password' => 'password'];
        $user = new User(['id' => 1, 'email' => 'leo@example.com']);
        $token = 'fake-jwt-token';

        $guard = Mockery::mock();
        $guard->shouldReceive('attempt')->once()->with($credentials)->andReturn($token);
        $guard->shouldReceive('user')->once()->andReturn($user);

        Auth::shouldReceive('guard')->with('api')->andReturn($guard);

        $result = $this->authService->login($credentials);

        $this->assertEquals($user, $result['user']);
        $this->assertEquals($token, $result['token']);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $credentials = ['email' => 'leo@example.com', 'password' => 'wrong-password'];

        $guard = Mockery::mock();
        $guard->shouldReceive('attempt')->once()->with($credentials)->andReturn(false);

        Auth::shouldReceive('guard')->with('api')->andReturn($guard);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->authService->login($credentials);
    }

    public function test_get_me_returns_authenticated_user()
    {
        $user = new User(['id' => 1, 'name' => 'leo messi']);
        
        $guard = Mockery::mock();
        $guard->shouldReceive('user')->once()->andReturn($user);
        
        Auth::shouldReceive('guard')->with('api')->andReturn($guard);

        $result = $this->authService->me();

        $this->assertEquals($user, $result);
    }

    public function test_logout_successfully()
    {
        $guard = Mockery::mock();
        $guard->shouldReceive('logout')->once();
        
        Auth::shouldReceive('guard')->with('api')->andReturn($guard);

        $this->authService->logout();
        
        $this->assertTrue(true); 
    }

    public function test_forgot_password_sends_link()
    {
        $data = ['email' => 'leo@example.com'];
        $status = Password::RESET_LINK_SENT;

        $broker = Mockery::mock();
        $broker->shouldReceive('sendResetLink')->once()->with($data)->andReturn($status);

        Password::shouldReceive('broker')->once()->andReturn($broker);

        $result = $this->authService->forgotPassword($data);

        $this->assertEquals(__($status), $result);
    }

    public function test_reset_password_successfully()
    {
        $data = [
            'token' => 'fake-token',
            'email' => 'leo@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password'
        ];
        $status = Password::PASSWORD_RESET;

        $broker = Mockery::mock();
        $broker->shouldReceive('reset')->once()->with($data, Mockery::type('Closure'))->andReturn($status);

        Password::shouldReceive('broker')->once()->andReturn($broker);

        $result = $this->authService->resetPassword($data);

        $this->assertEquals(__($status), $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
