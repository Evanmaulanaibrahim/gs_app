<?php

namespace App\Http\Controllers\Api;

use App\Models\RFIDTag;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RFIDAccessController extends Controller
{
    public function checkAccess(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'device_id' => 'required|string',
        ]);
        
        $tag = RFIDTag::where('uid', $request->uid)->first();
        
        $accessGranted = false;
        if ($tag && $tag->hasAccess()) {
            $accessGranted = true;
        }
        
        // Log the access attempt
        AccessLog::create([
            'rfid_tag_id' => $tag ? $tag->id : null,
            'access_granted' => $accessGranted,
            'device_id' => $request->device_id,
        ]);
        
        return response()->json([
            'access_granted' => $accessGranted,
            'name' => $tag ? $tag->name : 'Unknown',
            'position' => $tag ? $tag->position : 'Unknown',
        ]);
    }
}