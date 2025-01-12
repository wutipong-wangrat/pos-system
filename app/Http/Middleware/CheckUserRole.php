<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ดึงข้อมูลผู้ใช้งานจาก session
        $user = User::find(session()->get('user_id'));

        // หากไม่พบผู้ใช้งาน
        if (!$user) {
            return redirect()->to('/')->with('error', 'ไม่พบผู้ใช้งาน');
        }

        // หาก role เป็น user ให้จำกัดสิทธิ์เฉพาะบาง route
        if ($user->role === 'user') {
            // เส้นทางที่อนุญาตให้เข้าถึง
            $allowedPaths = [
                'order',
                'order/checkout',
                'history',
            ];

            $currentPath = $request->path();

            // ตรวจสอบ route สำหรับ receipt (มี parameter)
            if (str_starts_with($currentPath, 'receipt/') && str_ends_with($currentPath, '/print')) {
                return $next($request);
            }

            // ตรวจสอบว่า path ปัจจุบันอยู่ใน allowedPaths หรือไม่
            $isAllowed = in_array($currentPath, $allowedPaths);

            if (!$isAllowed) {
                return redirect()->to('/order')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }
        }

        // หาก role เป็น admin หรือมีสิทธิ์ ให้ดำเนินการต่อ
        return $next($request);
    }
}
