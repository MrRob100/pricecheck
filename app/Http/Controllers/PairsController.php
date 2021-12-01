<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePairsRequest;

class PairsController extends Controller
{
    public function update(UpdatePairsRequest $request): bool
    {
        dump($request->all());

        if ($request->pairs) {

            dump('pairs');

            try {
                file_put_contents(public_path() . '/pairs.json', $request->pairs);
            }
            catch(\Exception $e) {
                dump($e->getMessage());
            }

        } else {

            dump('not pairs');

            return false;
        }

        return true;
    }
}
