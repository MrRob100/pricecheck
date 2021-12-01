<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePairsRequest;

class PairsController extends Controller
{
    public function update(UpdatePairsRequest $request): bool
    {
        $dir = public_path() . '/pairs';

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $path = "$dir/pairs.json";

        if (!file_exists($path)) {
            touch($path);
        }

        if ($request->pairs) {
            file_put_contents($path, json_encode($request->pairs));
        } else {
            return false;
        }

        return true;
    }
}
