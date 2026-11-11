<?php

namespace App\Http\Controllers;

use App\Services\Directory\ShopDirectory;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(ShopDirectory $directory): Response
    {
        return Inertia::render('home', [
            'shops' => $directory->list(),
        ]);
    }
}
