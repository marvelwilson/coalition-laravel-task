<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $jsonFile = 'products.json';

    public function index()
    {
        return view('welcome');
    }

    public function addProduct(Request $request)
    {
        $data = $request->all();
        $data['datetime'] = now()->toDateTimeString();
        $data['total_value'] = $data['quantity'] * $data['price'];
        
        // Read existing data
        $products = json_decode(file_get_contents(storage_path($this->jsonFile)), true) ?? [];
        $products[] = $data;
        
        // Save new data
        file_put_contents(storage_path($this->jsonFile), json_encode($products, JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }

    public function getProducts()
    {
        $products = json_decode(file_get_contents(storage_path($this->jsonFile)), true) ?? [];
        return response()->json($products);
    }

    public function editProduct(Request $request)
{
    $products = json_decode(file_get_contents(storage_path($this->jsonFile)), true);
    $index = $request->input('index'); // Get index from request
    
    if (isset($products[$index])) {
        // Update product details
        $products[$index]['productName'] = $request->input('productName');
        $products[$index]['quantity'] = $request->input('quantity');
        $products[$index]['price'] = $request->input('price');
        $products[$index]['datetime'] = now()->toDateTimeString(); // Update timestamp
        $products[$index]['total_value'] = $products[$index]['quantity'] * $products[$index]['price'];

        // Save updated data
        file_put_contents(storage_path($this->jsonFile), json_encode($products, JSON_PRETTY_PRINT));
    }

    return response()->json(['success' => true]);
}
}
