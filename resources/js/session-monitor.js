/**
 * Session Monitor - Detects when user is logged out from another application
 */
class SessionMonitor {
    constructor() {
        this.checkInterval = 60000; // Check every 60 seconds
        this.isChecking = false;
        this.consecutiveFailures = 0;
        this.maxFailures = 3;
        
        this.init();
    }
    
    init() {
        // Start monitoring when page loads
        this.startMonitoring();
        
        // Check when user becomes active after being idle
        this.setupActivityDetection();
        
        // Check when user navigates to sensitive pages
        this.setupNavigationDetection();
    }
    
    startMonitoring() {
        setInterval(() => {
            this.checkSession();
        }, this.checkInterval);
        
        // Also check immediately
        setTimeout(() => this.checkSession(), 2000);
    }
    
    setupActivityDetection() {
        let lastActivity = Date.now();
        
        // Track user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, () => {
                const now = Date.now();
                const timeSinceLastActivity = now - lastActivity;
                
                // If user was idle for more than 5 minutes, check session
                if (timeSinceLastActivity > 300000) {
                    this.checkSession();
                }
                
                lastActivity = now;
            }, true);
        });
    }
    
    setupNavigationDetection() {
        // Check session when navigating to sensitive pages
        const originalPushState = history.pushState;
        history.pushState = function(...args) {
            originalPushState.apply(history, args);
            if (window.sessionMonitor) {
                window.sessionMonitor.checkSessionForNavigation(args[2]);
            }
        };
        
        window.addEventListener('popstate', () => {
            if (window.sessionMonitor) {
                window.sessionMonitor.checkSessionForNavigation(window.location.href);
            }
        });
    }
    
    checkSessionForNavigation(url) {
        const criticalPaths = ['/dashboard', '/users', '/admin', '/settings', '/security'];
        const isCriticalPath = criticalPaths.some(path => url.includes(path));
        
        if (isCriticalPath) {
            this.checkSession();
        }
    }
    
    async checkSession() {
        if (this.isChecking) return;
        
        this.isChecking = true;
        
        try {
            const response = await fetch('/validate-token', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (!data.valid) {
                console.warn('Session validation failed - token is invalid');
                this.handleInvalidSession();
                return;
            }
            
            // Reset failure counter on success
            this.consecutiveFailures = 0;
            console.log('Session validation successful');
            
        } catch (error) {
            console.warn('Session check failed:', error.message);
            this.consecutiveFailures++;
            
            // If we have multiple consecutive failures, assume session is invalid
            if (this.consecutiveFailures >= this.maxFailures) {
                this.handleInvalidSession();
            }
        } finally {
            this.isChecking = false;
        }
    }
    
    handleInvalidSession() {
        // Show user-friendly notification
        this.showLogoutNotification();
        
        // Redirect to login after a brief delay
        setTimeout(() => {
            window.location.href = '/auth/login?session_expired=1';
        }, 3000);
    }
    
    showLogoutNotification() {
        // Create a simple notification
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            z-index: 9999;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        `;
        notification.innerHTML = `
            <strong>Session Expired</strong><br>
            You have been logged out from another application. Redirecting to login...
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after redirect
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
}

// Initialize session monitor when page loads
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window !== 'undefined') {
        window.sessionMonitor = new SessionMonitor();
    }
});
