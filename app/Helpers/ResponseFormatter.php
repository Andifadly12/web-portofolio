<?php
namespace App\Helpers;

class ResponseFormatter{
    protected static $respose=[
        'meta'=>[
            'code'=>200,
            'status'=>'success',
            'message'=>null
        ],
        'date'=>null
    ];
    public static function success($data=null, $message=null){
        self::$respose['meta']['message']=$message;
        self::$respose['data']=$data;
        return response()->json(self::$respose, self::$respose['meta']['code']);
    }
    public static function error($data=null, $message=null, $code=400){
        self::$respose['meta']['status']='error';
        self::$respose['meta']['code']=$code;
        self::$respose['meta']['message']=$message;
        self::$respose['data']=$data;
        return response()->json(self::$respose, self::$respose['meta']['code']);
    }
}
?>