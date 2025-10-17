<?php
namespace Src\Adapter\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Application\User\RegisterUserService;
use Src\Application\User\LoginService;
use Src\Application\User\CreateUserService;
use Src\Infrastructure\Persistence\EloquentUserRepository;
use Src\Infrastructure\Auth\JWTTokenService;

class UserController extends Controller {

    private RegisterUserService $registerService;
    private LoginService $loginService;
    private CreateUserService $createUserService;

    public function __construct() {
        $repo = new EloquentUserRepository();
        $jwt = new JWTTokenService();

        $this->registerService = new RegisterUserService($repo);
        $this->loginService = new LoginService($repo, $jwt);
        $this->createUserService = new CreateUserService($repo);
    }

    public function register(Request $request) {
        try {
            $user = $this->registerService->register($request->username, $request->password);
            return response()->json([
                'status' => 201,
                'message' => 'Usuario registrado correctamente',
                'result' => [
                    'user' => $user->getUsername(),
                    'role' => $user->getRole()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Error al registrar usuario',
                'result' => $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request) {
        try {
            $token = $this->loginService->login($request->username, $request->password);
            return response()->json([
                'status' => 200,
                'message' => 'Login exitoso',
                'result' => ['token' => $token]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 401,
                'message' => 'Credenciales inválidas',
                'result' => $e->getMessage()
            ], 401);
        }
    }

    public function createUser(Request $request) {
        try {
            $currentUser = $request->user;
            $user = $this->createUserService->createUser($request->all(), $currentUser);
            return response()->json([
                'status' => 201,
                'message' => 'Usuario creado correctamente',
                'result' => [
                    'user' => $user->getUsername(),
                    'role' => $user->getRole()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Error al crear usuario',
                'result' => $e->getMessage()
            ], 400);
        }
    }
}
