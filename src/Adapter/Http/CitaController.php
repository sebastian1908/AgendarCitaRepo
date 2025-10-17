<?php
namespace Src\Adapter\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Application\Cita\AgendarCitaService;
use Src\Infrastructure\Persistence\EloquentCitaRepository;
use Src\Adapter\Http\Middleware\JWTAuthMiddleware;

class CitaController extends Controller {

    private AgendarCitaService $service;
    private JWTAuthMiddleware $jwtMiddleware;

    public function __construct() {
        $repo = new EloquentCitaRepository();
        $this->service = new AgendarCitaService($repo);
        $this->jwtMiddleware = new JWTAuthMiddleware();
    }

    public function agendar(Request $request) {
        return $this->jwtMiddleware->handle($request, function($req) {
            $username = $req->user->username;
            $cita = $this->service->agendar($req->all(), $username);

            return response()->json([
                'status' => 201,
                'message' => 'Cita agendada correctamente',
                'result' => [
                    'id' => $cita->getId(),
                    'title' => $cita->getTitle(),
                    'description' => $cita->getDescription(),
                    'date' => $cita->getDate(),
                    'user' => $cita->getUser()
                ]
            ], 201);
        });
    }

    public function listar(Request $request) {
        return $this->jwtMiddleware->handle($request, function($req) {
            $username = $req->user->username;
            $citas = $this->service->listarCitas($username);
            $result = array_map(fn($c) => [
                'id' => $c->getId(),
                'title' => $c->getTitle(),
                'description' => $c->getDescription(),
                'date' => $c->getDate()
            ], $citas);

            return response()->json([
                'status' => 200,
                'message' => 'Citas obtenidas correctamente',
                'result' => $result
            ], 200);
        });
    }
}
