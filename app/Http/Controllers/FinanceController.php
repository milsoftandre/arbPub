<?php

namespace App\Http\Controllers;

use App\Models\Apiip;
use App\Models\Employee;
use App\Models\Finance;
use App\Models\Plans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $settings = Finance::settings(2);
        $sq = $request->all();
        if(Auth::user()->type==0){
        $rows = Finance::where(function ($q) use ($sq,$settings) {
            foreach ($sq as $key => $value) {
                if(in_array($key,$settings['table']) && $value){
                    $q->orWhere($key, 'like', "%{$value}%");
                }
                if($key=='date_from' && $value){
                    $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($value)));
                }

                if($key=='date_to' && $value){
                    $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($value)));
                }
            }
        })->orderByRaw('id DESC')->paginate($settings['pl']);
        }else {
            $rows = Finance::where('employees_id',Auth::user()->id)->where(function ($q) use ($sq,$settings) {
                foreach ($sq as $key => $value) {
                    if(in_array($key,$settings['table']) && $value){
                        $q->orWhere($key, 'like', "%{$value}%");
                    }
                    if($key=='date_from' && $value){
                        $q->whereDate('created_at', '>=', date('Y-m-d', strtotime($value)));
                    }

                    if($key=='date_to' && $value){
                        $q->whereDate('created_at', '<=', date('Y-m-d', strtotime($value)));
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }

        return view('finance.index', compact('rows', 'settings','request'));
    }

    public function pay(Request $request)
    {
       // dd($request->order_id);
        if($request->order_id){
            $pans = Plans::find(explode("-",$request->order_id)[1]);
            //dd($pans);
            return Redirect::to((new \App\Models\Apipay)->createLink($pans->price,$request->order_id));

        }
        $settings = Finance::settings(1);
        return view('finance.pay', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settings = Finance::settings(1);
        return view('finance.create', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'employees_id' => 'required'
        ]);




        if($request->employees_id){
            if($request->type==0){
                $model = Employee::find($request->employees_id);

                $r = (new Apiip())->addBalance(explode(":",$model->token)[0],$request->price);
//dd($r);
                if($r->status==200) {
                    $model->update([
                        'balance' => ($model->balance + $request->price)
                    ]);
                    Finance::create($request->all());
                }else {
                    return redirect()->route('finance.index')->with('success',$r->status);

                }
               // dd($r);
            }else {
                $model = Employee::find($request->employees_id);
                $r = (new Apiip())->removeBalance(explode(":",$model->token)[0],$request->price);
                if($r->status==200) {

                    $model->update([
                        'balance' => ($model->balance - $request->price)
                    ]);
                    Finance::create($request->all());

                }else {
                        return redirect()->route('finance.index')->with('success',$r->status);

                    }

            }
        }
        return redirect()->route('finance.index')->with('success',__('finance.delr'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $settings = Finance::settings();
        $model = Finance::find($id);
        return view('finance.edit',compact('model','settings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $settings = Finance::settings(1);
        $model = Finance::find($id);
        return view('finance.edit',compact('model','settings'));
    }


    public function paysave(Request $request)
    {

        Log::info($request->other);
//        $input = file_get_contents('php://input');
//        $decode = json_decode($input, 1);

        $order_id = explode("-",$request->other['order_id'])[0];
        $plan_id = explode("-",$request->other['order_id'])[1];
        $token = $request->token;
        $r = 'qN26LAKyo6MacHHQTOJjjn3tUR090i5fHqTS';

        if (in_array($_SERVER['REMOTE_ADDR'], ['92.255.107.43', '185.119.56.59', '185.178.44.137', '185.178.46.40', '185.178.47.148', '185.178.47.161'])){
            $plan = Plans::find($plan_id);

            $model = Employee::find($order_id);

            $r = (new Apiip())->addBalance(explode(":",$model->token)[0],$plan->mb);


            if($r->status->code==200) {
                $model->update([
                    'balance' => ($model->balance + $plan->mb)
                ]);
                Finance::create([
                    'name' => "Pay by crypto",
                    'price' => $plan->mb,
                    'employees_id' => $order_id
                ]);
            }else {

                return ['status'=>$r->status->message];
            }
            return ['status'=>'ok'];
        }else {
            return ['status'=>'Wrong token'];
        }


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
        $request->validate([
            'name' => 'required',
            'price' => 'required',
        ]);


        $model = Finance::find($id);
        $model->update($request->all());

        return redirect()->route('finance.index')->with('success',__('finance.upr'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Finance::find($id)->delete();

        return redirect()->route('finance.index')
            ->with('success',__('finance.dek'));
    }
}
