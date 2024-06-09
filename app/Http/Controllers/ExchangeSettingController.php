<?php

namespace App\Http\Controllers;

use App\BinanceService;
use App\Http\Controllers\Controller;
use App\Models\WalletBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExchangeSetting;


class ExchangeSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $settings = ExchangeSetting::settings();
        $sq = $request->all();

        if (Auth::user()->type=='1'){
            $rows = ExchangeSetting::where('client_id',Auth::user()->id)->where(function ($q) use ($sq, $settings) {
                foreach ($sq as $key => $value) {
                    if (in_array($key, $settings['table']) && $value && in_array($key, ['birthdate', 'name', 'date_of_death', 'country', 'death_country', 'profession'])) {
                        $q->where($key, 'like', "%{$value}%");
                    } elseif (in_array($key, $settings['table']) && $value) {
                        $q->where($key, '=', $value);
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }else {
            $rows = ExchangeSetting::where(function ($q) use ($sq, $settings) {
                foreach ($sq as $key => $value) {
                    if (in_array($key, $settings['table']) && $value && in_array($key, ['birthdate', 'name', 'date_of_death', 'country', 'death_country', 'profession'])) {
                        $q->where($key, 'like', "%{$value}%");
                    } elseif (in_array($key, $settings['table']) && $value) {
                        $q->where($key, '=', $value);
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }
        $status = ExchangeSetting::getStatus();
        return view('exchangesetting.index', compact('rows', 'settings','request', 'status'));
    }

    public function getBalance($id)
    {
        $thisAcc = ExchangeSetting::find($id);

        $getBal = new BinanceService($thisAcc->api_key,$thisAcc->secret_key);

        $rez = $getBal->getAccountBalances();
        //dd($rez);
        $commissionRates = $rez['commissionRates'];
        $thisAcc->purchase_commission = $commissionRates['maker'];
        $thisAcc->sale_commission = $commissionRates['taker'];
        $thisAcc->save();
        $dataArray=$rez['balances'];
        foreach ($dataArray as $data) {
            // Ищем запись в таблице wallet_balances для данного asset и exchange_settings_id
            $walletBalance = WalletBalance::where('asset', $data['asset'])
                ->where('exchange_settings_id', $id)
                ->first();

            if ($walletBalance) {
                // Если запись найдена, обновляем значения
                $walletBalance->update([
                    'free' => $data['free'],
                    'locked' => $data['locked'],
                ]);
            } else {
                // Если запись не найдена, создаем новую запись
                WalletBalance::create([
                    'exchange_settings_id' => $id,
                    'asset' => $data['asset'],
                    'free' => $data['free'],
                    'locked' => $data['locked'],
                ]);
            }
        }
       // dd(WalletBalance::where('exchange_settings_id', $id)->get());

        $rows = WalletBalance::where('exchange_settings_id',$id)->orderByRaw('free DESC')->get();

        return view('exchangesetting.bal', compact('rows'));
    }

    public function getBalancejson($id)
    {
        $thisAcc = ExchangeSetting::find($id);

        $getBal = new BinanceService($thisAcc->api_key,$thisAcc->secret_key);

        $rez = $getBal->getAccountBalances();
        $dataArray=$rez['balances'];
        foreach ($dataArray as $data) {
            // Ищем запись в таблице wallet_balances для данного asset и exchange_settings_id
            $walletBalance = WalletBalance::where('asset', $data['asset'])
                ->where('exchange_settings_id', $id)
                ->first();

            if ($walletBalance) {
                // Если запись найдена, обновляем значения
                $walletBalance->update([
                    'free' => $data['free'],
                    'locked' => $data['locked'],
                ]);
            } else {
                // Если запись не найдена, создаем новую запись
                WalletBalance::create([
                    'exchange_settings_id' => $id,
                    'asset' => $data['asset'],
                    'free' => $data['free'],
                    'locked' => $data['locked'],
                ]);
            }
        }
        // dd(WalletBalance::where('exchange_settings_id', $id)->get());

        $rows = WalletBalance::where('exchange_settings_id',$id)->whereRaw("free>0 or locked>0")->orderByRaw('free DESC')->get();

        return response()->json($rows);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response mv /var/www/html/1tra /var/www/html
     */
    public function create()
    {
        if (Auth::user()->type=='1') {
          //  return view('welcomeadmin');
        }
        $settings = ExchangeSetting::settings();
        return view('exchangesetting.create', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->type=='1') {
          //  return view('welcomeadmin');
           
            $request->merge(['client_id' => Auth::user()->id]);
        }
        $request->validate([
            'exchange_id' => 'required', 'client_id' => 'required', 'api_key' => 'required', 'secret_key' => 'required'

        ]);
        $request->merge(['password' => bcrypt($request->password)]);
        $request->merge(['purchase_commission' => '']);
        $request->merge(['sale_commission' => '']);

        ExchangeSetting::create($request->all());

        return redirect()->route('exchange-settings.index')->with('success',__('exchangesetting.delr'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Auth::user()->type=='1') {
          //  return view('welcomeadmin');
        }
        $settings = ExchangeSetting::settings();
        $model = ExchangeSetting::find($id);
        return view('exchangesetting.edit',compact('model','settings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->type=='1') {
          //  return view('welcomeadmin');
        }
        $settings = ExchangeSetting::settings();
        $model = ExchangeSetting::find($id);
        return view('exchangesetting.edit',compact('model','settings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        if (Auth::user()->type=='1') {
          //  return view('welcomeadmin');
            $request->merge(['client_id' => Auth::user()->id]);

        }
        $request->validate([
            'exchange_id' => 'required', 'client_id' => 'required', 'api_key' => 'required', 'secret_key' => 'required'

        ]);


        $model = ExchangeSetting::find($id);
        $model->update($request->all());

        return redirect()->route('exchange-settings.index')->with('success',__('exchangesetting.upr'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->type=='1') {
         //   return view('welcomeadmin');
        }

        ExchangeSetting::find($id)->delete();

        return redirect()->route('exchange-settings.index')
            ->with('success',__('exchangesetting.dek'));
    }
}
