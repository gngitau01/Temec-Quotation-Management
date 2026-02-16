<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // Web CRUD Methods
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'factory_number' => 'required|string|unique:customers',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'town' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
        ]);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('status', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'factory_number' => 'required|string|unique:customers,factory_number,' . $customer->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'town' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'contact_person' => 'required|string|max:255',
        ]);

        $customer->update($validated);
        return redirect()->route('customers.show', $customer)->with('status', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('status', 'Customer deleted successfully.');
    }

    // API Methods (legacy)
    public function listCustomers()
    {
        $customers = Customer::all();
        return response()->json([
            'data' => $customers
        ], 200);
    }

    // Create a new customer
    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'factory_number' => 'required|string|unique:customers,factory_number',
            'name'           => 'required|string',
            'address'        => 'nullable|string',
            'town'           => 'nullable|string',
            'phone_number'   => 'nullable|string',
            'contact_person' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201);
    }

    // Search customers
    public function search(Request $request)
    {
        $query = Customer::query();

        if ($request->factory_number) {
            $query->where('factory_number', 'like', '%' . $request->factory_number . '%');
        }

        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->phone_number) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        return response()->json($query->get());
    }
    public function editCustomer(Request $request, $id): JsonResponse
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'factory_number' => 'sometimes|required|string|unique:customers,factory_number,' . $id,
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'town' => 'sometimes|required|string|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'contact_person' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $customer,
            'message' => 'Customer updated successfully'
        ]);
    }
    public function deleteCustomer($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully'
        ], 200);
    }
}
