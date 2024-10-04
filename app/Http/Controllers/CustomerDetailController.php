<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;

class CustomerDetailController extends Controller
{
    public function index()
    {
        $customerDetails = CustomerDetail::with('company')->paginate(10);
        return view('customer-details.index', compact('customerDetails'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('customer-details.create', compact('companies'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'identification' => 'required|unique:customer_details',
            'full_name' => 'required',
            'email' => 'required|email|unique:customer_details',
            'phone' => 'required',
            'address' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        CustomerDetail::create($validatedData);

        return redirect()->route('customer-details.index')
            ->with('success', 'Cliente creado exitosamente.');
    }
    public function show($id)
    {
        $customer = CustomerDetail::with(['company'])->findOrFail($id);
        return view('customer-details.show', compact('customer'));
    }

    public function edit($id)
    {
        $companies = Company::all();
        $customerDetail = customerDetail::findOrFail($id);
        return view('customer-details.edit', compact('customerDetail', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $customerDetail = CustomerDetail::findOrFail($id);

        $validated = $request->validate([
            'identification' => 'required|unique:customer_details,identification,' . $customerDetail->id,
            'full_name' => 'required',
            'email' => 'required|email|unique:customer_details,email,' . $customerDetail->id,
            'phone' => 'required',
            'address' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        $customerDetail->update($validated);

        return redirect()->route('customer-details.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }


    public function destroy(CustomerDetail $customerDetail)
    {
        $customerDetail->delete();

        return redirect()->route('customer-details.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}
