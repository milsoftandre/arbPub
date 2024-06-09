<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->type=='1') {
            return view('welcomeadmin');
        }
        $settings = Exchange::settings();
        $sq = $request->all();
        $rows = Exchange::where(function ($q) use ($sq,$settings) {
            foreach ($sq as $key => $value) {
                if(in_array($key,$settings['table']) && $value && in_array($key,['birthdate','name','date_of_death','country','death_country','profession'])){
                    $q->where($key, 'like', "%{$value}%");
                }elseif(in_array($key,$settings['table']) && $value){
                    $q->where($key, '=', $value);
                }
            }
        })->orderByRaw('id DESC')->paginate($settings['pl']);

        return view('exchange.index', compact('rows', 'settings','request'));
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
        $settings = Exchange::settings();
        return view('exchange.create', compact('settings'));
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

        Exchange::create($request->all());

        return redirect()->route('exchange.index')->with('success',__('exchange.delr'));
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
        $settings = Exchange::settings();
        $model = Exchange::find($id);
        return view('exchange.edit',compact('model','settings'));
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
        $settings = Exchange::settings();
        $model = Exchange::find($id);
        return view('exchange.edit',compact('model','settings'));
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


        $model = Exchange::find($id);
        $model->update($request->all());

        return redirect()->route('exchange.index')->with('success',__('exchange.upr'));
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

        Exchange::find($id)->delete();

        return redirect()->route('exchange.index')
            ->with('success',__('exchange.dek'));
    }
}
