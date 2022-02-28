<?php

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/dialogrest.php');

const LOG_LAVEL = true; // записывать данные в лог - true/false

if (LOG_LAVEL) {
    /* Инициализация записи в файл лога */
    $path = __DIR__ . '/reqlogs/';
    if (!file_exists($path)) {
        @mkdir($path, 0775, true);
    }

    if (!file_exists($path . 'test_' . date("Y-m-d") . '.txt')) {
        file_put_contents($path . 'test_' . date("Y-m-d") . '.txt', 'Begin', true);
    }
}

switch(strtoupper($_REQUEST['event']))
{
    // сообщение при добавлении бота в чат
    case 'ONIMBOTJOINCHAT':
        $report = getAnswer("Привет");
        CRest::call(
            'imbot.message.add',
            [
                'DIALOG_ID' => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
                'MESSAGE' => $report['report'] . "\n"
            ]
        );

        break;

    // просто сообщение от бота
    case 'ONIMBOTMESSAGEADD':
        // response from our bot
        $report = getAnswer($_REQUEST['data']['PARAMS']['MESSAGE']);

        // send answer message
        $result = CRest::call(
            'imbot.message.add',
            [
                "DIALOG_ID" => $_REQUEST['data']['PARAMS']['DIALOG_ID'],
                "MESSAGE" => $report['report'] . "\n"
            ]
        );

        logging( var_export(['CRestResult' => $result], true), LOG_LAVEL);
        break;
}

function getAnswer($command = 'Привет')
{
    $report = DialogRest::getAnswer($command, $_REQUEST['data']['PARAMS']['DIALOG_ID']);
    $type = 'dialog'; // by default

    if(isset($report) && mb_stristr($_REQUEST['data']['PARAMS']['DIALOG_ID'], "chat")) {
        $report['response'] =  '[USER=' . $_REQUEST['data']['USER']['ID'] . ']' .
                               $_REQUEST['data']['USER']['NAME'] . '[/USER] ' . $report['response'];
        $type = 'chat';
    }

    logging($type, LOG_LAVEL);
    logging(var_export($_REQUEST, true), LOG_LAVEL);
    logging(var_export($report, true), LOG_LAVEL);

    return [
        'title' => '', //'You said: ',
        'report' => isset($report) ? $report['response'] : ''
    ];
}

function logging($text = '', $level = null) {

    if($level) {
        file_put_contents(__DIR__ . '/reqlogs/' . 'test_' . date("Y-m-d") . '.txt', date("Y-m-d H:i:s ") . "\n" . $text, FILE_APPEND);
        file_put_contents(__DIR__ . '/reqlogs/' . 'test_' . date("Y-m-d") . '.txt',  "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/reqlogs/' . 'test_' . date("Y-m-d") . '.txt',  "\n", FILE_APPEND);
    }
}
