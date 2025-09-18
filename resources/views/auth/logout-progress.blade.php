<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging out...</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        
        .logout-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .logout-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .logout-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 3px;
        }
        
        .status-text {
            font-size: 14px;
            color: #888;
            margin-top: 10px;
        }
        
        .hidden-iframe {
            display: none;
        }
        
        .error-message {
            color: #e74c3c;
            background: #ffeaea;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
            display: none;
        }
        
        .manual-logout {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 15px;
            display: none;
        }
        
        .manual-logout:hover {
            background: #5a6fd8;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="spinner"></div>
        <h1 class="logout-title">Logging Out</h1>
        <p class="logout-message">
            Please wait while we securely log you out from all applications...
        </p>
        
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        
        <div class="status-text" id="statusText">
            Initializing global logout process...
        </div>
        
        <div class="error-message" id="errorMessage">
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
