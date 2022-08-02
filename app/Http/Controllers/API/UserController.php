<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->sendResponse($users, 'List of users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'application' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        unset($input['application']);
        unset($input['role']);

        $application = Application::where('name', $request->application)->first();

        if (is_null($application)) {
            return $this->sendError("Application '" . $request->application . "' not found");
        }

        $role = Role::where('name', $request->role)->first();

        if (is_null($role)) {
            return $this->sendError("Application '" . $request->role . "' not found");
        }

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $permision_input = [
            'user_id' => $user->id,
            'role_id' => $role->id,
            'application_id' => $application->id,
        ];

        $permision = Permission::create($permision_input);

        return $this->sendResponse($permision, 'User register successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($email, $application)
    {
        $user = User::with(['permissions.application', 'role.role'])
            ->where('email', $email)
            ->whereHas('permissions.application', function ($query) use ($application) {
                // $query->whereHas('application', function ($query) use ($application) {
                    $query->where('name', $application);
                // });
            })
            ->first();
        return $user;
        // dd($user->permission);

        if (is_null($user)) {
            return $this->sendError('User permissions for application not found');
        }

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'application' => $user->permissions->application->name,
            'role' => $user->role->role->name
        ];

        return $this->sendResponse($data, 'User permissions retirived');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();

        dd();

        // return response()->json($input);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
