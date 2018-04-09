<?php

namespace Application\Service;

use User\Service\RbacManager;
use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\Url;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * Auth service.
     * @var AuthenticationService
     */
    private $authService;

    /**
     * Url view helper.
     * @var Url
     */
    private $urlHelper;

    /**
     * RBAC manager.
     * @var RbacManager
     */
    private $rbacManager;

    /**
     * Constructs the service.
     */
    public function __construct($authService, $urlHelper, $rbacManager)
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->rbacManager = $rbacManager;
    }

    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems()
    {
        $url = $this->urlHelper;
        $items = [];

        $items[] = [
            'id' => 'home',
            'label' => 'Home',
            'link' => $url('home')
        ];

        $items[] = [
            'id' => 'about',
            'label' => 'About',
            'link' => $url('about')
        ];

        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users.
        if (!$this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'login',
                'label' => 'Sign in',
                'link' => $url('login'),
                'float' => 'right'
            ];
        } else {
            if ($this->rbacManager->isGranted(null, 'application.manage')) {
                $items[] = [
                    'id' => 'applications',
                    'label' => 'Application management',
                    'link' => $url('applications')
                ];
            }

            if ($this->rbacManager->isGranted(null, 'environment.manage')) {
                $items[] = [
                    'id' => 'environments',
                    'label' => 'Environment management',
                    'link' => $url('environments')
                ];
            }

            $userDropdownItems = [];

            if ($this->rbacManager->isGranted(null, 'user.manage')) {
                $userDropdownItems[] = [
                    'id' => 'users',
                    'label' => 'Manage Users',
                    'link' => $url('users')
                ];
            }

            if ($this->rbacManager->isGranted(null, 'permission.manage')) {
                $userDropdownItems[] = [
                    'id' => 'permissions',
                    'label' => 'Manage Permissions',
                    'link' => $url('permissions')
                ];
            }

            if ($this->rbacManager->isGranted(null, 'role.manage')) {
                $userDropdownItems[] = [
                    'id' => 'roles',
                    'label' => 'Manage Roles',
                    'link' => $url('roles')
                ];
            }

            if (count($userDropdownItems) != 0) {
                $items[] = [
                    'id' => 'user_management',
                    'label' => 'User management',
                    'dropdown' => $userDropdownItems
                ];
            }

            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'settings',
                        'label' => 'Settings',
                        'link' => $url('application', ['action' => 'settings'])
                    ],
                    [
                        'id' => 'logout',
                        'label' => 'Sign out',
                        'link' => $url('logout')
                    ],
                ]
            ];
        }

        return $items;
    }
}


