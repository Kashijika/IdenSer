<?php

/**
 * WSO2 User Role Mapping
 * 
 * This file maps WSO2 users to their roles until WSO2 is properly configured
 * to include roles in JWT tokens or userinfo endpoint.
 * 
 * Once WSO2 is properly configured, this file can be deleted.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | User Role Mappings by Email
    |--------------------------------------------------------------------------
    | 
    | Map user emails to their WSO2 roles. These should match the actual
    | roles assigned in WSO2 Identity Server.
    |
    */
    'user_roles_by_email' => [
        'kashijika460@gmail.com' => ['Admin'],
        'tata61455@gmail.com' => ['Human Resources'],
        // Add more user mappings as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | User Role Mappings by User ID
    |--------------------------------------------------------------------------
    |
    | Map user IDs to their WSO2 roles. This is more specific than email
    | mapping and takes precedence.
    |
    */
    'user_roles_by_id' => [
        'f94687c1-c254-4118-844e-080a7289af71' => ['Admin'], // Kashijika Pratama
        'e237fc69-c75d-4d87-9faf-686769458f27' => ['Human Resources'], // Haru Urara
        'fa7fd6a8-4b47-481b-ba4f-6364bff47550' => ['Employee'], // Symboli Rudolf
        // Add more user mappings as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Available WSO2 Roles
    |--------------------------------------------------------------------------
    |
    | List of available roles in your WSO2 setup. These should match exactly
    | what's configured in WSO2 Identity Server.
    |
    */
    'available_roles' => [
        'Admin',
        'Human Resources', 
        'Employee'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Role
    |--------------------------------------------------------------------------
    |
    | The default role assigned to users when no specific role is found.
    |
    */
    'default_role' => 'Employee',

    /*
    |--------------------------------------------------------------------------
    | Auto-discovery Settings
    |--------------------------------------------------------------------------
    |
    | Settings for automatically discovering roles from WSO2
    |
    */
    'auto_discovery' => [
        'enabled' => true,
        'fallback_to_config' => true,
        'cache_duration' => 3600, // 1 hour
    ]
];
