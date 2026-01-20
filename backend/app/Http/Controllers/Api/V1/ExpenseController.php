<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::where('user_id', Auth::id())->get();
        return ExpenseResource::collection($expenses);
    }

    public function store(CreateExpenseRequest $request)
    {
        $expense = Expense::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return new ExpenseResource($expense);
    }

    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return new ExpenseResource($expense);
    }

    public function update(CreateExpenseRequest $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($request->only(['amount', 'description', 'date']));

        return new ExpenseResource($expense);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(null, 204);
    }
}