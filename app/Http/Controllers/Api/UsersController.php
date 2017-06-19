<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    public function show(Request $request)
    {
        $user = User::find($request->id);
        return formatJson(__('api_common.success'), 200, $user);
    }

    public function showFromToken(Request $request)
    {
        $token = Token::tokenValue($request->token)->first();
        return formatJson(__('api_common.success'), 200, $token->user);
    }
}