<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['Seller', 'Admin'], true);
    }

    public function rules(): array
    {
        $year = (int) date('Y') + 1;
        return [
            'make'             => 'sometimes|string|max:50',
            'model'            => 'sometimes|string|max:50',
            'year'             => "sometimes|integer|min:1900|max:$year",
            'mileage'          => 'sometimes|integer|min:0',
            'price'            => 'sometimes|numeric|min:1|max:100000000',
            'fuel_type'        => 'sometimes|in:Petrol,Diesel,Hybrid,Electric',
            'transmission'     => 'sometimes|in:Manual,Automatic,CVT',
            'condition_status' => 'sometimes|in:New,Used',
            'description'      => 'nullable|string|max:1000',
            'category_id'      => 'nullable|exists:categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'integer|exists:tags,id',
            'status'           => 'sometimes|in:Pending,Active,Sold,Rejected',
        ];
    }
}
