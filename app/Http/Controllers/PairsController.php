<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePairsRequest;

class PairsController extends Controller
{
    public function update(UpdatePairsRequest $request): bool
    {
        if ($request->pairs) {
            file_put_contents(public_path() . '/pairs.json', $request->pairs);
        } else {
            return false;
        }

        return true;
    }
}
