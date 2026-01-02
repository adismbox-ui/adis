<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class MobileAppController extends Controller
{
    /**
     * Affiche la page de téléchargement de l'application mobile
     */
    public function downloadPage()
    {
        return view('download-app');
    }

    /**
     * Télécharge le fichier APK de l'application mobile
     */
    public function download()
    {
        // Chemin du fichier APK dans le dossier public
        // Vous pouvez placer votre fichier APK dans public/app/adis-mobile.apk
        $apkPath = public_path('app/adis-mobile.apk');
        
        // Si le fichier n'existe pas dans public/app, chercher dans storage
        if (!file_exists($apkPath)) {
            $apkPath = storage_path('app/public/adis-mobile.apk');
        }
        
        // Si le fichier existe, le télécharger
        if (file_exists($apkPath)) {
            $fileName = 'adis-mobile.apk';
            
            return Response::download($apkPath, $fileName, [
                'Content-Type' => 'application/vnd.android.package-archive',
            ]);
        }
        
        // Si le fichier n'existe pas, rediriger vers une page d'information
        // ou vers Google Play Store si disponible
        return redirect()->route('download-app.page')->with('error', 'L\'application n\'est pas encore disponible pour le téléchargement.');
    }

    /**
     * Redirige vers Google Play Store ou App Store selon la plateforme
     */
    public function redirectToStore()
    {
        // Détecter la plateforme de l'utilisateur
        $userAgent = request()->header('User-Agent');
        
        if (preg_match('/android/i', $userAgent)) {
            // Lien Google Play Store (à remplacer par votre lien réel)
            $playStoreUrl = 'https://play.google.com/store/apps/details?id=com.example.adis_mobile';
            return redirect($playStoreUrl);
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            // Lien App Store (à remplacer par votre lien réel)
            $appStoreUrl = 'https://apps.apple.com/app/adis-mobile';
            return redirect($appStoreUrl);
        }
        
        // Par défaut, rediriger vers la page de téléchargement
        return redirect()->route('download-app.page');
    }
}

