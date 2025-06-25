<?php
/**
 * API Response Handler
 * 
 * Provides consistent JSON responses for the AppleJuice API
 */

namespace appleJuiceNETZ\appleJuice;

class ApiResponse
{
    /**
     * Send successful response
     */
    public function success($data = null, $message = 'Success', $code = 200)
    {
        http_response_code($code);
        
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => time(),
            'data' => $data
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Send error response
     */
    public function error($message = 'An error occurred', $code = 400, $details = null)
    {
        http_response_code($code);
        
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $code,
                'details' => $details
            ],
            'timestamp' => time()
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Send validation error response
     */
    public function validationError($errors, $message = 'Validation failed')
    {
        $this->error($message, 422, ['validation_errors' => $errors]);
    }
    
    /**
     * Send not found response
     */
    public function notFound($message = 'Resource not found')
    {
        $this->error($message, 404);
    }
    
    /**
     * Send unauthorized response
     */
    public function unauthorized($message = 'Unauthorized access')
    {
        $this->error($message, 401);
    }
    
    /**
     * Send server error response
     */
    public function serverError($message = 'Internal server error', $details = null)
    {
        $this->error($message, 500, $details);
    }
    
    /**
     * Send paginated response
     */
    public function paginated($data, $total, $page = 1, $perPage = 20, $message = 'Success')
    {
        $totalPages = ceil($total / $perPage);
        
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ],
            'timestamp' => time()
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>