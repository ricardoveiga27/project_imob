<?php

namespace App\Http\Controllers\Admin;

use App\Contract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract as ContractRequest;
use App\Property;
use App\User;
use Illuminate\Http\Request;
use SebastianBergmann\Environment\Console;
use Symfony\Component\Console\Logger\ConsoleLogger;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::orderBy('id', 'DESC')->get();

        return view('admin.contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lessors = User::lessors();
        $lessees = User::lessees();

        return view('admin.contracts.create', compact('lessors', 'lessees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractRequest $request)
    {
        $contractCreate = Contract::create($request->all());

        return redirect()->route('admin.contracts.edit', [
            'contract' => $contractCreate->id
        ])->with(['color' => 'green', 'message' => 'Contrato cadastrado com sucesso!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contract = Contract::where('id', $id)->first();

        $lessors = User::lessors();
        $lessees = User::lessees();

        return view('admin.contracts.edit', compact('contract', 'lessors', 'lessees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContractRequest $request, $id)
    {
        $contract = Contract::where('id', $id)->first();
        $contract->fill($request->all());
        $contract->save();

        if($request->property) {
            $property = Property::where('id', $request->property)->first();

            if( $request->status === 'active') {
                $property->status = '0';
                $property->save();
            } else {
                $property->status = '1';
                $property->save();
            }
        }

        return redirect()->route('admin.contracts.edit', [
            'contract' => $contract->id
        ])->with(['color' => 'green', 'message' => 'Contrato editado com sucesso!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getDataOwner(Request $request)
    {
        $lessor = User::where('id', $request->user)->first([
            'id',
            'civil_status',
            'spouse_name',
            'spouse_document'
        ]);

        if(empty($lessor)) {
            $spouse = null;
            $companies = null;
            $properties = null;
        } else {
            $civilStatusSpouseRequired = [
                'married',
                'separated'
            ];

            if(in_array($lessor->civil_status, $civilStatusSpouseRequired)) {
                $spouse = [
                    'spouse_name' => $lessor->spouse_name,
                    'spouse_document' => $lessor->spouse_document
                ];
            } else {
                $spouse = null;
            }

            $companies = $lessor->companies()->get([
                'id',
                'alias_name',
                'document_company'
            ]);

            $getProperties = $lessor->properties()->get();
            $property =[];

            foreach($getProperties as $property) {
                $properties[] = [
                    'id' => $property->id,
                    'description' => '#' . $property->id . ' ' . $property->street . ',' . $property->number .
                        ' ' . $property->complement . ' ' . $property->neighborhood . ' ' . $property->city .
                        '/' . $property->state . '(' . $property->zipcode . ')'
                ];
            }
        }

        $json['spouse'] = $spouse;
        $json['companies'] = (!empty($companies) && $companies->count() ? $companies : null);
        $json['properties'] = (!empty($properties) ? $properties : null);

        return response()->json($json);
    }

    public function getDataAcquirer(Request $request)
    {
        $lessee = User::where('id', $request->user)->first([
            'id',
            'civil_status',
            'spouse_name',
            'spouse_document'
        ]);

        if(empty($lessee)) {
            $spouse = null;
            $companies = null;

        } else {
            $civilStatusSpouseRequired = [
                'married',
                'separated'
            ];

            if(in_array($lessee->civil_status, $civilStatusSpouseRequired)) {
                $spouse = [
                    'spouse_name' => $lessee->spouse_name,
                    'spouse_document' => $lessee->spouse_document
                ];
            } else {
                $spouse = null;
            }

            $companies = $lessee->companies()->get([
                'id',
                'alias_name',
                'document_company'
            ]);
        }

        $json['spouse'] = $spouse;
        $json['companies'] = (!empty($companies) && $companies->count() ? $companies : null);

        return response()->json($json);
    }

    public function getDataProperty(Request $request)
    {
        $property = Property::where('id', $request->property)->first();

        if(empty($property)) {
            $property = null;
        } else {
            $property = [
                'id' => $property->id,
                'sale_price' => $property->sale_price,
                'rent_price' => $property->rent_price,
                'tribute' => $property->tribute,
                'condominium' => $property->condominium
            ];
        }

        $json['property'] = $property;

        return response()->json($json);
    }
}
