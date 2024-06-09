<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\CurrencyPairController;
use App\Http\Controllers\ExchangeSettingController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\TradeHistoryController;
use App\Http\Controllers\RealTimeController;
use App\Http\Controllers\BinanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/get-balances', [BinanceController::class, 'getBalances']);

Route::get('/get-price', [BinanceController::class, 'getPrice']);

Route::post('/make-order', [BinanceController::class, 'makeOrder'])->middleware('auth')->name('makeorder');

//Route::get('/get-balancesjson', [BinanceController::class, 'getBalancesjson']);

Route::get('/updatestart', function () {
    $thisBot = \App\Models\Bot::where('status',0)->get();
    //dd($thisBot);
    foreach ($thisBot as $item){
        $id=$item->id;
        exec("pkill -f 'python3 /var/www/html/storage/app/bots/bot".$id.".py'", $output, $exitCode);
        exec('/usr/bin/python3 /var/www/html/storage/app/bots/v2bot'.$id.'.py > /dev/null 2>&1 & echo $!', $output, $exitCode);


    }
dd($output);
})->middleware('guest')
    ->name('updatestart');

Route::get('/run', function (Request $request) {
    $id = $request->id;
    $type = '';

    if($request->type){
        $type = 'v2';
    }
    exec("/usr/bin/python3 /var/www/html/storage/app/bots/test".$type."bot".$id.".py  2>&1", $output, $exitCode);
    // Вывод результата выполнения скрипта
    echo "Output: " . implode("<br>", $output) . "<br>";
    echo "Exit Code: " . $exitCode;
})
    ->name('runnn');

Route::get('/getOpenOrders', function () {
    $thisAcc = \App\Models\ExchangeSetting::find(1);

    $getBal = new \App\BinanceService($thisAcc->api_key,$thisAcc->secret_key);

    $rez = $getBal->getAccountBalances();
    dd($rez);

    $thisAcc = \App\Models\ExchangeSetting::find(1);

    $getBal = new \App\BinanceService($thisAcc->api_key,$thisAcc->secret_key);

    $rez = $getBal->getOrderById('663894178');
    dd($rez);
});
Route::get('/run-script', function (Request $request) {
    $output = [];
    $exitCode = 0;

    $id = $request->id;

    $type = '';

    if($request->type){
        $type = 'v2';
    }

    $thisBot = \App\Models\Bot::find($id);

    exec('/usr/bin/python3 /var/www/html/storage/app/bots/'.$type.'bot'.$id.'.py > /dev/null 2>&1 & echo $!', $output, $exitCode);
    $pid = trim($output[0]); // PID будет в $pid

    //\Illuminate\Support\Facades\DB::table('bots')->update(['status' => 0]);
    $thisBot->status = 0;
    $thisBot->save();
    #return redirect()->route('bot.index');
    if ($exitCode === 0) {
        return response()->json(['status' => 'success', 'output' => $output]);
    } else {
        return response()->json(['status' => 'error', 'output' => $output, 'pid' => $pid], 500);
    }
})->name('botstart');

Route::get('/stop-script', function (Request $request) {

    $id = $request->id;

    $thisBot = \App\Models\Bot::find($id);
    $type = '';

    if($request->type){
        $type = 'v2';
    }
    // Пытаемся завершить процесс с использованием команды pkill
    exec("pkill -f 'python3 /var/www/html/storage/app/bots/".$type."bot".$id.".py'", $output, $exitCode);
    $thisBot->status = 1;
    $thisBot->save();


    if ($exitCode === 0) {
        return response()->json(['status' => 'success', 'message' => 'Script stopped']);
    } else {
        return response()->json(['status' => 'error', 'message' => $exitCode]);
    }
})->name('botstop');

Route::get('/data/{botID}.txt', function ($botID) {
    $filePath = '/bots/data' . $botID . '.txt';

    if (Storage::exists($filePath)) {
        $stream = Storage::readStream($filePath);

        return new \Symfony\Component\HttpFoundation\StreamedResponse(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            200,
            [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            ]
        );
    } else {
        return response('Log not found', 404)->header('Content-Type', 'text/plain');
    }
});

Route::get('/datadel/{botID}.txt', function ($botID) {
    $filePath = '/bots/data' . $botID . '.txt';

    if (Storage::exists($filePath)) {
        Storage::delete($filePath);
        return response('Log deleted', 200)->header('Content-Type', 'text/plain');
    } else {
        return response('Log not found', 404)->header('Content-Type', 'text/plain');
    }
});

Route::post('/send-event', [RealTimeController::class, 'sendEvent']);
Route::get('/show', [RealTimeController::class, 'show']);
Route::get('/real-time-listener', [RealTimeController::class, 'listenForEvents']);

Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['en', 'ru', 'fr'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    App::setLocale($locale);

    return view('welcome');
})->name('lang');

Route::get('/', function () {
   // dd(Auth::user());

    //return redirect(route('orders.index'));
if(@Auth::user()->id){
    return view('welcomeadmin');
}else {
    return view('welcome');
}

})->name('dashboard')->middleware('auth');

Route::post('/paysave', [FinanceController::class, 'paysave'])
    ->middleware('guest')
    ->name('paysave');


Route::post('/rstore', [ClientController::class, 'rstore'])
    ->middleware('guest')
    ->name('rstore');

Route::post('/registerstore', [ClientController::class, 'store'])
    ->middleware('guest')
    ->name('registerstore');

Route::resource('settings', SettingsController::class)->middleware('auth');

Route::resource('employee', EmployeeController::class)->middleware('auth');


Route::resource('client', ClientController::class)->middleware('auth');

Route::get('clientshow', [ClientController::class,'show'])->middleware('auth')->name('clientshow');
Route::get('generator', [ClientController::class,'generator'])->middleware('auth')->name('generator');
Route::get('upbalance', [ClientController::class,'upbalance'])->middleware('auth')->name('upbalance');

Route::get('/pay', [FinanceController::class, 'pay'])
    ->middleware('auth')
    ->name('pay');

Route::resource('finance', FinanceController::class)->middleware('auth');
Route::resource('exchange', ExchangeController::class)->middleware('auth');
Route::resource('currency-pairs', CurrencyPairController::class)->middleware('auth');
Route::resource('exchange-settings', ExchangeSettingController::class)->middleware('auth');
Route::resource('bot', BotController::class)->middleware('auth');
Route::resource('trade-history', TradeHistoryController::class)->middleware('auth');

Route::get('/load-data', [CurrencyPairController::class, 'getPair'])->name('loaddata');


Route::get('profile', [EmployeeController::class,'profile'])->middleware('auth')->name('profile');
Route::put('profileupdate', [EmployeeController::class,'profileupdate'])->middleware('auth')->name('profileupdate');

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('custom-login', [LoginController::class, 'customLogin'])->name('login.custom');
Route::get('/logout', [LoginController::class, 'perform'])->name('logout');


Route::get('/register', [ClientController::class, 'register'])
    ->middleware('guest')
    ->name('register');

Route::get('/rpwd', [ClientController::class, 'rpwd'])
    ->middleware('guest')
    ->name('rpwd');

Route::get('/telegram',  [TelegramController::class, 'handleWebhook'])
    ->name('ddd2');

Route::post('/telegram/webhook',  [TelegramController::class, 'handleWebhook'])->middleware('guest')
    ->name('ddd');

Route::get('/get-balance/{id}', [ExchangeSettingController::class, 'getBalance'])->middleware('auth')
    ->name('get-balance');

Route::get('/get-balancejson/{id}', [ExchangeSettingController::class, 'getBalancejson'])->middleware('auth')
    ->name('get-balancejson');