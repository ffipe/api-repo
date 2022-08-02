<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Application;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

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
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        return $this->sendResponse($user, 'User added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($email, $application)
    {
        $user = User::with((['permission' => function ($query) use ($application) {
            $query->whereHas('application', function ($query) use ($application) {
                $query->where('name', $application);
            });
        }
            , 'permission.application', 'permission.role']))
            ->where('email', $email)
            ->first();

        if (is_null($user->permission)) {
            return $this->sendError('User permissions for application not found');
        }

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'application' => $user->permission->application->name,
            'role' => $user->permission->role->name,
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
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError("User not found");
        }
        
        $input = $request->all();

        $validator = Validator::make($input, [
            'application' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $application = Application::where('name', $request->application)->first();

        if (is_null($application)) {
            return $this->sendError("Application '" . $request->application . "' not found");
        }

        $role = Role::where('name', $request->role)->first();

        if (is_null($role)) {
            return $this->sendError("Application '" . $request->role . "' not found");
        }

        $permission = Permission::where([
            'user_id' => $id,
            'application_id' => $application->id
        ])->first();

        if (! is_null($permission)) {
            $permission->role_id = $role->id;
            $permission->save();
            return $this->sendResponse([], 'Role for user updated');
        }

        $permision_input = [
            'user_id' => $user->id,
            'role_id' => $role->id,
            'application_id' => $application->id,
        ];

        $permision = Permission::create($permision_input);

        return $this->sendResponse($permision, 'User register successfully.');
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
