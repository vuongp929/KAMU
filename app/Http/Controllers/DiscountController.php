<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::paginate(10);
        return view('admins.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admins.discounts.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:discounts,code',
            'discount_type' => 'required|in:percent,amount',
            'discount_value' => 'required_if:discount_type,percent|nullable|numeric|min:0|max:100',
            'amount' => 'required_if:discount_type,amount|nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_order_amount' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'once_per_order' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (strtotime($request->input('start_date')) >= strtotime($request->input('end_date'))) {
            return redirect()->back()
                ->withErrors(['end_date' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu!'])
                ->withInput();
        }

        $data = $request->all();
        $data['discount_type'] = $request->input('discount_type');
        $data['discount'] = $request->input('discount_type') === 'percent' ? $request->input('discount_value') : null;
        $data['amount'] = $request->input('discount_type') === 'amount' ? $request->input('amount') : null;
        $data['once_per_order'] = $request->has('once_per_order');
        $data['is_active'] = $request->has('is_active');
        $data['used_count'] = 0;
        if (isset($data['start_date'])) $data['start_at'] = $data['start_date'];
        if (isset($data['end_date'])) $data['end_at'] = $data['end_date'];

        Discount::create($data);

        return redirect()->route('admins.discounts.index')
            ->with('success', 'Mã giảm giá đã được tạo thành công.');
    }

    public function edit(Discount $discount)
    {
        return view('admins.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:discounts,code,' . $discount->id,
            'discount_type' => 'required|in:percent,amount',
            'discount_value' => 'required_if:discount_type,percent|nullable|numeric|min:0|max:100',
            'amount' => 'required_if:discount_type,amount|nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_order_amount' => 'required|numeric|min:0',
            'max_uses' => 'required|integer|min:1',
            'once_per_order' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (strtotime($request->input('start_date')) >= strtotime($request->input('end_date'))) {
            return redirect()->back()
                ->withErrors(['end_date' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu!'])
                ->withInput();
        }

        $data = $request->all();
        $data['discount_type'] = $request->input('discount_type');
        $data['discount'] = $request->input('discount_type') === 'percent' ? $request->input('discount_value') : null;
        $data['amount'] = $request->input('discount_type') === 'amount' ? $request->input('amount') : null;
        $data['once_per_order'] = $request->has('once_per_order');
        $data['is_active'] = $request->has('is_active');
        if (isset($data['start_date'])) $data['start_at'] = $data['start_date'];
        if (isset($data['end_date'])) $data['end_at'] = $data['end_date'];

        $discount->update($data);

        return redirect()->route('admins.discounts.index')
            ->with('success', 'Mã giảm giá đã được cập nhật thành công.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admins.discounts.index')
            ->with('success', 'Mã giảm giá đã được xóa thành công.');
    }

    public function applyDiscount(Request $request)
    {
        $code = $request->input('code');
        $orderAmount = $request->input('order_amount');

        $discount = Discount::where('code', $code)->first();

        if (!$discount) {
            return response()->json(['error' => 'Mã giảm giá không tồn tại.'], 404);
        }

        if (!$discount->isValid()) {
            return response()->json(['error' => 'Mã giảm giá đã hết hạn hoặc không còn hiệu lực.'], 400);
        }

        if (!$discount->canApplyToOrder($orderAmount)) {
            return response()->json([
                'error' => 'Đơn hàng không đủ điều kiện áp dụng mã giảm giá này.',
                'min_amount' => $discount->min_order_amount
            ], 400);
        }

        return response()->json([
            'success' => true,
            'discount' => $discount
        ]);
    }
}
