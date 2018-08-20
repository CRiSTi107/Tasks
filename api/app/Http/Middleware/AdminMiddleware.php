<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Closure;
use GenTux\Jwt\GetsJwtToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AdminMiddleware
 *
 * @package App\Http\Middleware
 */
class AdminMiddleware
{
    use GetsJwtToken;

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
            if (!$this->jwtToken()->validate()) {
                return $this->formatErrorMessage('Not authenticated!');
            }

            $token = $this->jwtToken();

            /** @var User $user */
            $user = User::where('id', $token->payload('id'))->where('email', $token->payload('context.email'))->first();

            if (!$user || $user->role_id !== Role::ROLE_ADMIN) {
                return $this->formatErrorMessage('You need to be admin to call this route!');
            }

            return $next($request);

        } catch (\Exception $e) {
            return $this->formatErrorMessage($e->getMessage());
        }
    }
}