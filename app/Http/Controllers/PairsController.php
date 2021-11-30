<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PairsController extends Controller
{
    public function update(Request $request): bool
    {
        if ($request->pairs) {
            file_put_contents(public_path() . '/pairs.json', $request->pairs);
        } else {
            return false;
        }

        return true;
    }
}
