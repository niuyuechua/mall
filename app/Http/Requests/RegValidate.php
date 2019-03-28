<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account'=>[
                'required',
                'unique:users',
                'regex:/^\w+@+\w+\.com$/',
                //Rule::unique('web')->ignore(request()->id, 'id'),
            ],
            'code'=>[
                'required',
            ],
            'pwd'=>[
                'required',
                'regex:/^\w{6,18}$/',
            ],
        ];
    }
    public function messages(){
        return [
            'account.required' => '邮箱必填',
            'account.regex'=>'邮箱格式不正确',
            'account.unique'=>'邮箱已注册',
            'code.required'=>'验证码必填',
            'pwd.required'=>'密码必填',
            'pwd.regex'=>'密码必须为字母、数字、下划线，6到18位',
        ];
    }
}
