<?php

namespace App\Validators;

use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResepValidator
{
    private static $rules;
    private static $messages;

    private static function init()
    {
        self::$rules = [
            'nama_resep' => 'required',
            'desc_resep' => 'nullable',
            'langkah' => 'required',
            'desc_bahan' => 'required|array',
        ];

        self::$messages = [
            'nama_resep.required' => 'Nama Resep Wajib diisi',
            'langkah.required' => 'Langkah-langkah harus diisi',
            'desc_bahan.required' => 'Bahan harus dimasukan'
        ];
    }

    public static function validate(Request $request) : array
    {
        self::init();
        $validator = Validator::make($request->all(), self::$rules, self::$messages);
        if($validator->fails()){
            $error = implode(", ", array_map('implode', array_values($validator->errors()->messages())));
            
            throw new ValidationException($error);
        }

        return $validator->safe()->except(['desc_bahan']);
    }
}
