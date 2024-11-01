<?php

namespace App\Http\Controllers;

use App\Helpers\MyHelper;
use App\Http\Resources\ContractResource;
use App\Models\Client;
use App\Models\Contract;
use Illuminate\Http\Request;

class SignedContractsController extends Controller
{

    public function signed()
    {

        //$contracts =  Contract::orderByDesc('created_at')->get();

        $contracts = Contract::where(
            'contract_status',
            'assigned'
        )->get();

        return ContractResource::collection($contracts);

        //  return Contract::where('status', 'signed')->get();
    }

    public function installed()
    {

        $contracts = Contract::where('stage_id', 3)->get(); // تم الانتهاء من تركيب المصعد  

        return ContractResource::collection($contracts);
    }
}
