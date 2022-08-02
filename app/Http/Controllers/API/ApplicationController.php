<?php

namespace App\Http\Controllers\API;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Resources\ApplicationResource;
use App\Http\Controllers\API\BaseController;
use Validator;

class ApplicationController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $applications = Application::all();

        return $this->sendResponse(ApplicationResource::collection($applications), 'List of applications');
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
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error.', $validator->errors());
        }

        $application = Application::create($input);

        return $this->sendResponse(new ApplicationResource($application), 'New application added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $application = Application::find($id);

        if (is_null($application)) {
            return $this->sendError('Application not found');
        }

        return $this->sendResponse(new ApplicationResource($application), 'Application retrived');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation update error.', $validator->errors());
        }

        $application->name = $input['name'];
        $application->save();

        return $this->sendResponse(new ApplicationResource($application), 'Application updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        $application->delete();

        return $this->sendResponse([], 'Application deleted');
    }
}
