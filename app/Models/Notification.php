<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'is_read',
        'data',
        'user_id',
        'admin_id',
        'action_url',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with the user who triggered the notification
     */
    public function user()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    /**
     * Relationship with the admin who should receive the notification
     */
    public function admin()
    {
        return $this->belongsTo(Utilisateur::class, 'admin_id');
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get notifications for admin
     */
    public function scopeForAdmin($query, $adminId = null)
    {
        if ($adminId) {
            return $query->where('admin_id', $adminId);
        }
        // Retourner toutes les notifications destinées aux admins (admin_id null = pour tous les admins)
        return $query->whereNull('admin_id');
    }

    /**
     * Scope to get notifications by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Get formatted time difference
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a new notification
     */
    public static function createNotification($data)
    {
        return self::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'icon' => $data['icon'] ?? 'fas fa-bell',
            'color' => $data['color'] ?? 'primary',
            'data' => $data['data'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'admin_id' => $data['admin_id'] ?? null,
            'action_url' => $data['action_url'] ?? null,
        ]);
    }

    /**
     * Create notification for assistant actions
     */
    public static function notifyAdminOfAssistantAction($type, $title, $message, $assistantId, $data = null, $actionUrl = null)
    {
        $iconMap = [
            'module' => 'fas fa-book',
            'cours_domicile' => 'fas fa-home',
            'session' => 'fas fa-calendar-plus',
            'inscription' => 'fas fa-user-plus',
            'document' => 'fas fa-file-alt',
            'certificat' => 'fas fa-certificate',
            'questionnaire' => 'fas fa-question-circle',
        ];

        $colorMap = [
            'module' => 'info',
            'cours_domicile' => 'success',
            'session' => 'primary',
            'inscription' => 'success',
            'document' => 'info',
            'certificat' => 'warning',
            'questionnaire' => 'secondary',
        ];

        return self::createNotification([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $iconMap[$type] ?? 'fas fa-bell',
            'color' => $colorMap[$type] ?? 'primary',
            'user_id' => $assistantId,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }
}