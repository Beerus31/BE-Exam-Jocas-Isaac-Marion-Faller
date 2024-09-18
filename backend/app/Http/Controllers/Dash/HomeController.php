<?php

namespace App\Http\Controllers\Dash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Products;

class HomeController extends Controller
{


    public function testMethod(Request $request)
    {
        // Dummy response 
        return response()->json([
            'status' => 200,
            'message' => 'Test API route is working!'
        ], 200);
    }
    // Function to fetch and return all products
    
    public function index(Request $request)
{
    try {
        $perPage = $request->input('per_page', 10); // Number of items per page
        $searchQuery = $request->input('search', ''); // Search query
        $category = $request->input('filter', ''); // Selected category
        
        $query = Products::query();
        
        // Apply search filter
        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%$searchQuery%")
                  ->orWhere('description', 'LIKE', "%$searchQuery%");
            });
        }
        
        // Apply category filter
        if (!empty($category)) {
            $query->where('category', $category);
        }
        
        $products = $query->paginate($perPage); // Paginate the products

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'next_page_url' => $products->nextPageUrl(),
                'prev_page_url' => $products->previousPageUrl(),
            ],
            'status' => true
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Error fetching products',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Function to create a new product
    public function create(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:225',
            'category' => 'required|string|max:225',
            'description' => 'nullable|string|max:225',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate as an image file
            'date_time' => 'nullable|date'
        ]);

        try {
            $product = new Products(); // Create a new product instance
            $product->name = $request->name;
            $product->category = $request->category;
            $product->description = $request->description;

            // Handle file upload if a product image is provided
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename); // Move file to 'public/images' directory
                $product->product_image = $filename;
            }

            $product->date_time = $request->date_time ?? now(); // Use provided date_time or set to current time
            $product->save(); // Save the product to the database

            return response()->json([
                'status' => 201,
                'message' => 'Product Added Successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during product creation
            return response()->json([
                'status' => 500,
                'message' => 'Error inserting product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Function to show details of a specific product by ID
    public function show($id)
    {
        $product = Products::find($id); // Find product by ID
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "nothing found"
            ], 404);
        }
    }

    public function edit($id)
    {
        $product = Products::find($id); // Find product by ID
        if ($product) {
            return response()->json([
                'status' => 200,
                'product' => $product
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "nothing found"
            ], 404);
        }
    }
    
    // Function to update an existing product by ID
    public function update(Request $request, int $id)
    {
        // Validate incoming request data
        $request->validate([
            'name' => 'required|string|max:225',
            'category' => 'required|string|max:225',
            'description' => 'nullable|string|max:225',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate as an image file
            'date_time' => 'nullable|date',
        ]);
    
        try {
            $product = Products::find($id); // Find product by ID
    
            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found'
                ], 404);
            }
    
            // Update product details
            $product->name = $request->name;
            $product->category = $request->category;
            $product->description = $request->description;
    
            // Handle file upload if a new product image is provided
            if ($request->hasFile('product_image')) {
     
                $file = $request->file('product_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                // Move file to 'public/images' directory
                $file->move(public_path('images'), $filename); 
                // Update the image filename in the database
                $product->product_image = $filename; 
            }
            
            // Keep old date if not provid
            $product->date_time = $request->date_time ?? $product->date_time; 
            // Save the updated product to the database
            $product->save(); 
    
            return response()->json([
                'status' => 200,
                'message' => 'Product updated successfully',
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during product update
            return response()->json([
                'status' => 500,
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // Function to delete a product by ID
    public function destroy($id)
    {
        // Find product by ID
        $product = Products::find($id); 
        if ($product) {
            // Delete the product from the database
            $product->delete(); 
            return response()->json([
                'status' => 200,
                'message' => 'Product deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No such product found'
            ], 404);
        }
    }
}
