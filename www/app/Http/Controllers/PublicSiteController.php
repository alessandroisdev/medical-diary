<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Setting;

class PublicSiteController extends Controller
{
    private function getSettings()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function home()
    {
        $settings = $this->getSettings();
        return view('public.home', compact('settings'));
    }

    public function about()
    {
        $settings = $this->getSettings();
        return view('public.about', compact('settings'));
    }

    public function specialties()
    {
        $specialties = Specialty::all();
        $settings = $this->getSettings();
        return view('public.specialties', compact('specialties', 'settings'));
    }

    public function doctors()
    {
        $doctors = Doctor::with('specialties')->get();
        $settings = $this->getSettings();
        return view('public.doctors', compact('doctors', 'settings'));
    }

    public function contact()
    {
        $settings = $this->getSettings();
        return view('public.contact', compact('settings'));
    }
}
