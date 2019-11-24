<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Http\Requests\ActivateCodeRequest;
use App\Http\Requests\CouponByIdRequest;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\DeleteCouponRequest;
use App\Http\Requests\FilterCouponsRequest;
use App\Http\Requests\GetCouponByCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CouponController extends Controller
{
    public function create(CreateCouponRequest $request)
    {
        $data = $request->all();
        $count = $data['count'];
        unset($data['count']);

        do {
            $data['code'] = rand(100000000, 999999999);
        }
        while(Coupon::where('code', $data['code'])->count());

        $qrCode = new QrCode('ASY'.$data['code']);
        $qrCode->setSize(500);
        $qrCode->setWriterByName('png');
        Storage::put('codes/'.$data['code'].'.png', $qrCode->writeString());
        $data['qrlink'] = Str::random(8);

        Coupon::create($data);

        return ResponseBuilder::success($request);
    }

    public function list(FilterCouponsRequest $request)
    {
        $coupons = Coupon::with('activations')
            ->orderByDesc('id');
        if(!empty($request->active)) $coupons->where('active', $request->active);
        return ResponseBuilder::success($coupons->get());
    }

    public function delete(DeleteCouponRequest $request)
    {
        Coupon::find($request->id)->activations()->delete();
        Coupon::find($request->id)->delete();
        return ResponseBuilder::success();
    }

    public function showCode($link)
    {
        $coupon = Coupon::where('qrlink', $link)->first();
        if($coupon) {
            $qr = Storage::get('codes/'.$coupon->code.'.png');
        } else {
            $pseudo = '';
            for($i = strlen($link) - 1; $i >= 0; $i--) {
                $pseudo .= ord($link[$i]);
            }
            if(strlen($pseudo) > 9) {
                $pseudo = substr($pseudo, 0, 9);
            } else {
                $pseudo .= rand(pow(10, 8 - strlen($pseudo)), pow(10, 9 - strlen($pseudo)) - 1);
            }
            $qrCode = new QrCode('ASY'.$pseudo);
            $qrCode->setSize(500);
            $qrCode->setWriterByName('png');
            $qr = $qrCode->writeString();
        }
        return response($qr, 200)
            ->header('Content-Type', 'image/png');
    }

    public function getByCode(GetCouponByCodeRequest $request)
    {
        $coupon = Coupon::with('activations')
            ->where('code', $request->code)->first();
        if(!$coupon) return ResponseBuilder::error(105);
        $coupon->state = static::checkCoupon($coupon);
        return ResponseBuilder::success($coupon);
    }

    public function activateCode(ActivateCodeRequest $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        $state = static::checkCoupon($coupon);
        if($state['status'] == 'success') {
            $coupon->activations()->create(['date' => Carbon::now()]);
        }
        return ResponseBuilder::{$state['status']}($state['code']);
    }

    public static function toggleActive(CouponByIdRequest $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->active = !$coupon->active;
        $coupon->save();
        return ResponseBuilder::success();
    }



    public static function checkCoupon(Coupon $coupon)
    {
        $now = Carbon::now();
        if($coupon->active) {
            if($coupon->infinite) {
                if($coupon->reusable) {
                    return ['status' => 'success', 'code' => null];
                } else {
                    if($coupon->activations()->count() == 0) {
                        return ['status' => 'success', 'code' => null];
                    } else {
                        // coupon already activated
                        return ['status' => 'error', 'code' => 102];
                    }
                }
            } else if($coupon->active_from < $now && $coupon->active_to > $now) {
                if($coupon->reusable) {
                    return ['status' => 'success', 'code' => null];
                } else {
                    if($coupon->activations()->count() == 0) {
                        return ['status' => 'success', 'code' => null];
                    } else {
                        // coupon already activated
                        return ['status' => 'error', 'code' => 102];
                    }
                }
            } else {
                // coupon outdated
                return ['status' => 'error', 'code' => 103];
            }
        } else {
            // coupon desactivated
            return ['status' => 'error', 'code' => 104];
        }
    }
}