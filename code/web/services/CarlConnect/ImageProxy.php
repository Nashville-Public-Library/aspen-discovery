<?php
/**
 * Proxy service for retrieving images from CarlConnect that require authentication
 *
 * @category Aspen
 * @author Junie
 * Date: 2025-07-31
 */

require_once ROOT_DIR . '/Action.php';
require_once ROOT_DIR . '/sys/CurlWrapper.php';

class CarlConnect_ImageProxy extends Action {
    private $carlConnectUrl = 'https://nashville.carlconnect.com';
    private $loginUrl = 'https://nashville.carlconnect.com/login';
    private $curlWrapper;
    private $sessionId = null;
    
    function getBreadcrumbs(): array {
        $breadcrumbs = [];
        $breadcrumbs[] = new Breadcrumb('/Admin/Home', 'Administration Home');
        $breadcrumbs[] = new Breadcrumb('', 'CarlConnect Image Proxy');
        return $breadcrumbs;
    }
    
    function getActiveAdminSection(): string {
        return 'system_reports';
    }
    
    function canView(): bool {
        return UserAccount::userHasPermission([
            'View All Librarian Facebook',
            'View Location Librarian Facebook',
        ]);
    }

    function launch() {
        global $interface;
        
        // Initialize curl wrapper
        $this->curlWrapper = new CurlWrapper();
        
        // Get the image URL from the request
        $imageUrl = isset($_REQUEST['url']) ? $_REQUEST['url'] : null;
        
        if (empty($imageUrl)) {
            $this->sendErrorResponse('No image URL provided');
            return;
        }
        
        // Check if we have credentials in the session
        if (isset($_SESSION['carlconnect_username']) && isset($_SESSION['carlconnect_password'])) {
            $username = $_SESSION['carlconnect_username'];
            $password = $_SESSION['carlconnect_password'];
            
            // Try to authenticate and get the image
            $imageData = $this->getImageWithAuth($imageUrl, $username, $password);
            if ($imageData !== false) {
                $this->sendImageResponse($imageData);
                return;
            }
        }
        
        // If we don't have credentials or authentication failed, show login form
        if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
            // User has submitted credentials, try to authenticate
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];
            
            $imageData = $this->getImageWithAuth($imageUrl, $username, $password);
            if ($imageData !== false) {
                // Store credentials in session for future requests
                $_SESSION['carlconnect_username'] = $username;
                $_SESSION['carlconnect_password'] = $password;
                
                $this->sendImageResponse($imageData);
                return;
            } else {
                $interface->assign('error', 'Authentication failed. Please check your credentials.');
            }
        }
        
        // Show login form
        $interface->assign('imageUrl', $imageUrl);
        $interface->assign('returnUrl', $_SERVER['REQUEST_URI']);
        $this->display('imageProxy.tpl', 'CarlConnect Authentication');
    }
    
    /**
     * Authenticate with CarlConnect and retrieve the image
     * 
     * @param string $imageUrl The URL of the image to retrieve
     * @param string $username The username for CarlConnect
     * @param string $password The password for CarlConnect
     * @return string|false The image data if successful, false otherwise
     */
    private function getImageWithAuth($imageUrl, $username, $password) {
        global $logger;
        
        // First, authenticate with CarlConnect
        if (!$this->authenticate($username, $password)) {
            $logger->log("Failed to authenticate with CarlConnect", Logger::LOG_ERROR);
            return false;
        }
        
        // Now retrieve the image
        $imageData = $this->curlWrapper->curlGetPage($imageUrl);
        
        // Check if we got a valid image
        if (empty($imageData) || $this->curlWrapper->getResponseCode() != 200) {
            $logger->log("Failed to retrieve image from CarlConnect: " . $imageUrl, Logger::LOG_ERROR);
            return false;
        }
        
        // Check if the response is an image (by content type)
        $contentType = null;
        foreach ($this->curlWrapper->responseHeaders as $header) {
            if (strpos($header, 'Content-Type:') === 0) {
                $contentType = trim(substr($header, 14));
                break;
            }
        }
        
        if ($contentType && strpos($contentType, 'image/') === 0) {
            return $imageData;
        } else {
            $logger->log("Response from CarlConnect is not an image: " . $contentType, Logger::LOG_ERROR);
            return false;
        }
    }
    
    /**
     * Authenticate with CarlConnect
     * 
     * @param string $username The username for CarlConnect
     * @param string $password The password for CarlConnect
     * @return bool True if authentication was successful, false otherwise
     */
    private function authenticate($username, $password) {
        global $logger;
        
        // Set up cookie jar for session handling
        $this->curlWrapper->setCookieJar('CARLCONNECT');
        
        // First, get the login page to get any necessary cookies or tokens
        $loginPage = $this->curlWrapper->curlGetPage($this->loginUrl);
        if (empty($loginPage)) {
            $logger->log("Failed to retrieve login page from CarlConnect", Logger::LOG_ERROR);
            return false;
        }
        
        // Extract CSRF token if needed (adjust based on actual site requirements)
        $csrfToken = '';
        if (preg_match('/<input[^>]*name="csrf_token"[^>]*value="([^"]*)"/', $loginPage, $matches)) {
            $csrfToken = $matches[1];
        }
        
        // Prepare login data
        $postData = [
            'username' => $username,
            'password' => $password,
        ];
        
        if (!empty($csrfToken)) {
            $postData['csrf_token'] = $csrfToken;
        }
        
        // Submit login form
        $response = $this->curlWrapper->curlPostPage($this->loginUrl, $postData);
        
        // Check if login was successful (this will depend on how the site indicates success)
        // For now, we'll assume it's successful if we get a 200 response and are redirected
        $responseCode = $this->curlWrapper->getResponseCode();
        $info = $this->curlWrapper->getInfo();
        
        if ($responseCode == 200 || $responseCode == 302) {
            // Check if we have cookies, which would indicate a successful login
            if (!empty($this->curlWrapper->cookies)) {
                return true;
            }
        }
        
        $logger->log("Authentication with CarlConnect failed. Response code: " . $responseCode, Logger::LOG_ERROR);
        return false;
    }
    
    /**
     * Send an image response to the client
     * 
     * @param string $imageData The image data to send
     */
    private function sendImageResponse($imageData) {
        // Determine content type from the image data
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $contentType = $finfo->buffer($imageData);
        
        // Send appropriate headers
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen($imageData));
        header('Cache-Control: max-age=86400'); // Cache for 24 hours
        
        // Output the image data
        echo $imageData;
        exit;
    }
    
    /**
     * Send an error response to the client
     * 
     * @param string $message The error message
     */
    private function sendErrorResponse($message) {
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: text/plain');
        echo $message;
        exit;
    }
}