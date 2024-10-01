<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(){
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    public function create(){
        /* $company = new Company();
        $company->nit = '123456789';
        $company->name = 'Company 1';
        $company->phone = '1234567';
        $company->address = 'Calle 123';
        $company->department = 'Antioquia';
        $company->municipality = 'Medellin';
        $company->save();
        return $company; */

        return view('companies.create');
    }

    public function store(Request $request){
        $request->validate([
            'nit' => 'required|unique:companies',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'department' => 'required',
            'municipality' => 'required',
        ]);

        Company::create($request->all());

        return redirect()->route('companies.index')
            ->with('success', 'Empresa creada exitosamente.');
    }

    public function show($id){
        $company = Company::findOrFail($id);
        return view('companies.show', compact('company'));
    }

    public function edit($id){
        $company = Company::find($id);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company){
        $validated = $request->validate([
            'nit' => 'required|unique:companies,nit,' . $company->id,
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'department' => 'required',
            'municipality' => 'required',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Empresa actualizada exitosamente.');
    }

    public function destroy(Company $company){
        $company->delete();
        return redirect()->route('companies.index')
            ->with('success', 'Empresa eliminada exitosamente.');
    }
}

