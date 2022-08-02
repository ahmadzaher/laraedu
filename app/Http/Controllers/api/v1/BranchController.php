<?php

namespace App\Http\Controllers\api\v1;

use App\Branch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        if($search != '')
        {
            $groups = Branch::latest()->where(function ($query) use ($search){
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            })->paginate($request->per_page);
        }else
            $groups = Branch::latest()->paginate($request->per_page);
        return response($groups, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $branches = Branch::latest()->get();
        return response($branches, 200);
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
            'name' => ['required', 'string', 'max:255', 'unique:branches'],
            'years' => ['required', 'integer', 'between:1,6']
        ]);
        $branch = new Branch([
            'name' => $request->name,
            'years' => $request->years,
            'description' => $request->description

        ]);
        $branch->save();
        return response($branch, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return Response($branch, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name,'.$branch->id],
            'years' => ['required', 'integer'],
        ]);
        if($branch == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $branch->name = $request->name;
        $branch->description = $request->description;
        $branch->years = $request->years;
        $branch->save();

        return response($branch, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return response(['msg' => 'Deleted Successfully!'], 200);
    }
}
