<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\PersonnelEmployee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Helpers\ActivityLogHelper;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title="employees";
        $designations = Designation::get();
        $departments = Department::get();
        $employees = Employee::with('department','designation')->get();
        $dotnet_employees = PersonnelEmployee::get();


        return view('backend.employees',
        compact('title','designations','departments','employees', 'dotnet_employees'));
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function list()
   {
       $title="employees";
       $designations = Designation::get();
       $departments = Department::get();
       $employees = Employee::with('department','designation')->get();
       $dotnet_employees = PersonnelEmployee::get();
       // dd($employees);

       return view('backend.employees-list',
       compact('title','designations','departments','employees', 'dotnet_employees'));
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $this->validate($request,[
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
        'phone' => 'nullable|max:15',
        'company' => 'required|max:200',
        'avatar' => 'file|image|mimes:jpg,jpeg,png,gif',
        'department' => 'required|integer', 
        'designation' => 'required|integer', 
    ]);

    $imageName = null;
    if ($request->hasFile('avatar')) {
        $imageName = time().'.'.$request->avatar->extension();
        $request->avatar->move(public_path('storage/employees'), $imageName);
    }

    $uuid = IdGenerator::generate(['table' => 'employees','field'=>'uuid', 'length' => 7, 'prefix' =>'EMP-']);

    $employee = Employee::create([
        'uuid' => $uuid,
        'firstname' => $request->firstname,
        'lastname' => $request->lastname,
        'email' => $request->email,
        'phone' => $request->phone,
        'company' => $request->company,
        'department_id' => $request->department,
        'designation_id' => $request->designation,
        'avatar' => $imageName,
    ]);

    if ($employee) {
        storeActivityLog($userId=1, $action='store', $description=$request->firstname.' '.$request->lastname, $moduleName='Employee', $moduleId=$employee->id ,$status='Employee has been added');
        return back()->with('success', "Employee has been added");
    } else {
        return back()->with('error', "Failed to add employee");
    }
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request)
    // {
    //     $this->validate($request, [
    //         'firstname' => 'required',
    //         'lastname' => 'required',
    //         'email' => 'required|email',
    //         'phone' => 'nullable|max:15',
    //         'company' => 'required|max:200',
    //         'avatar' => 'file|image|mimes:jpg,jpeg,png,gif',
    //         'department_id' => 'required', // Add validation for department_id
    //         'designation_id' => 'required', // Add validation for designation_id
    //     ]);
    
    //     if ($request->hasFile('avatar')) {
    //         $imageName = time() . '.' . $request->avatar->extension();
    //         $request->avatar->move(public_path('storage/employees'), $imageName);
    //     } else {
    //         $imageName = null;
    //     }
    
    //     $employee = Employee::find($request->id);
    
    //     if (!$employee) {
    //         return back()->with('error', "Employee not found");
    //     }
    
    //     $employee->update([
    //         'uuid' => $employee->uuid,
    //         'firstname' => $request->firstname,
    //         'lastname' => $request->lastname,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'company' => $request->company,
    //         'department_id' => $request->department_id,
    //         'designation_id' => $request->designation_id,
    //         'avatar' => $imageName,
    //     ]);
    //     storeActivityLog($userId=1, $action='Update', $description=$request->firstname.''.$request->lastname, $moduleName='Employee', $moduleId=$request->id ,$status='Employee has been updated');

    //     return back()->with('success',"Employee details has been updated");
    // }
    public function update(Request $request)
{
    $this->validate($request, [
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
        'phone' => 'nullable|max:15',
        'company' => 'required|max:200',
        'avatar' => 'file|image|mimes:jpg,jpeg,png,gif',
        'department_id' => 'required|integer', 
        'designation_id' => 'required|integer', 
    ]);

    $employee = Employee::find($request->id);

    if (!$employee) {
        return back()->with('error', "Employee not found");
    }

    // Handle avatar if provided
    if ($request->hasFile('avatar')) {
        $imageName = time().'.'.$request->avatar->extension();
        $request->avatar->move(public_path('storage/employees'), $imageName);
        $employee->avatar = $imageName;
    }

    $employee->firstname = $request->firstname;
    $employee->lastname = $request->lastname;
    $employee->email = $request->email;
    $employee->phone = $request->phone;
    $employee->company = $request->company;
    $employee->department_id = $request->department_id;
    $employee->designation_id = $request->designation_id;

    if ($employee->save()) {
        storeActivityLog($userId=1, $action='Update', $description=$request->firstname.' '.$request->lastname, $moduleName='Employee', $moduleId=$request->id ,$status='Employee has been updated');
        return back()->with('success', "Employee details has been updated");
    } else {
        return back()->with('error', "Failed to update employee details");
    }
}

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    { 
        $employee = Employee::find($request->id);
        $employee->delete();
        storeActivityLog($userId=1, $action='Delete', $description='delete', $moduleName='Employee', $moduleId=$request->id ,$status='Employee has been deleted');

        return back()->with('success',"Employee has been deleted");
    }
}
