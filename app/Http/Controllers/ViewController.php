<?php

namespace App\Http\Controllers;

use App\Models\Alarm;
use App\Models\Setting;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $last = Alarm::count();
        $alert = Setting::where(['name' => 'alert'])->first();
        $sound  = asset('/storage/' . $alert->val) ?? asset('/alert.mp3');

        return view('viewer', compact('last', 'alert', 'sound'));
    }
}
