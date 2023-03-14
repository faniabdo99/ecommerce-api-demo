<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $response (mixed) The response to be returned to the frontend
     * @param $success (bool) Was the request a success?
     * @param $status (int) The response status code
     * @return Application|ResponseFactory|Response
     */
    protected function api_response($response, bool $success, int $status = 200){
        return response([
            'success' => $success,
            'status' => $status,
            'response' => $response
        ], $status);
    }
}
