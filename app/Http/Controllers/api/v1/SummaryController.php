<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Summary;

class SummaryController extends Controller
{
    public function index()
    {
        $summaries = Summary::paginate(25);

        $summaries->each(function ($summary) {
            $summary->getMedia();
            $summary->summary_image = url($summary->getFirstMediaUrl('summaries'));
        });

        return Response($summaries);
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|min:3',
            'name_teacher' => 'min:3',
            'description' => 'min:3',
            'file' => 'mimes:pdf,doc,docx'
        ]);

        $summary  = Summary::create([
            'name' => request('name'),
            'name_teacher' => request('name_teacher'),
            'description' => request('description'),
            'year' => request('year'),
            'branch_id' => request('branch_id')
        ]);

        if (request()->hasFile('file') && request()->file('file')->isValid()) {
            $summary->addMediaFromRequest('file')
                ->toMediaCollection('summaries');
        }

        return Response($summary, 201);
    }

    public function show(Summary $summary)
    {
        $summary->getMedia();

        $summary->summary_image = url($summary->getFirstMediaUrl('summaries'));

        return Response($summary);
    }

    public function update( Summary $summary)
    {
        request()->validate([
            'name' => 'required|min:3',
            'teacher_name' => 'min:3',
            'description' => 'min:3',
            'file' => 'mimes:pdf,doc,docx'
        ]);

        $summary->update( [
           'name' => request('name'),
            'teacher_name' => request('teacher_name'),
            'description' => request('description'),
            'year' => request('year'),
            'branch_id' => request('branch_id')
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
