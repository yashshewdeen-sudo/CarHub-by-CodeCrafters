<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['Seller', 'Admin'], true);
    }

    public function rules(): array
    {
        $year = (int) date('Y') + 1;
        return [
            'make'             => 'required|string|max:50',
            'model'            => 'required|string|max:50',
            'year'             => "required|integer|min:1900|max:$year",
            'mileage'          => 'required|integer|min:0',
            'price'            => 'required|numeric|min:1|max:100000000',
            'fuel_type'        => 'required|in:Petrol,Diesel,Hybrid,Electric',
            'transmission'     => 'required|in:Manual,Automatic,CVT',
            'condition_status' => 'required|in:New,Used',
            'description'      => 'nullable|string|max:1000',
            'category_id'      => 'nullable|exists:categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'integer|exists:tags,id',
            'images'   => 'nullable|array|max:5',
            'images.*' => 'nullable|image|max:5120',
        ];
    
    }
}
