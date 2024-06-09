<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
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
        $type = 0;
        $rows = Employee::where('type',$type)->where(function ($q) use ($sq,$settings) {
            foreach ($sq as $key => $value) {
                if(in_array($key,$settings['table']) && $value){
                    $q->orWhere($key, 'like', "%{$value}%");
                }
            }
        })->orderByRaw('id DESC')->paginate($settings['pl']);

        return view('employee.index', compact('rows', 'settings','request','type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $settings = Employee::settings(1);
        return view('employee.create', compact('settings'));
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
            'email' => 'required',
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $request->merge(['password' => bcrypt($request->password)]);
        //$request->merge(['token' => md5(time())]);

         Employee::create($request->all());

        return redirect()->route('employee.index')->with('success','Запись успешно добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $settings = Employee::settings();
        $model = Employee::find($id);
        return view('employee.edit',compact('model','settings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $settings = Employee::settings(1);
        $model = Employee::find($id);
        return view('employee.edit',compact('model','settings'));
    }

    public function profile()
    {
        $id=Auth::user()->id;
        $settings = Employee::settings(1);
        $model = Employee::find($id);
        return view('employee.profile',compact('model','settings'));
    }

    public function profileupdate(Request $request)
    {
        $id=Auth::user()->id;
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
        $model = Employee::find($id);
        //  print_r($request); exit;
        $model->update($data);

        return redirect()->route('profile')->with('success','Profile successfully updated');
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
        $model = Employee::find($id);
      //  print_r($request); exit;
       $model->update($data);

        return redirect()->route('employee.index')->with('success','Запись успешно обновлена');
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

        return redirect()->route('employee.index')
            ->with('success','Запись удалена');
    }
}
