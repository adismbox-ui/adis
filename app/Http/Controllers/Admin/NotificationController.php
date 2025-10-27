<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display the notifications page
     */
    public function index()
    {
        $notifications = \App\Models\Notification::forAdmin()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at,
                    'data' => $notification->data ?? []
                ];
            })->toArray();
        
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get notifications data (mock data for now)
     */
    private function getNotifications()
    {
        // Mock notifications data - replace with actual database queries
        return [
            [
                'id' => 1,
                'type' => 'inscription',
                'title' => 'Nouvelle inscription reçue',
                'message' => 'Un nouvel apprenant s\'est inscrit à la formation "Développement Web"',
                'icon' => 'fas fa-user-plus',
                'color' => 'success',
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(2),
                'data' => [
                    'user_name' => 'Jean Dupont',
                    'formation' => 'Développement Web'
                ]
            ],
            [
                'id' => 2,
                'type' => 'payment',
                'title' => 'Paiement en attente',
                'message' => 'Un paiement de 500€ est en attente de validation',
                'icon' => 'fas fa-credit-card',
                'color' => 'warning',
                'is_read' => false,
                'created_at' => Carbon::now()->subMinutes(15),
                'data' => [
                    'amount' => 500,
                    'user_name' => 'Marie Martin'
                ]
            ],
            [
                'id' => 3,
                'type' => 'session',
                'title' => 'Session créée',
                'message' => 'Une nouvelle session de formation a été programmée',
                'icon' => 'fas fa-calendar-plus',
                'color' => 'info',
                'is_read' => true,
                'created_at' => Carbon::now()->subHour(1),
                'data' => [
                    'session_name' => 'Formation Laravel Avancé',
                    'date' => '2025-07-25'
                ]
            ],
            [
                'id' => 4,
                'type' => 'document',
                'title' => 'Nouveau document',
                'message' => 'Un certificat a été généré automatiquement',
                'icon' => 'fas fa-file-alt',
                'color' => 'primary',
                'is_read' => true,
                'created_at' => Carbon::now()->subHours(3),
                'data' => [
                    'document_type' => 'Certificat',
                    'user_name' => 'Pierre Durand'
                ]
            ],
            [
                'id' => 5,
                'type' => 'system',
                'title' => 'Mise à jour système',
                'message' => 'Le système a été mis à jour avec succès',
                'icon' => 'fas fa-cog',
                'color' => 'secondary',
                'is_read' => true,
                'created_at' => Carbon::now()->subDay(1),
                'data' => [
                    'version' => '2.1.0'
                ]
            ]
        ];
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = \App\Models\Notification::find($id);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notification non trouvée'
        ], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        \App\Models\Notification::forAdmin()
            ->unread()
            ->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues'
        ]);
    }

    /**
     * Delete notification
     */
    public function delete(Request $request, $id)
    {
        $notification = \App\Models\Notification::find($id);
        
        if ($notification) {
            $notification->delete();
            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notification non trouvée'
        ], 404);
    }

    /**
     * Get notifications for AJAX requests (sidebar)
     */
    public function getNotificationsAjax(): JsonResponse
    {
        $notifications = \App\Models\Notification::forAdmin()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5) // Only show latest 5 for sidebar
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->toISOString(),
                    'data' => $notification->data ?? []
                ];
            });

        $unreadCount = \App\Models\Notification::forAdmin()->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        $notifications = $this->getNotifications();
        
        $stats = [
            'total' => count($notifications),
            'unread' => count(array_filter($notifications, fn($n) => !$n['is_read'])),
            'today' => count(array_filter($notifications, fn($n) => $n['created_at']->isToday())),
            'this_week' => count(array_filter($notifications, fn($n) => $n['created_at']->isCurrentWeek()))
        ];

        return $stats;
    }
}
