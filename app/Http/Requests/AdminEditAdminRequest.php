<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Admin;

class AdminEditAdminRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$admin = Admin::find($this->id);
		return [
			'admin_name' => 'required|string|max:255',
			'admin_email' => 'required|string|email|max:255|unique:admins,admin_email,'.$admin->admin_email.',admin_email',
		];
	}
}
