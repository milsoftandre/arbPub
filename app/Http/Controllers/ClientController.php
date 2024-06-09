<?php

namespace App\Http\Controllers;

use App\Mail\Sendmail;
use App\Models\Apiip;
use App\Models\Employee;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {





        $settings = Employee::settings(2);
        $sq = $request->all();
        $type = 1;
        if (Auth::user()->type=='1'){
            return view('welcomeadmin');
            $rows = Employee::where('type',$type)->where('employees_id',(Auth::user()->employees_id)?Auth::user()->employees_id:Auth::user()->id)->where(function ($q) use ($sq,$settings) {
                foreach ($sq as $key => $value) {
                    if(in_array($key,$settings['table']) && $value){
                        $q->orWhere($key, 'like', "%{$value}%");
                    }
                }
            })->orderByRaw('id DESC')->paginate($settings['pl']);
        }else {
        $rows = Employee::where('type',$type)->where(function ($q) use ($sq,$settings) {
            foreach ($sq as $key => $value) {
                if(in_array($key,$settings['table']) && $value){
                    $q->orWhere($key, 'like', "%{$value}%");
                }
            }
        })->orderByRaw('id DESC')->paginate($settings['pl']);
        }
        return view('client.index', compact('rows', 'settings','request','type'));
    }

    public function generator()
    {
        $settings = Employee::settings(1,1);
        return view('client.generator', compact('settings'));
    }
    public function upbalance()
    {
        $user = Auth::user()->token;
        //$bal = (new \App\Models\Apiip())->checkBalance(explode(":",$user)[0])->data->balance;
        $model = \App\Models\Employee::find(Auth::user()->id);
        $model->update(['balance'=>0]);
        return 0;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settings = Employee::settings(1,1);
        return view('client.create', compact('settings'));
    }

    public function register()
    {
        $settings = Employee::settings(1,1);
        return view('client.register', compact('settings'));
    }

    public function rpwd()
    {
        $settings = Employee::settings(1,1);
        return view('client.rpwd', compact('settings'));
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
            'email' => 'required|unique:employees',
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],


        ]);
        $login = md5(time());
      //  $response = (new Apiip())->createCustomer($login,$login.'@mymail.com');


            $request->merge(['password' => bcrypt($request->password)]);


            $request->merge(['type' => 1]);


            $id = Employee::create($request->all());


        return redirect()->route('client.index')->with('success',__('client.delr'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = Employee::find($request->id)->token;
        //dd($user);
        $rez = (new Apiip())->getUserTransactions(explode(":",$user)[0])->data->stats;

        return view('client.show',compact('rez'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $settings = Employee::settings(1,1);
        $model = Employee::find($id);
        return view('client.edit',compact('model','settings'));
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
            'email' => 'required',

        ]);



        if (trim($request->get('password')) == '') {
            $data = $request->except('password');
        }else {
            $request->merge(['password' => bcrypt($request->password)]);
            $data = $request->all();
        }
        if (Auth::user()->type=='1'){
            $request->merge(['employees_id' => Auth::user()->id]);
        }else {
            if(!$request->password) { $request->merge(['employees_id' => 0]); }
        }
        $model = Employee::find($id);
      //  print_r($request); exit;
       $model->update($data);

        return redirect()->route('client.index')->with('success',__('client.upr'));
    }
    public function rstore(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $id = Employee::where('email',$request->email)->get()[0]->id;

        if($id){
        $model = Employee::find($id);
        $genPwd = rand(1111111,99999999);
        $data = ['password' => bcrypt($genPwd)];


            $details = [
                'title' => "New password",
                'body' => 'Your password: '.$data['password']
            ];
           // Mail::to($request->email)->send(new Sendmail($details));
// the message
            $msg = $details['body'];

// use wordwrap() if lines are longer than 70 characters
            $msg = wordwrap($msg,70);

// send email
            mail($request->email,"New password",$msg);

        $model->update($data);
            return redirect()->route('rpwd')->with('success','Your password has been changed. Check your mail.');
        }else {
            return redirect()->route('rpwd');
        }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Employee::find($id)->delete();

        return redirect()->route('client.index')
            ->with('success',__('client.dek'));
    }
}
