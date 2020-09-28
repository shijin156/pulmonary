<?php

namespace App\Http\Controllers;

use App\Category;
use App\Clinicaltrial;
use App\Mail\SubmitpatientMail;
use App\Trialfield;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ClinicaltrialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('clinicaltrials.list', compact('categories'));

    }

    public function search(Request $request)
    {
        $searchterm = $request->search;
        $categories = Category::where('name', 'LIKE', "%{$request->search}%")->orWhereHas('trials', function ($q) use ($searchterm) {
            $q->where('trialname', 'LIKE', "%{$searchterm}%");
         })->orderBy('name', 'asc')->get();
        return view('clinicaltrials.search', compact('categories', 'searchterm'));

    }

    public function sort($id)
    {
        $categories = Category::where('name', 'LIKE', "{$id}%")->orderBy('name', 'asc')->get();
        return view('clinicaltrials.sort', compact('categories'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $coordinators = User::role('Coordinator')->get();
        $trialtypes = Trialfield::where('status', 1)->get();
        return view('clinicaltrials.add', compact('categories', 'trialtypes', 'coordinators'));
    }

    public function createbycondition($id)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $category = Category::findOrFail($id);
        $coordinators = User::role('Coordinator')->get();
        $trialtypes = Trialfield::where('status', 1)->get();
        return view('clinicaltrials.addbycategory', compact('categories', 'category', 'trialtypes', 'coordinators'));
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
            'trialname'   => 'required|string',
            'category_id' => 'required',
            'coordinator_id'    => 'required',
        ],
        [],
        [
            'trialname' =>  'Trial Name',
            'category_id'   =>  'Medical Condition',
            'coordinator_id'    =>  'Coordinator Name',
        ]);
        $clinicaltrial = Clinicaltrial::create($request->except('trialtypes'));
        $clinicaltrial->trialfields()->attach($request->trialtypes);
        if($clinicaltrial) {
            return redirect('clinicaltrials')->with('success','Clinical Trial Created Successfully');
        }
        else {
            return back()->withInput()->withErrors();
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
        $trialtypes = Trialfield::where('status', 1)->orderBy('order', 'asc')->get();
        $clinicaltrial = Clinicaltrial::findOrFail($id);
        return view('clinicaltrials.view', compact('trialtypes', 'clinicaltrial'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $trialtypes = Trialfield::where('status', 1)->orderBy('order', 'asc')->get();
        $clinicaltrial = Clinicaltrial::findOrFail($id);
        $coordinators = User::role('Coordinator')->get();
        return view('clinicaltrials.edit', compact('categories', 'trialtypes', 'clinicaltrial', 'coordinators'));
    }

    // public function submitpatient($id)
    // {
    //     $trialtypes = Trialfield::where('status', 1)->orderBy('order', 'asc')->get();
    //     $clinicaltrial = Clinicaltrial::findOrFail($id);
    //     return view('clinicaltrials.submitpatient', compact('trialtypes', 'clinicaltrial'));
    // }

    public function forwardpatient(Request $request, $id)
    {
        $this->validate(request(), [
            'physician'           => 'required|string',
            'patientid'           => 'required|numeric',
            ],
            [],
            [
                'physician' =>  'Physician Name',
                'patientid' =>  'Patient ID',
            ]);
        $trialtypes = Trialfield::where('status', 1)->orderBy('order', 'asc')->get();
        $clinicaltrial = Clinicaltrial::findOrFail($id);
        $coordinator = User::findOrFail($clinicaltrial->coordinator_id);
        $data['physician'] = $request->physician;
        $data['patientid'] = $request->patientid;
        $data['clinicaltrial'] = $clinicaltrial;
        $data['trialtypes'] = $trialtypes;
        $data['coordinator'] = $coordinator;
        $data['crcemail'] = $coordinator->email;
        $data['crcname'] = $coordinator->name;
        Mail::send('email.submitpatient', $data, function ($message) use ($data) {
            $message->from('noreply@webstuff.com', 'CFPG');
            $message->sender('noreply@webstuff.com', 'CFPG');
            // $message->to('andy@webstuff.com', 'Andy Anderson');
            $message->to($data['crcemail'], $data['crcname']);
            $message->cc('info@voiceactor.net', 'Kathleen');
            // $message->replyTo('john@johndoe.com', 'John Doe');
            $message->subject('Clinical Trial Patient Submission');
            $message->priority(3);
        });
        if(count(Mail::failures()) > 0) {
            return back()->withInput()->withErrors();
        }
        else {
            activity()->performedOn($clinicaltrial)->log('New Submission');
            return redirect('clinicaltrials')->with('success','Patient Details Submitted Successfully');
        }
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
        $this->validate(request(), [
            'trialname'   => 'required|string',
            'category_id' => 'required',
            'coordinator_id'    => 'required',
            ],
            [],
            [
                'trialname' =>  'Trial Name',
                'category_id'   =>  'Medical Condition',
                'coordinator_id'    =>  'Coordinator Name',
            ]);
        $clinicaltrial = Clinicaltrial::findOrFail($id);
        $clinicaltrial->trialfields()->detach(); //deletes old values
        $clinicaltrial->trialfields()->attach($request->trialtypes);
        $result = $clinicaltrial->update($request->all());
        if($result) {
            return back()->with('success','Clinical Trial Updated Successfully');
        }
        else {
            return back()->withInput()->withErrors();
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
        $clinicaltrial = Clinicaltrial::findOrFail($id);
        if($clinicaltrial->delete())
        {
            return back()->with('success','Clinical Trial Deleted Successfully');
        }
        else
        {
            return back()->with('error', 'Error in Deleting Clinical Trial');
        }
    }
}
