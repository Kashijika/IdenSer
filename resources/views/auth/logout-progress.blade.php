<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - IdenSer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
            box-sizing: border-box;
        }
        
        .logout-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 400px;
            position: relative;
            box-sizing: border-box;
        }
        
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid white;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .message {
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .sub-message {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .manual-redirect {
            margin-top: 20px;
            display: none;
        }
        
        .btn {
            background: white;
            color: #1e40af;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="spinner"></div>
        <div class="message">Logging out from all applications...</div>
        <div class="sub-message">Please wait while we securely terminate your session.</div>
        
        <div class="manual-redirect" id="manualRedirect">
            <p>Taking longer than expected?</p>
            <a href="{{ $login_url }}" class="btn">Go to Login Page</a>
        </div>
    </div>

    <!-- Hidden iframe for WSO2 logout -->
    <iframe id="wso2LogoutFrame" src="{{ $wso2_base_url }}/oidc/logout?post_logout_redirect_uri={{ urlencode($login_url . '?logged_out=1') }}@if($id_token)&id_token_hint={{ $id_token }}@endif" style="display: none;"></iframe>

    <script>
        let redirectTimer;
        let manualRedirectTimer;
        
        // Function to redirect to login page
        function redirectToLogin() {
            window.location.href = '{{ $login_url }}';
        }
        
        // Show manual redirect option after 10 seconds
        manualRedirectTimer = setTimeout(function() {
            document.getElementById('manualRedirect').style.display = 'block';
        }, 10000);
        
        // Automatic redirect after 5 seconds
        redirectTimer = setTimeout(redirectToLogin, 5000);
        
        // Listen for iframe load events (indicates WSO2 logout completed)
        document.getElementById('wso2LogoutFrame').onload = function() {
            // WSO2 logout completed, redirect sooner
            clearTimeout(redirectTimer);
            setTimeout(redirectToLogin, 2000);
        };
        
        // Fallback: redirect after maximum 15 seconds regardless
        setTimeout(redirectToLogin, 15000);
        
        // Handle page visibility changes (user might close/switch tabs)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // User switched tabs, complete logout in background
                setTimeout(redirectToLogin, 1000);
            }
        });
    </script>
</body>
</html>
            Logout process is taking longer than expected. You may need to manually clear your browser data.
        </div>
        
        <button class="manual-logout" id="manualLogout" onclick="forceRedirect()">
            Continue to Login
        </button>
    </div>

    <!-- Hidden iframe for WSO2 logout -->
    <iframe id="wso2LogoutFrame" class="hidden-iframe" src="about:blank"></iframe>

    <script>
        let progress = 0;
        let maxTime = 8000; // 8 seconds total
        let intervalTime = 100; // Update every 100ms
        let progressIncrement = (intervalTime / maxTime) * 100;
        
        let statusMessages = [
            "Initializing global logout process...",
            "Revoking access tokens...",
            "Terminating WSO2 session...",
            "Clearing all application sessions...",
            "Finalizing logout..."
        ];
        
        let currentMessageIndex = 0;
        let progressInterval;
        let messageInterval;
        
        function updateProgress() {
            progress += progressIncrement;
            
            if (progress > 100) {
                progress = 100;
            }
            
            document.getElementById('progressFill').style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(progressInterval);
                clearInterval(messageInterval);
                redirectToLogin();
            }
        }
        
        function updateMessage() {
            if (currentMessageIndex < statusMessages.length) {
                document.getElementById('statusText').textContent = statusMessages[currentMessageIndex];
                currentMessageIndex++;
            }
        }
        
        function redirectToLogin() {
            document.getElementById('statusText').textContent = "Global logout completed. Redirecting...";
            
            // Clear any remaining session data
            if (typeof(Storage) !== "undefined") {
                localStorage.clear();
                sessionStorage.clear();
            }
            
            // Redirect to login
            setTimeout(function() {
                window.location.href = "{{ $login_url }}";
            }, 500);
        }
        
        function forceRedirect() {
            window.location.href = "{{ $login_url }}";
        }
        
        function handleLogoutError() {
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('manualLogout').style.display = 'inline-block';
            clearInterval(progressInterval);
            clearInterval(messageInterval);
        }
        
        // Start the logout process
        window.onload = function() {
            // Start progress animation
            progressInterval = setInterval(updateProgress, intervalTime);
            messageInterval = setInterval(updateMessage, 1600); // Change message every 1.6 seconds
            
            // Trigger WSO2 logout in hidden iframe
            setTimeout(function() {
                try {
                    const wso2LogoutUrl = "{{ $wso2_base_url }}/oidc/logout?" + 
                                        "post_logout_redirect_uri=" + encodeURIComponent("{{ $login_url }}?logged_out=1") +
                                        @if($id_token)
                                        "&id_token_hint=" + encodeURIComponent("{{ $id_token }}");
                                        @else
                                        "";
                                        @endif
                    
                    console.log('WSO2 Global Logout URL:', wso2LogoutUrl);
                    document.getElementById('wso2LogoutFrame').src = wso2LogoutUrl;
                    
                    // Fallback: If iframe doesn't work, redirect directly
                    setTimeout(function() {
                        if (progress < 90) {
                            progress = 90;
                            document.getElementById('progressFill').style.width = progress + '%';
                        }
                    }, 3000);
                    
                } catch (e) {
                    console.error('Logout error:', e);
                    handleLogoutError();
                }
            }, 1000);
            
            // Safety timeout - force redirect after 10 seconds
            setTimeout(function() {
                if (window.location.pathname.includes('logout-progress')) {
                    forceRedirect();
                }
            }, 10000);
        };
        
        // Handle iframe load events
        document.getElementById('wso2LogoutFrame').onload = function() {
            try {
                // Check if iframe has loaded WSO2 logout successfully
                if (this.contentWindow && this.contentWindow.location) {
                    progress = Math.max(progress, 70);
                    document.getElementById('progressFill').style.width = progress + '%';
                }
            } catch (e) {
                // Cross-origin restrictions prevent access, but that's expected
                // The logout likely succeeded
                progress = Math.max(progress, 80);
                document.getElementById('progressFill').style.width = progress + '%';
            }
        };
        
        // Prevent user from staying on this page
        window.addEventListener('beforeunload', function(e) {
            // This will only work if the user manually tries to leave
            e.preventDefault();
            e.returnValue = '';
        });
    </script>
</body>
</html>
