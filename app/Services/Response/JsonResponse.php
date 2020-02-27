<?php


namespace App\Services\Response;


class JsonResponse extends \Illuminate\Http\JsonResponse
{
    public function __construct($data = null, int $status = 200, array $headers = [], int $options = 0)
    {
        if ($data instanceof JsonRespondent) {
            $data = $data->response();
        }
        parent::__construct($data, $status, $headers, $options);
    }
}