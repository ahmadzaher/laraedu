<?php

namespace App\Http\Controllers\api\v1;

use App\Bank;
use App\Http\Controllers\Controller;
use App\Question;
use Symfony\Component\HttpFoundation\Response;

class BankController extends Controller
{
    public function index()
    {
        $quizzes = Bank::with('questions')
            ->select(['banks.*', 'categories.name as category_name'])
            ->leftJoin('categories', 'categories.id', '=', 'banks.category_id')
            ->latest()
            ->filter(request(['search']))
            ->paginate(request('per_page'));

        return response($quizzes);
    }

    public function store()
    {
        request()->validate([
            'name' => 'string|max:255|unique:banks'
        ]);

        $bank = Bank::create([
            'name' => request('name'),
            'summary' => request('summary'),
            'content' => request('content'),
            'category_id' => request('category_id')
        ]);

        $questions = array_unique(request('questions'), SORT_REGULAR);

        $bank->questions()->sync($questions);

        return response($bank, Response::HTTP_CREATED);
    }

    public function show(Bank $bank)
    {
        $bank->questions = Question::with(['answers', 'group'])
            ->whereHas('banks', fn ($query) =>  $query->where('id', '=', $bank->id))
            ->get()
            ->each(fn ($item, $key) => $item->group_name = $item->group->title);

        return response($bank);
    }

    public function update(Bank $bank)
    {
        request()->validate([
            'name' => 'string|max:255|unique:banks'
        ]);

        $bank->update([
            'name' => request('name'),
            'summary' => request('summary'),
            'content' => request('content'),
            'category_id' => request('category_id')
        ]);

        $questions = array_unique(request('questions'), SORT_REGULAR);

        $bank->questions()->sync($questions);

        return response($bank);
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();

        return response(['message' => 'Bank Deleted Successfully!']);
    }

}
