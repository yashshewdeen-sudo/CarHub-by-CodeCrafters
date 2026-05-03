<?php

<?php

namespace App\Http\Controllers;

use JsonSchema\Validator;
use Illuminate\Http\Request;
use App\Models\Listing;

class ApiController extends Controller
{
    public function validateCarData(Request $request)
    {
        $data = $request->json()->all();
        
        // Define JSON Schema for car validation
        $schema = [
            'type' => 'object',
            'properties' => [
                'make' => [
                    'type' => 'string',
                    'minLength' => 2,
                    'maxLength' => 50
                ],
                'model' => [
                    'type' => 'string',
                    'minLength' => 2,
                    'maxLength' => 50
                ],
                'year' => [
                    'type' => 'integer',
                    'minimum' => 1900,
                    'maximum' => 2024
                ],
                'price' => [
                    'type' => 'number',
                    'minimum' => 0,
                    'maximum' => 1000000
                ],
                'fuel_type' => [
                    'type' => 'string',
                    'enum' => ['Gasoline', 'Diesel', 'Electric', 'Hybrid']
                ],
                'transmission' => [
                    'type' => 'string',
                    'enum' => ['Manual', 'Automatic']
                ],
                'mileage' => [
                    'type' => 'integer',
                    'minimum' => 0,
                    'maximum' => 500000
                ],
                'description' => [
                    'type' => 'string',
                    'maxLength' => 1000
                ]
            ],
            'required' => ['make', 'model', 'year', 'price']
        ];
        
        // Validate against schema
        $validator = new Validator();
        $validator->validate($data, $schema);
        
        if ($validator->isValid()) {
            return response()->json([
                'success' => true,
                'message' => 'Car data is valid',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->getErrors()
            ], 422);
        }
    }
    
    public function createListing(Request $request)
    {
        $data = $request->json()->all();
        
        // Validate with JSON Schema
        $schema = [
            'type' => 'object',
            'properties' => [
                'make' => ['type' => 'string', 'minLength' => 2],
                'model' => ['type' => 'string', 'minLength' => 2],
                'year' => ['type' => 'integer', 'minimum' => 1900, 'maximum' => 2024],
                'price' => ['type' => 'number', 'minimum' => 0],
                'fuel_type' => ['type' => 'string'],
                'transmission' => ['type' => 'string'],
                'mileage' => ['type' => 'integer', 'minimum' => 0],
                'description' => ['type' => 'string'],
                'category_id' => ['type' => 'integer']
            ],
            'required' => ['make', 'model', 'year', 'price', 'category_id']
        ];
        
        $validator = new Validator();
        $validator->validate($data, $schema);
        
        if (!$validator->isValid()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getErrors()
            ], 422);
        }
        
        // Create listing
        $listing = Listing::create([
            'make' => $data['make'],
            'model' => $data['model'],
            'year' => $data['year'],
            'price' => $data['price'],
            'fuel_type' => $data['fuel_type'] ?? 'Gasoline',
            'transmission' => $data['transmission'] ?? 'Manual',
            'mileage' => $data['mileage'] ?? 0,
            'description' => $data['description'] ?? '',
            'category_id' => $data['category_id'],
            'user_id' => auth()->id(),
            'status' => 'Pending'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Listing created successfully',
            'listing' => $listing
        ]);
    }
}