<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\QuotationFile;

class DashboardController extends Controller
{

    public function index()
    {
        $customersCount = Customer::count();
        $quotationsCount = QuotationFile::count();

        return view('dashboard', compact('customersCount', 'quotationsCount'));
    }

    public function profile()
    {
        return view('profile');
    }

    public function quotations()
    {
        return redirect()->route('quotations.index');
    }

    public function customers()
    {
        return redirect()->route('customers.index');
    }

    public function settings()
    {
        return view('settings');
    }
}
