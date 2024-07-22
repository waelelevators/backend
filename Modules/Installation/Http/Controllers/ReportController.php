<?php

namespace Modules\Installation\Http\Controllers;

use App\Models\Contract;
use App\Models\InstallationLocationDetection;
use App\Models\InstallationQuotation;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    function countContracts()
    {
        $Quotations = InstallationQuotation::count();
        $installationLocationDetections = InstallationLocationDetection::count();
        $SignedContracts = Contract::where('contract_status', 'assigned')->count();
        $NotSignedContracts = Contract::where('contract_status', 'Draft')->count();


        return response()->json([
            'installationLocationDetections' => $installationLocationDetections,
            'Quotations' => $Quotations,
            'SignedContracts' => $SignedContracts,
            'NotSignedContracts' => $NotSignedContracts
        ]);
    }
}
