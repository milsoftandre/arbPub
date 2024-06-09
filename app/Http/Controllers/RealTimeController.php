<?php

namespace App\Http\Controllers;

use App\Events\RealTimeEvent;
use App\Models\Bot;
use App\Models\TradeHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class RealTimeController extends Controller
{
    public function sendEvent(Request $request)
    {
        Log::info('Incoming Free data:', $request->all());

        // Получение данных из запроса
        $prices = $request->input('order_results');
        $result = $request->input('result');
        $bot_id = $request->input('bot_id');
        $deposit = $request->input('deposit');
        $thisBot = Bot::find($bot_id);

        if($thisBot->id){

            $saveTrade = new TradeHistory();
            $saveTrade->exchange_id = $thisBot->exchange_id;
            $saveTrade->bot_id = $thisBot->id;
            $saveTrade->client_id = $thisBot->client_id;
            $saveTrade->currency_pairs = $thisBot->currency_pairs;
            $saveTrade->predicted_profit_percent = $result;
            $saveTrade->amount = $deposit;
            $saveTrade->status = 1;
            $saveTrade->order_id = 0;


            $saveTrade->prices = json_encode($prices);
            $saveTrade->save();


            return ['true'];
        }
        return ['false'];
    }

    protected function sseResponse($callback)
    {
        // Создаем новый SSE-ответ
        $response = new StreamedResponse($callback);

        // Устанавливаем заголовки для SSE
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    public function listenForEvents(Request $request)
    {
// Путь к файлу
        $id = $request->input('id');
        $filePath = '/var/www/html/storage/app/bots/data'.$id.'.txt';

// Чтение данных из файла
        //$data = File::get($filePath);
        // Чтение строк из файла
// Чтение всего файла как одной строки
        if(file_exists($filePath)){
        $fileContent = File::get($filePath);

// Замена одинарных кавычек на двойные
        $fileContent = str_replace("'", '"', $fileContent);
// Разделение на строки
        $lines = explode("\n", $fileContent);
// Массив для хранения данных
        $data['message'] = '';

        foreach ($lines as $line) {
            // Преобразование JSON строки в массив
            $decodedLine = json_decode($line);
//dd($line);
if(@$decodedLine->prices) {
    $data['message'] .= "Received prices: " . json_encode(@$decodedLine->prices) . " | Result: " . @$decodedLine->result . " | ".@$decodedLine->des."\n";
}else {
    $data['message'] .= $line."\n";
}
        }
      //  dd($data);
// Удаление файла
         File::delete($filePath);
        }else {
            $data['message'] = '';
        }
        // Отправляем событие клиенту через SSE
        return $this->sseResponse(function () use ($data) {
            echo "data: " . json_encode($data) . "\n\n";
            ob_flush();
            flush();
        });
    }

    public function show()
    {
        $messages = ['Message 1', 'Message 2', 'Message 3'];
        return view('realtime.index', ['messages' => $messages]);
    }
}
