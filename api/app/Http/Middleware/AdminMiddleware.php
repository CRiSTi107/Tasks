<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Closure;
use GenTux\Jwt\Drivers\JwtDriverInterface;
use GenTux\Jwt\GetsJwtToken;
use GenTux\Jwt\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AdminMiddleware
 *
 * @package App\Http\Middleware
 */
class AdminMiddleware
{
    /**
     * Format error message
     *
     * @param $error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function formatErrorMessage($error)
    {
        $response = [
            'responseType' => Controller::RESPONSE_ERROR,
            'data' => null,
            'errorMessage' => $error
        ];

        $statusCode = Response::HTTP_OK;

        return response()->json($response, $statusCode);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $jwtToken = new JwtToken(app(JwtDriverInterface::class));
            $jwtToken->setToken($request->bearerToken());

            if (!$jwtToken->validate()) {
                return $this->formatErrorMessage('Not authenticated!');
            }

            /** @var User $user */
            $user = User::where('id', $jwtToken->payload('id'))->where('email', $jwtToken->payload('context.email'))->first();

            if (!$user || $user->role_id !== Role::ROLE_ADMIN) {
                return $this->formatErrorMessage('You need to be admin to call this route!');
            }

            return $next($request);

        } catch (\Exception $e) {
            return $this->formatErrorMessage($e->getMessage());
        }
    }
}