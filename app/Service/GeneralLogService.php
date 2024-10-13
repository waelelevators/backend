<?php

namespace App\Service;

use App\Models\GeneralLog;

class GeneralLogService
{
    public static function log($loggable, $action, $comment = null, $metadata = [])
    {
        GeneralLog::create([
            'loggable_type' => get_class($loggable),
            'loggable_id' => $loggable->id,
            'action' => $action,
            'comment' => $comment,
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
        ]);
    }
}
