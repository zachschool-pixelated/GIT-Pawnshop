<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of all customers
     */
    public function index(Request $request)
    {
        $query = Customer::with('transactions');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $customers = $query->paginate(20);
        
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            $customer = Customer::create($request->validated());
            
            AuditService::logCreate($customer);
            
            return redirect()->route('customers.show', $customer->id)
                ->with('success', 'Customer created successfully!');
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to create customer: ' . $e->getMessage());
            return back()->with('error', 'Error creating customer: ' . $e->getMessage());
        }
    }

    /**
     * Display a specific customer
     */
    public function show(Customer $customer)
    {
        $customer->load('transactions', 'items');
        $auditLogs = AuditService::getLogsForModel('Customer', $customer->id);
        
        return view('customers.show', compact('customer', 'auditLogs'));
    }

    /**
     * Show the form for editing a customer
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update a customer in storage
     */
    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        try {
            $oldValues = $customer->toArray();
            
            $customer->update($request->validated());
            
            AuditService::logUpdate($customer, $oldValues);
            
            return redirect()->route('customers.show', $customer->id)
                ->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to update customer: ' . $e->getMessage());
            return back()->with('error', 'Error updating customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove a customer from storage
     */
    public function destroy(Customer $customer)
    {
        try {
            AuditService::logDelete($customer);
            
            $customer->delete();
            
            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully!');
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to delete customer: ' . $e->getMessage());
            return back()->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    /**
     * Get customer details via AJAX
     */
    public function getDetails($customerId)
    {
        $customer = Customer::with('transactions')->findOrFail($customerId);
        
        return response()->json([
            'id' => $customer->id,
            'name' => $customer->full_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'id_type' => $customer->id_type,
            'id_number' => $customer->id_number,
            'occupation' => $customer->occupation,
            'total_transactions' => $customer->transactions->count(),
            'active_transactions' => $customer->transactions->where('status', 'active')->count(),
        ]);
    }
}

