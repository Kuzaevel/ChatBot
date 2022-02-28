<?php

class DialogRest
{
    const URL = "http://192.168.88.24:8102/cores/"; //http://192.168.88.24:8102/
    const CORE_ID = "61e6b8e5137ec1b6bd167f5e"; //https://ds.promo-bot.ru/cores/61e6b8e5137ec1b6bd167f5e

    protected static function callCurl($action, $arParams)
    {
        $ch = curl_init(static::URL.static::CORE_ID."/".$action);
        $arParams = json_encode($arParams);
        //curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arParams);
        $result = curl_exec($ch);
        //$cod = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return json_decode($result, true);
    }

    public static function getAnswer($query, $dialog_id)
    {
        //$sessionID = static::getSessionId($dialog_id);
        $array = [
            "query" => $query,
            //"tol" => 0,
            "session_id" => $dialog_id,
        ];
        $return = static::callCurl("response", $array);
//        if ($return && array_key_exists("session_id",$return) && !empty($return["session_id"])) {
//            static::setSessionId($dialog_id, $return["session_id"]);
//        }
        //$return["response"]=$return;
        return $return;
    }

    public static function addLog($query, $user_id, $user_name, $arrayResponse)
    {
        $array = [
            "user_id" => $user_id,
            "user_name" => $user_name,
            "query" => $query,
            "response" => $arrayResponse["response"],
            "confidence" => $arrayResponse["confidence"],
            "delay" => $arrayResponse["delay"],
        ];
        return true; //static::callCurl("logs/add", $array);
    }

    protected static function getSessionId($dialog_id)
    {
        $path = __DIR__ . '/sessions/dialog_' . $dialog_id . '.txt';
        if(file_exists($path)) {
            $return = file_get_contents($path);
        } else {
            $return = 0;
        }
        return $return;
    }

    protected static function setSessionId($dialog_id, $sessionId)
    {
        $dir = __DIR__ . '/sessions/';
        if (!file_exists($dir))
        {
            @mkdir($dir, 0775, true);
        }
        $path = $dir . 'dialog_' . $dialog_id . '.txt';
        $return = file_put_contents($path, $sessionId);
        return true;
    }

}
