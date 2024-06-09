<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bot;

class BotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->type=='1') {
           // return view('welcomeadmin');
        }
        $settings = Bot::settings();
        $sq = $request->all();

        if (Auth::user()->type=='1'){
            $rows = Bot::where('client_id',Auth::user()->id)->where(function ($q) use ($sq, $settings) {
                foreach ($sq as $key => $value) {
                    if (in_array($key, $settings['table']) && $value && in_array($key, ['birthdate', 'name', 'date_of_death', 'country', 'death_country', 'profession'])) {
                        $q->where($key, 'like', "%{$value}%");
                    } elseif (in_array($key, $settings['table']) && $value) {
                        $q->where($key, '=', $value);
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }else {
            $rows = Bot::where(function ($q) use ($sq, $settings) {
                foreach ($sq as $key => $value) {
                    if (in_array($key, $settings['table']) && $value && in_array($key, ['birthdate', 'name', 'date_of_death', 'country', 'death_country', 'profession'])) {
                        $q->where($key, 'like', "%{$value}%");
                    } elseif (in_array($key, $settings['table']) && $value) {
                        $q->where($key, '=', $value);
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }
        $status = Bot::getStatus();
        return view('bot.index', compact('rows', 'settings','request', 'status'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response mv /var/www/html/1tra /var/www/html
     */
    public function create()
    {
        if (Auth::user()->type=='1') {
           // return view('welcomeadmin');
        }
        $settings = Bot::settings();
        return view('bot.create', compact('settings'));
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
            $request->status=0;
        }
      //  $pairsJson = str_replace(" → ",'":"',$request->pairsJson);
        $pairsJson = str_replace("[",'{',$request->pairsJson);
        $pairsJson = $request->pairsJson;

        $request->merge(['min_profit_percent' => str_replace(",",".",$request->min_profit_percent)]);
        $request->merge(['min_amount' => str_replace(",",".",$request->min_amount)]);

        $request->validate([
            'name' => 'required',
            'min_profit_percent'=> 'required',
            'min_amount' => 'required'
        ]);




        $request->merge(['currency_pairs' => $pairsJson]);

        Bot::create($request->all());

        return redirect()->route('bot.index')->with('success',__('bot.delr'));
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
           // return view('welcomeadmin');
        }
        $settings = Bot::settings();
        $model = Bot::find($id);
        return view('bot.edit',compact('model','settings'));
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
           // return view('welcomeadmin');
        }
        $settings = Bot::settings();
        $model = Bot::find($id);
        return view('bot.edit',compact('model','settings'));
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
        $request->merge(['min_profit_percent' => str_replace(",",".",$request->min_profit_percent)]);
      //  dd($request->min_profit_percent);
        $request->merge(['min_amount' => str_replace(",",".",$request->min_amount)]);
        //$pairsJson = str_replace(" → ",'":"',$request->pairsJson);
//        $pairsJson = str_replace("[",'{',$pairsJson);
//        $pairsJson = str_replace("]",'}',$pairsJson);
        $pairsJson = $request->pairsJson;
        $request->validate([
            'name' => 'required',
            'min_profit_percent'=> 'required',
            'min_amount' => 'required'
        ]);

        $request->merge(['currency_pairs' => $pairsJson]);

        $model = Bot::find($id);
        $model->update($request->all());

        return redirect()->route('bot.index')->with('success',__('bot.upr'));
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
          //  return view('welcomeadmin');
        }

        Bot::find($id)->delete();

        return redirect()->route('bot.index')
            ->with('success',__('bot.dek'));
    }

    public function botstop()
    {


        return redirect()->route('bot.index')
            ->with('success',__('bot.dek'));
    }
}
