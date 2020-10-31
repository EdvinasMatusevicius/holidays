<?php

    namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    protected $status = null;

    //status 200
    public function success($message = null)
    {
        $response = $this->base();
        if($message){
            $response['message']=$message;
        }
        return response()->json($response,JsonResponse::HTTP_OK);
    }

    //status 400
    public function exception($message = null): JsonResponse
    {
        $response = $this->setStatus(JsonResponse::HTTP_BAD_REQUEST)->base();
        $response['message'] = $message ?? 'Something went wrong';

        return response()->json($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    // set status method that allows chaining
    protected function setStatus($status){
        $this->status = $status;
        return $this;
    }

    //default response if no message
    protected function base(): array
    {
        return [
            'status'=>$this->status ?? JsonResponse::HTTP_OK,
            'message'=>''
        ];
    }
}