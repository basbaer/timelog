<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForstwirtFormController extends Controller
{
    public function show()
    {
        return view('log-forms/log-forstwirt');
    }
}
