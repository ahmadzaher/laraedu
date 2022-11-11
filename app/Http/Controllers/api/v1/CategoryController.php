<?php

namespace App\Http\Controllers\api\v1;

use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $year = $request->year;
        $search = $request->search;
            $categories = Category::latest()->where(function ($query) use ($search){
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            })->where(function ($query) use ($branch_id, $subject_id, $year) {

                if($branch_id != ''){
                    $query->where('categories.branch_id', $branch_id);
                    $query->where('categories.subject_id', $subject_id);
                    $query->where('categories.year', $year);
                }

            })->paginate($request->per_page);
        return response($categories, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $year = $request->year;
        $categories = Category::latest()
            ->where(function ($query) use ($branch_id, $subject_id, $year) {

                if($branch_id != ''){
                    $query->where('categories.branch_id', $branch_id);
                    $query->where('categories.subject_id', $subject_id);
                    $query->where('categories.year', $year);
                }

            })
            ->get();
        return response($categories, 200);
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
            'name' => ['required', 'string', 'max:255', 'unique:categories']
//            'branch_id' => ['required', 'integer'],
//            'subject_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        $category = new Category([
            'name' => $request->name,
            'description' => $request->description,
            'branch_id' => $request->branch_id,
            'subject_id' => $request->subject_id,
            'year' => $request->year,

        ]);
        $category->save();
        return response($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return Response($category, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
//            'branch_id' => ['required', 'integer'],
//            'subject_id' => ['required', 'integer'],
//            'year' => ['required', 'integer']
        ]);
        if($category == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->branch_id = $request->branch_id;
        $category->subject_id = $request->subject_id;
        $category->year = $request->year;
        $category->save();

        return response($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->middleware('superadmin');
        $category->delete();
        return response(['msg' => 'Deleted Successfully!'], 200);
    }
}
