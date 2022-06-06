<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Validator;
use App\Services\ThetaService;
use Illuminate\Validation\Rule;

class NetworkController extends Controller
{

    private $thetaService;

    public function __construct(ThetaService $thetaService) {
        $this->thetaService = $thetaService;
    }

    public function validators()
    {
        $validators = Validator::all()->sortByDesc('amount');
        return view('admin.network.validators', [
            'validators' => $validators
        ]);
    }

    public function addValidator()
    {
        if (request()->isMethod('post')) {
            request()->validate([
                'holder' => ['bail', 'required', 'string', 'unique:validators'],
                'name' => ['bail', 'required', 'string'],
                'amount' => ['bail', 'required', 'numeric']
            ]);
            $data = request()->only('holder', 'name', 'amount');
            $data['chain'] = 'theta';
            $data['coin'] = 'theta';
            Validator::create($data);
            $this->thetaService->cacheValidators();
            return back()->with('message', 'Added successfully.');
        }
        return view('admin.network.add_validator');
    }

    public function editValidator($id)
    {
        $validator = Validator::find($id);
        if (request()->isMethod('post')) {
            request()->validate([
                'holder' => ['bail', 'required', 'string', Rule::unique('validators')->ignore($validator->holder, 'holder')],
                'name' => ['bail', 'required', 'string'],
                'amount' => ['bail', 'required', 'numeric']
            ]);
            $data = request()->only('holder', 'name', 'amount');
            $validator->update($data);
            $this->thetaService->cacheValidators();
            return back()->with('message', 'Edited successfully.');
        }
        return view('admin.network.edit_validator', ['validator' => $validator]);
    }

    public function deleteValidator($id)
    {
        Validator::destroy($id);
        return back();
    }
}
