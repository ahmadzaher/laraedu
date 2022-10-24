<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
//        $summaries = Summary::paginate(25);
//
//        $summaries->each(function ($summary) {
//            $summary->getMedia();
//            $summary->summary_image = url($summary->getFirstMediaUrl('summaries'));
//        });
//
//        return Response($summaries);


        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $seller_id = $request->seller_id;
        $year = $request->year;
        $search = $request->search;
        $summaries = Summary::latest()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('id', 'like', '%'.$search.'%');
            })->where(function ($query) use ($branch_id, $subject_id, $year, $seller_id) {

                if($branch_id != ''){
                    $query->where('summaries.branch_id', $branch_id);
                    $query->where('summaries.subject_id', $subject_id);
                    $query->where('summaries.seller_id', $seller_id);
                    $query->where('summaries.year', $year);
                }

            })->select(['summaries.*'])->paginate($request->per_page);
        $summaries->each(function ($summary) {
            $summary->getMedia();
            $summary->summary_file = $summary->getFirstMediaUrl('summaries') ? url($summary->getFirstMediaUrl('summaries')) : '';
        });

        return response($summaries, 200);
    }

    public function all(Request $request)
    {
        $branch_id = $request->branch_id;
        $subject_id = $request->subject_id;
        $year = $request->year;
        $summaries = Summary::where('published', 1)
            ->select(['summaries.*', DB::raw("(SELECT COUNT('id') FROM `transactions` WHERE transactions.summary_id = summaries.id AND transactions.user_id = ".$request->user()->id.") as is_purchased ")])
            ->where(function ($query) use ($branch_id, $subject_id, $year) {

                if($branch_id != ''){
                    $query->where('summaries.branch_id', $branch_id);
                    $query->where('summaries.subject_id', $subject_id);
                    $query->where('summaries.year', $year);
                }

            })
            ->latest()
            ->get();
        $summaries->each(function ($summary) {
            $summary->getMedia();
            $summary->summary_file = $summary->getFirstMediaUrl('summaries') ? url($summary->getFirstMediaUrl('summaries')) : '';
        });
        return response($summaries, 200);
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|min:3',
            'teacher_name' => 'min:3',
            'description' => 'min:3',
            'file' => 'mimes:pdf',
            'percentage' => 'required|integer',
            'price' => 'required|integer'
        ]);

        $summary  = Summary::create([
            'name' => request('name'),
            'teacher_name' => request('teacher_name'),
            'description' => request('description'),
            'year' => request('year'),
            'branch_id' => request('branch_id'),
            'subject_id' => request('subject_id'),
            'seller_id' => request('seller_id'),
            'price' => request('price'),
            'percentage' => request('percentage')
        ]);

        if (request()->hasFile('file') && request()->file('file')->isValid()) {
            $summary->addMediaFromRequest('file')
                ->usingFileName(sha1(request('file')))
                ->toMediaCollection('summaries');
        }

        return Response($summary, 201);
    }

    public function show(Summary $summary, Request $request)
    {
        $summary->getMedia();

        $summary->summary_file = $summary->getFirstMediaUrl('summaries') ? url($summary->getFirstMediaUrl('summaries')) : '';

        return Response($summary);
    }

    public function student_show(Summary $summary, Request $request)
    {

        if(!$summary->purchasedBy($request->user()))
            return response(['message' => 'Not Purchased'], 403);
        $summary->getMedia();

        $summary->summary_file = $summary->getFirstMediaUrl('summaries') ? url($summary->getFirstMediaUrl('summaries')) : '';

        return Response($summary);
    }

    public function update(Summary $summary)
    {
        request()->validate([
            'name' => 'required|min:3',
            'teacher_name' => 'min:3',
            'description' => 'min:3',
            'file' => 'mimes:pdf',
            'percentage' => 'required|integer',
            'price' => 'required|integer'
        ]);

        $summary->update( [
           'name' => request('name'),
            'teacher_name' => request('teacher_name'),
            'description' => request('description'),
            'year' => request('year'),
            'branch_id' => request('branch_id'),
            'subject_id' => request('subject_id'),
            'seller_id' => request('seller_id'),
            'price' => request('price'),
            'percentage' => request('percentage')
        ]);

        if (request()->hasFile('file') && request()->file('file')->isValid()) {
            $summary->clearMediaCollection('summaries');
            $summary->addMediaFromRequest('file')->toMediaCollection('summaries');
        }

        return Response($summary);
    }

    public function destroy(Summary $summary)
    {
        $summary->delete();

        return Response(['message' => 'Summary Deleted Successfully!'] );
    }
}
