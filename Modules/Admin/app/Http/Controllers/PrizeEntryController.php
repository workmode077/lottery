<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Price;

class PrizeEntryController extends Controller
{
    protected $modelClass = 'App\Models\Price';
    protected $viewPath = 'prize-entry';

    public function index()
    {
        $baseRouteName = $this->getBaseRouteName();
        $price = Price::first();
        return view('admin::' . $this->viewPath . '.create', compact('baseRouteName', 'price'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $price = Price::findOrFail(base64_decode($id));
            $price->update($request->except(['_token', '_method']));
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Price updated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
