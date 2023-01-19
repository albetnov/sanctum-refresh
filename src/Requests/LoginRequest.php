<?php

namespace Albet\SanctumRefresh\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => [Rule::requiredIf(fn () => ! isset($this->email)), 'string'],
            'email' => [Rule::requiredIf(fn () => ! isset($this->username)), 'email'],
            'password' => 'required',
        ];
    }

    public function auth(): User|bool
    {
        if ($this->has('email')) {
            $attemptPayload = $this->only('email', 'password');
        } else {
            $attemptPayload = $this->only('username', 'password');
        }

        if (! Auth::attempt($attemptPayload)) {
            return false;
        }

        return User::where('email', $this->email)->first();
    }
}
