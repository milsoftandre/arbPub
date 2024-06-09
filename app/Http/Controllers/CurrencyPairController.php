<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TradingPairParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CurrencyPair;

class CurrencyPairController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        $parser = new TradingPairParser();
//        $result = $parser->parseAndStoreTradingPairs();
     //   dd($result);
        if (Auth::user()->type=='1') {
            return view('welcomeadmin');
        }
        $settings = CurrencyPair::settings();
        $sq = $request->all();
        $rows = CurrencyPair::where(function ($q) use ($sq,$settings) {
            foreach ($sq as $key => $value) {
                if(in_array($key,$settings['table']) && $value && in_array($key,['birthdate','name','date_of_death','country','death_country','profession'])){
                    $q->where($key, 'like', "%{$value}%");
                }elseif(in_array($key,$settings['table']) && $value){
                    $q->where($key, '=', $value);
                }
            }
        })->orderByRaw('id DESC')->paginate($settings['pl']);

        return view('currencypair.index', compact('rows', 'settings','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response mv /var/www/html/1tra /var/www/html
     */
    public function create()
    {
        if (Auth::user()->type=='1') {
            return view('welcomeadmin');
        }
        $settings = CurrencyPair::settings();
        return view('currencypair.create', compact('settings'));
    }

    public function getPair(Request $request)
    {
            $id = $request->id;
       // $thisPair = CurrencyPair::find($id);
        return response()->json(CurrencyPair::selectRaw("
    CASE
        WHEN buy_currency = '{$id}' THEN CONCAT(buy_currency, ' → ', sell_currency)
        WHEN sell_currency = '{$id}' THEN CONCAT(sell_currency, ' → ', buy_currency)
        ELSE NULL
    END as name,
    id
")
            ->where('status', 'TRADING')
            ->whereRaw("(buy_currency='".$id."' or sell_currency='".$id."') and id!='".$id."'")
            ->get()
            ->pluck('name', 'id')
            ->toArray());
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
            return view('welcomeadmin');
        }
        $request->validate([
            'name' => 'required',


        ]);
        $request->merge(['password' => bcrypt($request->password)]);

        CurrencyPair::create($request->all());

        return redirect()->route('currency-pairs.index')->with('success',__('currencypair.delr'));
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
            return view('welcomeadmin');
        }
        $settings = CurrencyPair::settings();
        $model = CurrencyPair::find($id);
        return view('currencypair.edit',compact('model','settings'));
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
            return view('welcomeadmin');
        }
        $settings = CurrencyPair::settings();
        $model = CurrencyPair::find($id);
        return view('currencypair.edit',compact('model','settings'));
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
            return view('welcomeadmin');
        }
        $request->validate([
            'name' => 'required',

        ]);


        $model = CurrencyPair::find($id);
        $model->update($request->all());

        return redirect()->route('currency-pairs.index')->with('success',__('currencypair.upr'));
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
            return view('welcomeadmin');
        }

        CurrencyPair::find($id)->delete();

        return redirect()->route('currency-pairs.index')
            ->with('success',__('currencypair.dek'));
    }
}
