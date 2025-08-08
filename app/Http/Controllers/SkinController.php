<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class SkinController extends Controller
{
    /**
     * Available skins configuration
     */
    protected $skins = [
        'modern' => [
            'name' => 'Modern',
            'description' => 'Dynamic modern design with glassmorphism',
            'template' => 'auth.login',
            'preview' => '/images/skin-modern.jpg',
            'features' => ['Glassmorphism', 'Animations', 'Gradient backgrounds']
        ],
        'classic' => [
            'name' => 'Classic',
            'description' => 'Traditional Bootstrap-based design',
            'template' => 'auth.login-classic',
            'preview' => '/images/skin-classic.jpg',
            'features' => ['Bootstrap styling', 'Clean layout', 'Professional look']
        ],
        'dark' => [
            'name' => 'Dark Mode',
            'description' => 'Dark theme for night usage',
            'template' => 'auth.login-dark',
            'preview' => '/images/skin-dark.jpg',
            'features' => ['Dark background', 'Eye-friendly', 'Modern accents']
        ],
        'minimal' => [
            'name' => 'Minimal',
            'description' => 'Ultra-clean minimalist design',
            'template' => 'auth.login-minimal',
            'preview' => '/images/skin-minimal.jpg',
            'features' => ['Ultra clean', 'No distractions', 'Fast loading']
        ]
    ];

    /**
     * Show skin selector
     */
    public function index()
    {
        $currentSkin = $this->getCurrentSkin();
        return view('auth.skin-selector', [
            'skins' => $this->skins,
            'currentSkin' => $currentSkin
        ]);
    }

    /**
     * Set user's preferred skin
     */
    public function setSkin(Request $request)
    {
        $request->validate([
            'skin' => 'required|in:' . implode(',', array_keys($this->skins))
        ]);

        $skin = $request->input('skin');
        
        // Save to user preferences if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $user->skin_preference = $skin;
            $user->save();
        }

        // Also save to cookie for non-authenticated users
        $cookie = cookie('erp_skin', $skin, 60 * 24 * 30); // 30 days

        return response()->json([
            'success' => true,
            'message' => 'Skin berhasil diubah',
            'skin' => $skin,
            'skin_name' => $this->skins[$skin]['name']
        ])->cookie($cookie);
    }

    /**
     * Get current user's skin preference
     */
    public function getCurrentSkin()
    {
        // Check user preference first
        if (Auth::check() && Auth::user()->skin_preference) {
            $skin = Auth::user()->skin_preference;
            if (array_key_exists($skin, $this->skins)) {
                return $skin;
            }
        }

        // Check cookie
        $skin = request()->cookie('erp_skin', 'modern');
        if (array_key_exists($skin, $this->skins)) {
            return $skin;
        }

        return 'modern'; // default
    }

    /**
     * Get skin configuration
     */
    public function getSkinConfig($skin = null)
    {
        $skin = $skin ?: $this->getCurrentSkin();
        return $this->skins[$skin] ?? $this->skins['modern'];
    }

    /**
     * Show login with selected skin
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $currentSkin = $this->getCurrentSkin();
        $template = $this->skins[$currentSkin]['template'];
        
        return view($template);
    }
}
