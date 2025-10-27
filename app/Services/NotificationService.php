<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Notify admin when assistant creates a new module
     */
    public static function notifyModuleCreated($moduleData, $assistantId = null)
    {
        $assistantId = $assistantId ?? Auth::id();
        $assistantName = Auth::user()->prenom . ' ' . Auth::user()->nom;

        return Notification::notifyAdminOfAssistantAction(
            'module',
            'Nouveau module créé',
            "L'assistant {$assistantName} a créé un nouveau module : {$moduleData['nom']}",
            $assistantId,
            [
                'module_name' => $moduleData['nom'],
                'assistant_name' => $assistantName,
                'module_id' => $moduleData['id'] ?? null
            ]
        );
    }

    /**
     * Notify admin when assistant creates cours à domicile
     */
    public static function notifyCoursaDomicileCreated($coursData, $assistantId = null)
    {
        $assistantId = $assistantId ?? Auth::id();
        $assistantName = Auth::user()->prenom . ' ' . Auth::user()->nom;

        return Notification::notifyAdminOfAssistantAction(
            'cours_domicile',
            'Nouveau cours à domicile',
            "L'assistant {$assistantName} a programmé un nouveau cours à domicile",
            $assistantId,
            [
                'assistant_name' => $assistantName,
                'cours_id' => $coursData['id'] ?? null
            ]
        );
    }

    /**
     * Generic notification for any assistant action
     */
    public static function notifyAssistantAction($type, $title, $message, $data = [])
    {
        $assistantId = Auth::id();
        $assistantName = Auth::user()->prenom . ' ' . Auth::user()->nom;

        return Notification::notifyAdminOfAssistantAction(
            $type,
            $title,
            $message,
            $assistantId,
            array_merge($data, ['assistant_name' => $assistantName])
        );
    }
}
