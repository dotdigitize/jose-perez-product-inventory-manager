<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ProductRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['name'=>['required','string','max:120'],'quantity'=>['required','integer','min:0'],'price'=>['required','regex:/^\d+(\.\d{1,2})?$/','numeric','min:0']]; } public function messages(): array { return ['quantity.integer'=>'Quantity must be a whole number.','price.regex'=>'Price must be a nonnegative amount with up to two decimal places.']; } }
