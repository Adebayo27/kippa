<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public OrderService $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
       
        try {
            $query = Order::with(['user.userCategory.category', 'orderItems.product']);
            if (isset($request->startDate) && $request->startDate !== '' && isset($request->endDate) && $request->endDate != '') {
                $query->whereBetween('order_date', [$request->startDate, $request->endDate]);
            }

            if (isset($request->distributor)) {
                $user = $request->distributor;
                $query->whereIn('purchaser_id', function ($query) use ($user) {
                    return $query
                        ->select('id')
                        ->from('users')
                        ->where('first_name', 'like', "%{$user}%")
                        ->orWhere('last_name', 'like', "%{$user}%")
                        ->orWhere('username', 'like', "%{$user}%")
                        ->orWhere('id', 'like', "%{$user}%");
                });
            }

            $orders = $query->paginate(15)->withQueryString();
            return view('report', ['orders' => $orders]);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function getOrderItemsByOrderId(int $orderId)
    {
        $order = OrderItem::where('order_id', $orderId)->with('product')->get();
        return response()->json($order);
    }

    public function topSelling()
    {
        $users = User::whereHas('referrers')
                ->limit(500)
                ->get()
                ->reject(function($user){
                    return $user->is_distributor === false;
                })->sortBy(function($value, $key) {
                    return ($value['total_sales']);
                }, SORT_REGULAR, true)
                ->values();

        return view('top_distributor', ['users' => $users]);
    }
}
