<?php

/**
 * Une classe utilitaire pour la gestion des réponses API*/

namespace App\Helpers;

use App\Utils\RestServiceStatusCode;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;


class ApiResponse{

    const SUCCESS_MESSAGE='Operation Successful';

    private static int $statusCode=0;
    private static string $statusMessage="";
    private static $data;
    private static array $metaData=[];

    public static function setContextStatusCode(int $statusCode){
        self::$statusCode=$statusCode;
        return new self;
    }

    public static function setStatusMessage(string $message){
        self::$statusMessage=$message;
        return new self;

    }

    public static function setData($data){
        self::$data=$data;
        return new self;

    }

    public static function setHeader(array $header){
        self::$metaData=$header;
    }

    public static function sendToClient(int $httpStatus=200){
        return response()->json([
            'status_code' => self::$statusCode,
            'status_message' => self::$statusMessage,
            'data' => self::$data
        ],$httpStatus)->header('Content-Type', 'application/json');
    }



    public static function respond($message="",int $contextStatusCode=RestServiceStatusCode::SUCCESS_OPERATION,int $httpStatus=200,$data=""){

        $jsonBody=[
            'status_code' => $contextStatusCode,
            'status_message' =>  $message,
            'data' => $data,
        ];
        if(!empty(self::$metaData)){
            $jsonBody['meta_data'] = self::$metaData;
        }
        return response()->json($jsonBody,$httpStatus,self::$metaData)->header('Content-Type', 'application/json');
    }

    public static function autoRespond(int $contextStatusCode=RestServiceStatusCode::SUCCESS_OPERATION,int $httpStatus=200,$data=null):JsonResponse{
        $jsonBody=[
            'status_code' => $contextStatusCode,
            'status_message' =>  Messages::$message[$contextStatusCode] ?? 'Une erreur s\'est produite',
            'data' => $data,
        ];

        if(!empty(self::$metaData)){
            $jsonBody['meta_data'] = self::$metaData;
        }

        return response()->json($jsonBody,$httpStatus)->header('Content-Type', 'application/json');
    }

    public static function redirect(string $redirect_url,$message=""){
        return response()->json([
            'status_code' => RestServiceStatusCode::SEE_OTHER_REDIRECT,
            'redirect_url'=>$redirect_url,
            'status_message' => $message,
        ],HTTPResponse::HTTP_OK)->header('Content-Type', 'application/json');
    }

    public static function setMetaData(array $metaData){
        self::$metaData=$metaData;
        return new self;
    }

    public static function tellCriticalError(){
        return self::respond('An unknown error has occurred.',RestServiceStatusCode::SERVER_ERROR,HTTPResponse::HTTP_INTERNAL_SERVER_ERROR);
    }


    public static function success($data=null){
        return self::autoRespond(RestServiceStatusCode::SUCCESS_OPERATION,200,$data);
    }

    public static function error($message="",int $contextStatusCode=RestServiceStatusCode::SERVER_ERROR,int $httpStatus=400){
        return self::respond($message,$contextStatusCode,$httpStatus);
    }
}
