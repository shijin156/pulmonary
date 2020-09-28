<?php

namespace App\Http\Controllers;

use App\Trialfield;
use Illuminate\Http\Request;

class TrialfieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trialfields = Trialfield::orderBy('order', 'asc')->get();
        return view('trialfields.all', compact('trialfields'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('trialfields.add');
    }

    public function createoption($id)
    {
        $parentfield = Trialfield::findOrFail($id);
        return view('trialfields.addoption', compact('parentfield'));
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
            'fieldname' => 'required|string',
            'fieldtype' => 'required|string',
            'status' => 'required',
            ],
            [],
            [
              'fieldname'   =>  'Trial Field Name',
              'fieldtype'   =>  'Field Type',
              'status'  =>  'Status',
            ]);
        if(empty($request->requiredfield)) $request->requiredfield = 0;
        $trialfield = new Trialfield;
        $trialfield->name = $request->fieldname;
        $trialfield->type = $request->fieldtype;
        $trialfield->required = $request->requiredfield;
        $trialfield->status = $request->status;
        $result = $trialfield->save();

        if($result) {
            return redirect('trialfields')->with('success','Trial Field Created Successfully');
        }
        else {
            return back()->withInput()->withErrors();
        }

    }

    public function storeoption(Request $request, $id)
    {
        $this->validate(request(), [
            'fieldname' => 'required|string',
            'status' => 'required',
            ],
            [],
            [
                'fieldname' =>  'Trial Field Name',
                'status'    =>  'Status',
            ]);
        $trialfield = new Trialfield;
        $trialfield->parent_id = $id;
        $trialfield->name = $request->fieldname;
        $trialfield->status = $request->status;
        $result = $trialfield->save();

        if($result) {
            return redirect('trialfields')->with('success','Trial Field Option Created Successfully');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $trialfield = Trialfield::findOrFail($id);
        return view('trialfields.edit', compact('trialfield'));
    }

    public function editoption($id)
    {
        $trialfield = Trialfield::findOrFail($id);
        return view('trialfields.editoption', compact('trialfield'));
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
            'fieldname' => 'required|string',
            'fieldtype' => 'required|string',
            'status' => 'required',
            ],
            [],
            [
                'fieldname' =>  'Trial Field Name',
                'fieldtype' =>  'Field Type',
                'status'    =>  'Status',
            ]);
        if(empty($request->requiredfield)) $request->requiredfield = 0;
        $trialfield = Trialfield::findOrFail($id);
        $trialfield->name = $request->fieldname;
        $trialfield->type = $request->fieldtype;
        $trialfield->required = $request->requiredfield;
        $trialfield->status = $request->status;
        $result = $trialfield->save();

        if($result) {
            return redirect('trialfields')->with('success','Trial Field Updated Successfully');
        }
        else {
            return back()->withInput()->withErrors();
        }
    }

    public function updateoption(Request $request, $id)
    {
        $this->validate(request(), [
            'fieldname' => 'required|string',
            'status' => 'required',
            ],
            [],
            [
                'fieldname' =>  'Trial Field Name',
                'status'    =>  'Status',
            ]);
        $trialfield = Trialfield::findOrFail($id);
        $trialfield->name = $request->fieldname;
        $trialfield->status = $request->status;
        $result = $trialfield->save();

        if($result) {
            return redirect('trialfields')->with('success','Dropdown Option Updated Successfully');
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
        $trialfield = Trialfield::findOrFail($id);
        $fields = Trialfield::where('parent_id', $id)->get();
        if(count($fields) > 0){
            return redirect('trialfields')->with('error', 'Error in Deleting Dropdown Trial Field. Sub-options Exist.');
        }
        else {
            if($trialfield->delete())
            {
                return redirect('trialfields')->with('success','Trial Field Deleted Successfully');
            }
            else
            {
                return redirect('trialfields')->with('error', 'Error in Deleting Trial Field');
            }
        }

    }

    public function saveorder(Request $request)
    {
        $ii = 1;
        $arr = $request->order;
        $trialfields = explode(',', $arr[0]);
        foreach($trialfields as $id)
        {
            $ii = $ii + 1;
            $trialfield = Trialfield::findOrFail($id);
            $trialfield->order = $ii;
            $trialfield->save();
        }
        return back()->with('success', 'Trialfields Order Updated Successfully');
    }
}
