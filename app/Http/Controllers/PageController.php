<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function features()
    {
        return view('pages.funcionalidades');
    }

    public function benefits()
    {
        return view('pages.beneficios');
    }

    public function testimonials()
    {
        return view('pages.depoimentos');
    }

    public function about()
    {
        return view('pages.sobre');
    }

    public function terms()
    {
        return view('pages.termos');
    }

    public function privacy()
    {
        return view('pages.privacidade');
    }
}
