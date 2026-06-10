<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function currentUser(Request $request)
    {
        return $this->apiResponse(
            message: 'Current User',
            data: [
                'user' => $request->user()->toResource(),
            ]
        );
    }
}
