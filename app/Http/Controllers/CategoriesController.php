<?php

namespace App\Http\Controllers;

use App\Category;
use App\Clinicaltrial;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('categories.all', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.add');
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
            'name' => 'required|string|unique:categories',
        ],
        [
            'name.unique'   => 'This Medical Condition already exists',
        ],
        [
            'name'  =>  'Medical Condition',
        ]);
        $category = new Category;
        $result=$category->create($request->all());

        if($result) {
            return redirect('clinicaltrials')->with('success','Medical Condition Created Successfully');
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
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
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
            'name' => 'required|string',
        ],
        [
            'name.unique'   => 'This Medical Condition already exists',
        ],
        [
            'name'  =>  'Medical Condition'
        ]);
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $result = $category->save();
        if($result) {
            return redirect('clinicaltrials')->with('success','Medical Condition Updated Successfully');
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
        $category = Category::findOrFail($id);
        $trials = Clinicaltrial::where('category_id', $id)->get();
        if(count($trials) > 0)
        {
            return back()->with('error', 'Cannot Delete Medical Condition. Clinical Trial Entries Present.');
        }
        else {
            if($category->delete()) {
                return redirect('clinicaltrials')->with('success', 'Medical Condition Deleted Successfully');
            }
            else
            {
                return redirect('clinicaltrials')->with('error', 'Error in Deleting Medical Condition');
            }
        }
    }

    public function saveorder(Request $request)
    {
        $ii = 1;
        $arr = $request->order;
        dd($arr);
        $categories = explode(',', $arr[0]);
        pront_r($categories);
        foreach($categories as $id)
        {
            $ii = $ii + 1;
            $category = Category::findOrFail($id);
            $category->order = $ii;
            $category->save();
        }
        return back()->with('success', 'Medical Conditions Order Updated Successfully');
    }
}
