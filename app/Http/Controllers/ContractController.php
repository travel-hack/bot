<?php

namespace App\Http\Controllers;

use App\Contract;
use Illuminate\Http\Request;
use App\Player;

class ContractController extends Controller
{
    public function allContracts() 
    {        
        return Contract::with(['player','booking'])->orderBy('updated_at', 'desc')->get();
    }

    public function getContract(string $contract_id)
    {
        return Contract::find($contract_id);
    }

    public function updateContract(string $contract_id, Request $request)
    {
        $contract = Contract::find($contract_id);

        $contract->update($request->all());

        if ($contract->save()) {
            return $contract;
        }
    }

    public function cancelContract(string $contract_id)
    {
        $contract = Contract::find($contract_id); 
        
        $contract->update('status', 'cancelled');
        
        if($contract->save()) {
            return $contract;
        }
    }  
}
