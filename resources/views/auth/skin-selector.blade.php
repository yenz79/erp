<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Choose Your Style</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .selector-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header-section p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .skin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .skin-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .skin-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .skin-card.active {
            border-color: #28a745;
            background: rgba(255, 255, 255, 1);
        }

        .skin-card.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .skin-preview {
            width: 100%;
            height: 200px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #6c757d;
            position: relative;
            overflow: hidden;
        }

        .skin-preview.modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .skin-preview.classic {
            background: #f8f9fa;
            color: #007bff;
            border: 2px solid #e9ecef;
        }

        .skin-preview.dark {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ff6b6b;
        }

        .skin-preview.minimal {
            background: #ffffff;
            color: #333333;
            border: 1px solid #e0e0e0;
        }

        .skin-name {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .skin-description {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .skin-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .feature-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .select-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #007bff;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .select-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .select-btn.selected {
            background: #28a745;
        }

        .select-btn.selected:hover {
            background: #218838;
        }

        .action-buttons {
            text-align: center;
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-action {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary-action:hover {
            background: white;
            color: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary-action {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary-action:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        .current-skin {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 2rem;
            }

            .skin-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .action-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .selector-container {
                padding: 15px;
            }
        }

        /* Loading Animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="selector-container">
        <!-- Header -->
        <div class="header-section">
            <h1><i class="fas fa-palette me-3"></i>Choose Your Style</h1>
            <p>Pilih tampilan yang sesuai dengan preferensi Anda</p>
        </div>

        <!-- Skin Grid -->
        <div class="skin-grid">
            @foreach($skins as $key => $skin)
            <div class="skin-card {{ $currentSkin === $key ? 'active' : '' }}" data-skin="{{ $key }}">
                @if($currentSkin === $key)
                    <div class="current-skin">
                        <i class="fas fa-check me-1"></i>Current
                    </div>
                @endif
                
                <div class="skin-preview {{ $key }}">
                    <i class="fas fa-store"></i>
                </div>
                
                <h3 class="skin-name">{{ $skin['name'] }}</h3>
                <p class="skin-description">{{ $skin['description'] }}</p>
                
                <div class="skin-features">
                    @foreach($skin['features'] as $feature)
                        <span class="feature-badge">{{ $feature }}</span>
                    @endforeach
                </div>
                
                <button class="select-btn {{ $currentSkin === $key ? 'selected' : '' }}" 
                        onclick="selectSkin('{{ $key }}', this)">
                    @if($currentSkin === $key)
                        <i class="fas fa-check me-2"></i>Selected
                    @else
                        <i class="fas fa-mouse-pointer me-2"></i>Select This Style
                    @endif
                </button>
            </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('login') }}" class="action-btn btn-primary-action">
                <i class="fas fa-sign-in-alt"></i>
                Continue to Login
            </a>
            
            @auth
                <a href="{{ route('dashboard') }}" class="action-btn btn-secondary-action">
                    <i class="fas fa-tachometer-alt"></i>
                    Back to Dashboard
                </a>
            @endauth
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedSkin = '{{ $currentSkin }}';

        function selectSkin(skinKey, button) {
            // Show loading state
            const originalContent = button.innerHTML;
            button.innerHTML = '<div class="spinner"></div>';
            button.classList.add('loading');

            // Make AJAX request to save skin preference
            fetch('{{ route("skin.set") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    skin: skinKey
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI
                    updateSkinSelection(skinKey);
                    selectedSkin = skinKey;
                    
                    // Show success message
                    showNotification('Skin berhasil diubah ke ' + data.skin_name, 'success');
                } else {
                    throw new Error('Failed to save skin preference');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Gagal mengubah skin. Silakan coba lagi.', 'error');
            })
            .finally(() => {
                // Restore button state
                button.classList.remove('loading');
                updateButtonStates();
            });
        }

        function updateSkinSelection(newSkin) {
            // Remove active class from all cards
            document.querySelectorAll('.skin-card').forEach(card => {
                card.classList.remove('active');
                const currentBadge = card.querySelector('.current-skin');
                if (currentBadge) {
                    currentBadge.remove();
                }
            });

            // Add active class to selected card
            const selectedCard = document.querySelector(`[data-skin="${newSkin}"]`);
            selectedCard.classList.add('active');
            
            // Add current badge
            const badge = document.createElement('div');
            badge.className = 'current-skin';
            badge.innerHTML = '<i class="fas fa-check me-1"></i>Current';
            selectedCard.appendChild(badge);
        }

        function updateButtonStates() {
            document.querySelectorAll('.select-btn').forEach(btn => {
                const card = btn.closest('.skin-card');
                const skinKey = card.dataset.skin;
                
                if (skinKey === selectedSkin) {
                    btn.innerHTML = '<i class="fas fa-check me-2"></i>Selected';
                    btn.classList.add('selected');
                } else {
                    btn.innerHTML = '<i class="fas fa-mouse-pointer me-2"></i>Select This Style';
                    btn.classList.remove('selected');
                }
            });
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 10px;
                color: white;
                font-weight: 500;
                z-index: 9999;
                animation: slideIn 0.3s ease;
                ${type === 'success' ? 'background: #28a745;' : 'background: #dc3545;'}
            `;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Add CSS for notification animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Add hover effects for skin cards
        document.querySelectorAll('.skin-card').forEach(card => {
            card.addEventListener('click', function() {
                const skinKey = this.dataset.skin;
                const button = this.querySelector('.select-btn');
                if (skinKey !== selectedSkin) {
                    selectSkin(skinKey, button);
                }
            });
        });
    </script>
</body>
</html>
