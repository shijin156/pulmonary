<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('id', '!=', 1)->get();;
        return view('users.list', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('users.add', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required|string|unique:users',
            'email' => 'required|string|unique:users|email:rfc,dns',
            'password' => 'required|string|confirmed',
            'role'  => 'required',
            ],
            [],
            [
                'name'  =>  'Name',
                'email' =>  'Email',
                'password'  =>  'Password',
                'role'  =>  'User Role',
            ]);
        if($request->role == 'Coordinator') {
            $this->validate(request(), [
                'phone' => 'required|string',
                'ext' => 'required|string',
            ],
            [],
            [
                'phone' =>  'Phone Number',
                'ext'   =>  'Phone Extension',
            ]);
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->input('password'));
        $user->username = Str::slug($request->name, '');
        $user->phone = $request->phone;
        $user->ext = $request->ext;
        $result = $user->save();
        $user->syncRoles($request->role);

        if($result) {
            return redirect()->route('allusers')->with('success','User Created Successfully');
        }
        else {
            return back()->withInput()->withErrors();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function editprofile()
    {
        $user = User::findOrFail(Auth::user()->id);
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    public function updateprofile(Request $request, $id)
    {
        $this->validate(request(), [
            'name' => 'required|string',
            ],
            [],
            [
                'name'  =>  'Name',
            ]);
        $user = User::findOrFail($id);
        if(!empty($request->password)) {
            $this->validate(request(), [
                'password' => 'required|string|confirmed',
                ],
                [],
                [
                    'password'  =>  'Password',
                ]);
            //save password only if password field is not empty
            $user->password = bcrypt($request->input('password'));
        }
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->ext = $request->ext;
        $result = $user->save();
        if($result) {
            return redirect('users/'.$id.'/editprofile')->with('success','Profile Updated Successfully');
        }
        else {
            return back()->withInput()->withErrors();
        }
    }
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            'name' => 'required|string',
            'email' => 'required|string|email:rfc,dns',
            'role' => 'required',
        ],
        [],
        [
            'name'  =>  'Name',
            'email' =>  'Email',
            'role'  =>  'User Role',
        ]);
        $user = User::findOrFail($id);
        if(!empty($request->password)) {
            $request->validate([
                'password' => 'required|string|confirmed',
            ]);
            $user->password = bcrypt($request->input('password'));
        }
        if($request->role == 'Coordinator') {
            $this->validate(request(), [
                'phone' => 'required|string',
                'ext' => 'required|string',
            ],
            [],
            [
                'phone' =>  'Phone Number',
                'ext'   =>  'Phone Extension',
            ]);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->ext = $request->ext;
        $user->username = Str::slug($request->name, '');
        $result = $user->save();
        $user->syncRoles($request->role);
        if($result) {
            return redirect()->route('allusers')->with('success','User Updated Successfully');
        }
        else {
            return back()->withInput()->withErrors();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if($user->delete())
        {
            return redirect()->route('allusers')->with('success', 'User Deleted Successfully');
        }
        else
        {
            return redirect()->route('allusers')->with('error', 'Error in Deleting User');
        }
    }
}
