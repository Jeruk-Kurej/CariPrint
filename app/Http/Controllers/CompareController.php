<?php

namespace App\Http\Controllers;

use App\Services\Pricing\PriceComparison;
use Inertia\Inertia;
use Inertia\Response;

class CompareController extends Controller
{
    public function index(PriceComparison $comparison): Response
    {
        return Inertia::render('compare/index', $comparison->forAllShops());
    }
}
