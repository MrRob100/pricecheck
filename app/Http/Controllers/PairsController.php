<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePairsRequest;
use App\Models\Pair;

class PairsController extends Controller
{
    public function update(UpdatePairsRequest $request): bool
    {
        foreach(Pair::all() as $existing) {
            $existing->delete();
        }

        foreach ($request->pairs as $pair) {
            Pair::create([
                'symbol1' => $pair['s1'],
                'symbol2' => $pair['s2'],
            ]);
        }

        return true;
    }
}
