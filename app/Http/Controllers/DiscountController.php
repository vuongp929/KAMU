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
        ], [
            'code.required' => 'Mã giảm giá không được để trống.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'discount_type.required' => 'Loại giảm giá không được để trống.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required_if' => 'Giá trị giảm giá không được để trống.',
            'discount_value.numeric' => 'Giá trị giảm giá phải là số.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'discount_value.max' => 'Giá trị giảm giá không được vượt quá 100%.',
            'amount.required_if' => 'Số tiền giảm không được để trống.',
            'amount.numeric' => 'Số tiền giảm phải là số.',
            'amount.min' => 'Số tiền giảm phải lớn hơn 0.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.required' => 'Ngày kết thúc không được để trống.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',
            'min_order_amount.required' => 'Đơn hàng tối thiểu không được để trống.',
            'min_order_amount.numeric' => 'Đơn hàng tối thiểu phải là số.',
            'min_order_amount.min' => 'Đơn hàng tối thiểu phải lớn hơn hoặc bằng 0.',
            'max_uses.required' => 'Số lượng tối đa không được để trống.',
            'max_uses.integer' => 'Số lượng tối đa phải là số nguyên.',
            'max_uses.min' => 'Số lượng tối đa phải lớn hơn 0.',
            'once_per_order.boolean' => 'Trường chỉ dùng 1 lần/đơn hàng không hợp lệ.',
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

        try {
            $data = $request->all();
            $data['discount_type'] = $request->input('discount_type');
            $data['discount'] = $request->input('discount_type') === 'percent' ? $request->input('discount_value') : null;
            $data['amount'] = $request->input('discount_type') === 'amount' ? $request->input('amount') : null;
            $data['once_per_order'] = $request->has('once_per_order');
            $data['is_active'] = $request->has('is_active');
            $data['used_count'] = 0;
            
            // Chuyển đổi start_date và end_date thành start_at và end_at
            $data['start_at'] = $request->input('start_date');
            $data['end_at'] = $request->input('end_date');
            
            // Loại bỏ các field không cần thiết
            unset($data['start_date'], $data['end_date'], $data['discount_value']);

            Discount::create($data);

            return redirect()->route('admin.discounts.index')
                ->with('success', 'Mã giảm giá đã được tạo thành công.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Lỗi khi tạo mã giảm giá: ' . $e->getMessage()])
                ->withInput();
        }
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
        ], [
            'code.required' => 'Mã giảm giá không được để trống.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'discount_type.required' => 'Loại giảm giá không được để trống.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.required_if' => 'Giá trị giảm giá không được để trống.',
            'discount_value.numeric' => 'Giá trị giảm giá phải là số.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'discount_value.max' => 'Giá trị giảm giá không được vượt quá 100%.',
            'amount.required_if' => 'Số tiền giảm không được để trống.',
            'amount.numeric' => 'Số tiền giảm phải là số.',
            'amount.min' => 'Số tiền giảm phải lớn hơn 0.',
            'start_date.required' => 'Ngày bắt đầu không được để trống.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.required' => 'Ngày kết thúc không được để trống.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',
            'min_order_amount.required' => 'Đơn hàng tối thiểu không được để trống.',
            'min_order_amount.numeric' => 'Đơn hàng tối thiểu phải là số.',
            'min_order_amount.min' => 'Đơn hàng tối thiểu phải lớn hơn hoặc bằng 0.',
            'max_uses.required' => 'Số lượng tối đa không được để trống.',
            'max_uses.integer' => 'Số lượng tối đa phải là số nguyên.',
            'max_uses.min' => 'Số lượng tối đa phải lớn hơn 0.',
            'once_per_order.boolean' => 'Trường chỉ dùng 1 lần/đơn hàng không hợp lệ.',
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
        
        // Chuyển đổi start_date và end_date thành start_at và end_at
        $data['start_at'] = $request->input('start_date');
        $data['end_at'] = $request->input('end_date');
        
        // Loại bỏ các field không cần thiết
        unset($data['start_date'], $data['end_date'], $data['discount_value']);

        $discount->update($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Mã giảm giá đã được cập nhật thành công.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Mã giảm giá đã được xóa thành công.');
    }

    public function getAvailableVouchers()
    {
        $vouchers = Discount::where('is_active', true)
            ->where('max_uses', '>', 0)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedVouchers = $vouchers->map(function ($voucher) {
            return [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'title' => $this->generateVoucherTitle($voucher),
                'description' => $this->generateVoucherDescription($voucher),
                'type' => $voucher->discount_type === 'percent' ? 'percentage' : 'fixed',
                'value' => $voucher->discount_type === 'percent' ? $voucher->discount : $voucher->amount,
                'maxDiscount' => $voucher->discount_type === 'percent' ? $voucher->amount : null,
                'minOrder' => $voucher->min_order_amount,
                'condition' => 'Đơn tối thiểu ' . number_format($voucher->min_order_amount) . 'đ',
                'maxUses' => $voucher->max_uses,
                'usedCount' => $voucher->used_count,
                'startAt' => $voucher->start_at,
                'endAt' => $voucher->end_at,
                'icon' => $this->getVoucherIcon($voucher)
            ];
        });

        return response()->json([
            'success' => true,
            'vouchers' => $formattedVouchers
        ]);
    }

    private function generateVoucherTitle($voucher)
    {
        if ($voucher->discount_type === 'percent') {
            return 'Giảm ' . $voucher->discount . '%';
        } else {
            return 'Giảm ' . number_format($voucher->amount) . 'đ';
        }
    }

    private function generateVoucherDescription($voucher)
    {
        if ($voucher->discount_type === 'percent') {
            $maxDiscount = $voucher->amount ? ' tối đa ' . number_format($voucher->amount) . 'đ' : '';
            return 'Giảm ' . $voucher->discount . '%' . $maxDiscount;
        } else {
            return 'Giảm ngay ' . number_format($voucher->amount) . 'đ';
        }
    }

    private function getVoucherIcon($voucher)
    {
        if ($voucher->discount_type === 'percent') {
            return 'fas fa-percentage';
        } else {
            return 'fas fa-money-bill-wave';
        }
    }

    public function applyDiscount(Request $request)
    {
        // Xử lý JSON request
        $data = $request->json()->all();
        $code = $data['code'] ?? null;
        $orderAmount = $data['order_amount'] ?? null;

        if (!$code || !$orderAmount) {
            return response()->json(['error' => 'Thiếu thông tin mã giảm giá hoặc giá trị đơn hàng.'], 400);
        }

        $discount = Discount::where('code', $code)->first();

        if (!$discount) {
            return response()->json(['error' => 'Mã giảm giá không tồn tại.'], 404);
        }

        // Kiểm tra trạng thái mã giảm giá
        $status = $discount->getStatus();
        $errorMessages = [
            'disabled' => 'Mã giảm giá này đã bị vô hiệu hóa.',
            'expired' => 'Mã giảm giá này đã hết hạn.',
            'not_started' => 'Mã giảm giá này chưa có hiệu lực.',
            'used_up' => 'Mã giảm giá này đã hết số lượng sử dụng.'
        ];
        
        if (isset($errorMessages[$status])) {
            return response()->json(['error' => $errorMessages[$status]], 400);
        }

        // Kiểm tra số lượng còn lại
        if ($discount->max_uses !== null && $discount->used_count >= $discount->max_uses) {
            return response()->json(['error' => 'Mã giảm giá này đã hết số lượng sử dụng.'], 400);
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
