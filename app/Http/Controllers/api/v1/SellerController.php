<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $sellers = Seller::latest()->where(function ($query) use ($search){
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('location', 'like', '%'.$search.'%')
                ->orWhere('phone_number', 'like', '%'.$search.'%');
        })->paginate($request->per_page);
        return response($sellers, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $sellers = Seller::latest()->get();
        return response($sellers, 200);
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
            'name' => ['required', 'string', 'max:255', 'unique:subjects'],
            'location' => ['string', 'max:255'],
            'phone_number' => ['string', 'max:255']
        ]);
        $seller = new Seller([
            'name' => $request->name,
            'location' => $request->location,
            'phone_number' => $request->phone_number
        ]);
        $seller->save();
        return response($seller, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function show(Seller $seller)
    {
        return Response($seller, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name,'.$seller->id],
            'location' => ['string', 'max:255'],
            'phone_number' => ['string', 'max:255']
        ]);
        if($seller == null){
            return response(['message' => 'Something went wrong!'], 404);
        }

        $seller->name = $request->name;
        $seller->location = $request->location;
        $seller->phone_number = $request->phone_number;
        $seller->save();

        return response($seller, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller)
    {
        $seller->delete();
        return response(['msg' => 'Deleted Successfully!'], 200);
    }
}
