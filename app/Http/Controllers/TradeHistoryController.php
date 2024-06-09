<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TradeHistory;
class TradeHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->type=='1') {
         //   return view('welcomeadmin');
        }
        $settings = TradeHistory::settings();
        $sq = $request->all();

        if (Auth::user()->type=='1'){
            $rows = TradeHistory::where('client_id',Auth::user()->id)->where(function ($q) use ($sq, $settings) {
                foreach ($sq as $key => $value) {
                    if (in_array($key, $settings['table']) && $value && in_array($key, ['birthdate', 'name', 'date_of_death', 'country', 'death_country', 'profession'])) {
                        $q->where($key, 'like', "%{$value}%");
                    } elseif (in_array($key, $settings['table']) && $value) {
                        $q->where($key, '=', $value);
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }else {
            $rows = TradeHistory::where(function ($q) use ($sq, $settings) {
                foreach ($sq as $key => $value) {
                    if (in_array($key, $settings['table']) && $value && in_array($key, ['birthdate', 'name', 'date_of_death', 'country', 'death_country', 'profession'])) {
                        $q->where($key, 'like', "%{$value}%");
                    } elseif (in_array($key, $settings['table']) && $value) {
                        $q->where($key, '=', $value);
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }
        $status = TradeHistory::getStatus();
        return view('tradehistory.index', compact('rows', 'settings','request','status'));
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
        $settings = TradeHistory::settings();
        return view('tradehistory.create', compact('settings'));
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
            'name' => 'required',
        ]);
        $request->merge(['password' => bcrypt($request->password)]);

        TradeHistory::create($request->all());

        return redirect()->route('trade-history.index')->with('success',__('tradehistory.delr'));
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
        $settings = TradeHistory::settings();
        $model = TradeHistory::find($id);
        return view('tradehistory.edit',compact('model','settings'));
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
        $settings = TradeHistory::settings();
        $model = TradeHistory::find($id);
        return view('tradehistory.edit',compact('model','settings'));
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
           // return view('welcomeadmin');
            $request->merge(['client_id' => Auth::user()->id]);
        }
        $request->validate([
            'name' => 'required',

        ]);


        $model = TradeHistory::find($id);
        $model->update($request->all());

        return redirect()->route('trade-history.index')->with('success',__('tradehistory.upr'));
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
           // return view('welcomeadmin');
        }

        TradeHistory::find($id)->delete();

        return redirect()->route('trade-history.index')
            ->with('success',__('tradehistory.dek'));
    }
}
