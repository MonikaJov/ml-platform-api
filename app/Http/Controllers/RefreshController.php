<?php

namespace App\Http\Controllers;

use App\Actions\Refresh;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class RefreshController extends Controller
{
    /**
     * api.admin.auth.refresh
     */
    public function __invoke(Request $request): AuthResource|JsonResponse
    {
        try {
            return Refresh::run($request->refresh_token);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => __('Invalid token.')], 400);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => (__('Token has expired.'))], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
