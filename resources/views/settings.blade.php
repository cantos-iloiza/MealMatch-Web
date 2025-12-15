@extends('layouts.app')

@section('title', 'Settings - MealMatch')

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #FFF5CF;
            min-height: 100vh;
        }

        .app-header {
            background-color: #4CAF50;
            color: white;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .back-btn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            margin-right: 16px;
            padding: 4px;
        }

        .app-header h1 {
            font-size: 20px;
            font-weight: bold;
            flex: 1;
            text-align: center;
            margin-right: 40px;
        }

        .container {
            padding: 16px;
            max-width: 800px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            margin-bottom: 12px;
            margin-top: 24px;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .setting-card {
            background: white;
            border-radius: 12px;
            border: 2px solid #4CAF50;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .setting-card:hover {
            transform: translateY(-2px);
        }

        .setting-card.warning {
            border-color: #FFC107;
        }

        .setting-item {
            display: flex;
            align-items: center;
            padding: 16px;
            color: inherit;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .icon-container {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .icon-container svg {
            width: 24px;
            height: 24px;
        }

        .setting-content {
            flex: 1;
        }

        .setting-title {
            font-weight: bold;
            font-size: 15px;
            color: black;
            margin-bottom: 4px;
        }

        .setting-subtitle {
            font-size: 12px;
            color: #666;
        }

        .chevron {
            color: #999;
            font-size: 24px;
        }

        .logout-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 14px 48px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 32px auto 20px;
            box-shadow: 0 2px 8px rgba(244,67,54,0.3);
            transition: background-color 0.2s;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        .logout-btn svg {
            width: 20px;
            height: 20px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: #FFF5CF;
            border-radius: 16px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .modal-title {
            font-weight: bold;
            font-size: 18px;
            flex: 1;
        }

        .modal-body {
            font-size: 15px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .warning-box {
            background-color: #FFEBEE;
            border: 1px solid: #FFCDD2;
            border-radius: 8px;
            padding: 12px;
            margin: 12px 0;
        }

        .warning-point {
            font-size: 12px;
            color: #666;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-cancel {
            background: transparent;
            color: #666;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(76, 175, 80, 0.3);
            border-top-color: #4CAF50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .snackbar {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: #4CAF50;
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 3000;
        }

        .snackbar.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .snackbar.error {
            background-color: #f44336;
        }

        .snackbar svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endpush

@section('page-title')
    <h1 class="text-4xl font-bold">Settings</h1>
@endsection

@section('content')
    <div class="app-header" style="background:none; box-shadow:none; padding:0; margin-bottom:16px;">
        <button class="back-btn" onclick="history.back()">←</button>
    </div>

    <div class="container" style="padding:0;">
        <div class="section-title">Account</div>

        <div class="setting-card">
            <button type="button" class="setting-item" onclick="showEditProfileModal()">
                <div class="icon-container" style="background-color: rgba(76, 175, 80, 0.1);">
                    <svg fill="#4CAF50" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">Edit Profile</div>
                    <div class="setting-subtitle">Update your personal profile</div>
                </div>
                <div class="chevron">›</div>
            </button>
        </div>

        <div class="setting-card">
            <button type="button" class="setting-item" onclick="showChangePasswordModal()">
                <div class="icon-container" style="background-color: rgba(255, 152, 0, 0.1);">
                    <svg fill="#FF9800" viewBox="0 0 24 24">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">Change Password</div>
                    <div class="setting-subtitle">Update your password</div>
                </div>
                <div class="chevron">›</div>
            </button>
        </div>

        <div class="setting-card">
            <button type="button" class="setting-item" onclick="showModifyGoalsModal()">
                <div class="icon-container" style="background-color: rgba(233, 30, 99, 0.1);">
                    <svg fill="#E91E63" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">Modify Goals</div>
                    <div class="setting-subtitle">Change personal and daily calorie goals</div>
                </div>
                <div class="chevron">›</div>
            </button>
        </div>

        <div class="setting-card">
            <button type="button" class="setting-item" onclick="showWeightModal()">
                <div class="icon-container" style="background-color: rgba(33, 150, 243, 0.1);">
                    <svg fill="#2196F3" viewBox="0 0 24 24">
                        <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 16c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zm1-11h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">Weight</div>
                    <div class="setting-subtitle">Update your weight progress</div>
                </div>
                <div class="chevron">›</div>
            </button>
        </div>

        <div class="section-title">Support</div>

        <div class="setting-card">
            <a href="usermanual.blade.php" class="setting-item">
                <div class="icon-container" style="background-color: rgba(0, 150, 136, 0.1);">
                    <svg fill="#009688" viewBox="0 0 24 24">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">User's Manual</div>
                    <div class="setting-subtitle">Learn how to use MealMatch</div>
                </div>
                <div class="chevron">›</div>
            </a>
        </div>

        <div class="setting-card">
            <a href="aboutus.blade.php" class="setting-item">
                <div class="icon-container" style="background-color: rgba(158, 158, 158, 0.1);">
                    <svg fill="#9E9E9E" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">About Us</div>
                    <div class="setting-subtitle">Learn more about MealMatch</div>
                </div>
                <div class="chevron">›</div>
            </a>
        </div>

        <div class="setting-card warning">
            <button type="button" onclick="showDeleteDialog()" class="setting-item">
                <div class="icon-container" style="background-color: rgba(255, 193, 7, 0.1);">
                    <svg fill="#FFC107" viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="setting-content">
                    <div class="setting-title">Delete Account</div>
                    <div class="setting-subtitle">Permanently remove your account</div>
                </div>
                <div class="chevron">›</div>
            </button>
        </div>

        <button class="logout-btn" onclick="showLogoutDialog()">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
            </svg>
            Log Out
        </button>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(244, 67, 54, 0.1);">
                    <svg fill="#f44336" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                </div>
                <div class="modal-title">Log Out</div>
            </div>
            <div class="modal-body">
                Are you sure you want to log out?
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeModal('logoutModal')">Cancel</button>
                <button class="btn btn-danger" onclick="handleLogout()">Log Out</button>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(255, 193, 7, 0.1);">
                    <svg fill="#FFC107" viewBox="0 0 24 24" width="28" height="28">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="modal-title">Delete Account</div>
            </div>
            <div class="modal-body">
                <p style="font-weight: bold; margin-bottom: 12px;">Are you sure you want to delete your MealMatch account?</p>
                <p style="font-weight: 600; color: #666; font-size: 13px; margin-bottom: 8px;">Once your account is deleted:</p>
                <div class="warning-point">• All your profile information, saved meals, preferences, and activity history will be permanently removed</div>
                <div class="warning-point">• You will not be able to recover your data or reactivate the same account</div>
                <div class="warning-point">• Any active sessions on other devices will be automatically signed out</div>
                <div class="warning-box" style="margin-top: 12px; padding: 12px; background-color: #FFEBEE; border: 1px solid #FFCDD2; border-radius: 8px;">
                    <p style="font-weight: bold; color: #c62828; font-size: 13px; margin-bottom: 4px;">Deletion Period:</p>
                    <p style="color: #d32f2f; font-size: 12px; line-height: 1.4; margin-bottom: 8px;">Your account will be scheduled for deletion and will be permanently erased after 30 days.</p>
                    <p style="color: #d32f2f; font-size: 12px; line-height: 1.4;">If you log back in within this period, the deletion request will be automatically canceled, and your account will remain active.</p>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn btn-danger" onclick="handleDeleteAccount()">Delete Account</button>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(76, 175, 80, 0.1);">
                    <svg fill="#4CAF50" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div class="modal-title">Edit Profile</div>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Name</label>
                        <input type="text" id="profileName" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Enter your name" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Email</label>
                        <input type="email" id="profileEmail" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Enter your email" required>
                    </div>
                    <div style="background-color: rgba(33, 150, 243, 0.1); padding: 12px; border-radius: 8px; border: 1px solid rgba(33, 150, 243, 0.3); margin-top: 16px;">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg fill="#2196F3" viewBox="0 0 24 24" width="20" height="20" style="flex-shrink: 0; margin-top: 2px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            <p style="font-size: 12px; color: #1976D2; margin: 0; line-height: 1.4;">Changing your email requires verification. We'll send a link to your new email.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeModal('editProfileModal')">Cancel</button>
                <button class="btn btn-primary" onclick="handleUpdateProfile()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(255, 152, 0, 0.1);">
                    <svg fill="#FF9800" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                </div>
                <div class="modal-title">Change Password</div>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Current Password</label>
                        <input type="password" id="currentPassword" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Enter current password" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">New Password</label>
                        <input type="password" id="newPassword" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Enter new password" required minlength="6">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Confirm New Password</label>
                        <input type="password" id="confirmPassword" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Re-enter new password" required minlength="6">
                    </div>
                    <div style="background-color: rgba(33, 150, 243, 0.1); padding: 12px; border-radius: 8px; border: 1px solid rgba(33, 150, 243, 0.3);">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg fill="#2196F3" viewBox="0 0 24 24" width="20" height="20" style="flex-shrink: 0; margin-top: 2px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            <p style="font-size: 12px; color: #1976D2; margin: 0;">Password must be at least 6 characters long.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeModal('changePasswordModal')">Cancel</button>
                <button class="btn btn-primary" onclick="handleChangePassword()">Change Password</button>
            </div>
        </div>
    </div>

    <!-- Modify Goals Modal -->
    <div id="modifyGoalsModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(233, 30, 99, 0.1);">
                    <svg fill="#E91E63" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </div>
                <div class="modal-title">Modify Goals</div>
            </div>
            <div class="modal-body">
                <form id="modifyGoalsForm">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Goal Weight (kg)</label>
                        <input type="number" id="goalWeight" step="0.1" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Enter goal weight" required>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Weight Pace</label>
                        <select id="weightPace" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" required>
                            <option value="relaxed">Relaxed (0.25 kg/week)</option>
                            <option value="steady" selected>Steady (0.5 kg/week)</option>
                            <option value="accelerated">Accelerated (0.75 kg/week)</option>
                            <option value="vigorous">Vigorous (1 kg/week)</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">Activity Level</label>
                        <select id="activityLevel" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" required>
                            <option value="Sedentary">Sedentary - Spend most of the day sitting</option>
                            <option value="Lightly Active">Lightly Active - Spend a good part of the day on your feet</option>
                            <option value="Moderately Active" selected>Moderately Active - Spend a good part of the day doing physical activity</option>
                            <option value="Extremely Active">Extremely Active - Spend a good part of the day doing heavy physical activity</option>
                        </select>
                    </div>
                    <div style="background-color: rgba(0, 150, 136, 0.1); padding: 16px; border-radius: 12px; border: 2px solid rgba(0, 150, 136, 0.3);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <svg fill="#009688" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.9l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z"/>
                            </svg>
                            <div>
                                <div style="font-weight: bold; font-size: 14px;">Daily Calorie Goal</div>
                                <div id="calculatedCalories" style="font-size: 24px; font-weight: bold; color: #009688;">Calculating...</div>
                            </div>
                        </div>
                        <p style="font-size: 12px; color: #00695C; margin: 0;">Auto-calculated based on your pace and activity level</p>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeModal('modifyGoalsModal')">Cancel</button>
                <button class="btn btn-primary" onclick="handleUpdateGoals()">Save Goals</button>
            </div>
        </div>
    </div>

    <!-- Weight Modal -->
    <div id="weightModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div class="modal-icon" style="background-color: rgba(33, 150, 243, 0.1);">
                    <svg fill="#2196F3" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 16c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zm1-11h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
                    </svg>
                </div>
                <div class="modal-title">Update Weight</div>
            </div>
            <div class="modal-body">
                <div id="weightProgress" style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.8), #81C784); padding: 20px; border-radius: 16px; margin-bottom: 20px; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="margin: 0; font-size: 18px;">Your Journey</h3>
                        <span id="progressPercent" style="background: white; color: #4CAF50; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 14px;">0%</span>
                    </div>
                    <div style="background: rgba(255,255,255,0.3); height: 12px; border-radius: 10px; overflow: hidden; margin-bottom: 16px;">
                        <div id="progressBar" style="background: white; height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-around; font-size: 14px;">
                        <div style="text-align: center;">
                            <div style="font-weight: bold; font-size: 16px;" id="startWeight">0 kg</div>
                            <div style="opacity: 0.9;">Start</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: bold; font-size: 16px;" id="currentWeightDisplay">0 kg</div>
                            <div style="opacity: 0.9;">Current</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: bold; font-size: 16px;" id="goalWeightDisplay">0 kg</div>
                            <div style="opacity: 0.9;">Goal</div>
                        </div>
                    </div>
                </div>
                <form id="updateWeightForm">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">New Weight</label>
                        <div style="display: flex; gap: 12px;">
                            <input type="number" id="newWeight" step="0.1" style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px;" placeholder="Enter weight" required>
                            <select id="weightUnit" style="padding: 12px; border: 1px solid #ddd; border-radius: 12px; font-size: 14px; background-color: #4CAF50; color: white; font-weight: bold;">
                                <option value="kg">kg</option>
                                <option value="lbs">lbs</option>
                            </select>
                        </div>
                    </div>
                    <div style="background-color: rgba(33, 150, 243, 0.1); padding: 12px; border-radius: 8px; border: 1px solid rgba(33, 150, 243, 0.3);">
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg fill="#2196F3" viewBox="0 0 24 24" width="20" height="20" style="flex-shrink: 0; margin-top: 2px;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                            </svg>
                            <p style="font-size: 12px; color: #1976D2; margin: 0; line-height: 1.4;">Your daily calorie goal will be automatically adjusted based on your new weight.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeModal('weightModal')">Cancel</button>
                <button class="btn btn-primary" onclick="handleUpdateWeight()">Update Weight</button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <!-- Snackbar -->
    <div id="snackbar" class="snackbar">
        <svg fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span id="snackbarText"></span>
    </div>

    <script>
        // Demo data to keep the page fully functional without backend/Firebase
        let userData = {
            name: 'John Doe',
            email: 'john.doe@example.com',
            weight: 75.0,
            startingWeight: 80.0,
            goalWeight: 70.0,
            weightPace: 'steady',
            activityLevel: 'Moderately Active',
            dailyCalorieGoal: 2000,
            gender: 'male',
            age: 30,
            height: 175
        };

        window.addEventListener('DOMContentLoaded', function() {
            // Prefill demo fields
            document.getElementById('profileName').value = userData.name;
            document.getElementById('profileEmail').value = userData.email;
            document.getElementById('goalWeight').value = userData.goalWeight;
            document.getElementById('weightPace').value = userData.weightPace;
            document.getElementById('activityLevel').value = userData.activityLevel;
            updateWeightSummary();
            calculateCalories();
            console.log('Demo mode: Using static mock data');
        });

        window.showModal = function(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        window.closeModal = function(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        window.showLogoutDialog = function() {
            showModal('logoutModal');
        }

        window.showDeleteDialog = function() {
            showModal('deleteModal');
        }

        window.showEditProfileModal = function() {
            document.getElementById('profileName').value = userData.name || '';
            document.getElementById('profileEmail').value = userData.email || '';
            showModal('editProfileModal');
        }

        window.showChangePasswordModal = function() {
            document.getElementById('changePasswordForm').reset();
            showModal('changePasswordModal');
        }

        window.showModifyGoalsModal = function() {
            document.getElementById('goalWeight').value = userData.goalWeight || '';
            document.getElementById('weightPace').value = userData.weightPace || 'steady';
            document.getElementById('activityLevel').value = userData.activityLevel || 'Moderately Active';
            calculateCalories();
            showModal('modifyGoalsModal');
        }

        function updateWeightSummary() {
            const startWeight = userData.startingWeight || userData.weight || 0;
            const currentWeight = userData.weight || 0;
            const goalWeight = userData.goalWeight || 0;

            document.getElementById('startWeight').textContent = startWeight.toFixed(1) + ' kg';
            document.getElementById('currentWeightDisplay').textContent = currentWeight.toFixed(1) + ' kg';
            document.getElementById('goalWeightDisplay').textContent = goalWeight.toFixed(1) + ' kg';

            const totalNeeded = Math.abs(goalWeight - startWeight);
            const achieved = Math.abs(currentWeight - startWeight);
            const progress = totalNeeded > 0 ? (achieved / totalNeeded * 100).toFixed(1) : 0;

            document.getElementById('progressPercent').textContent = progress + '%';
            document.getElementById('progressBar').style.width = progress + '%';
        }

        window.showWeightModal = function() {
            updateWeightSummary();
            showModal('weightModal');
        }

        // Calculate calories when pace or activity changes
        document.getElementById('weightPace')?.addEventListener('change', calculateCalories);
        document.getElementById('activityLevel')?.addEventListener('change', calculateCalories);
        document.getElementById('goalWeight')?.addEventListener('input', calculateCalories);

        function calculateCalories() {
            if (!userData) return;

            const goalWeight = parseFloat(document.getElementById('goalWeight').value) || userData.goalWeight || 0;
            const weightPace = document.getElementById('weightPace').value;
            const activityLevel = document.getElementById('activityLevel').value;
            const currentWeight = userData.weight || 0;
            const gender = userData.gender || 'male';
            const age = userData.age || 25;
            const height = userData.height || 170;

            // Calculate BMR
            let bmr;
            if (gender.toLowerCase() === 'male') {
                bmr = (10 * currentWeight) + (6.25 * height) - (5 * age) + 5;
            } else {
                bmr = (10 * currentWeight) + (6.25 * height) - (5 * age) - 161;
            }

            // Activity multiplier
            let multiplier;
            switch (activityLevel.toLowerCase()) {
                case 'sedentary': multiplier = 1.2; break;
                case 'lightly active': multiplier = 1.375; break;
                case 'moderately active': multiplier = 1.55; break;
                case 'extremely active': multiplier = 1.9; break;
                default: multiplier = 1.2;
            }

            // Calculate TDEE
            let tdee = bmr * multiplier;

            // Apply pace adjustment
            const isLosingWeight = currentWeight > goalWeight;
            let adjustment;
            switch (weightPace) {
                case 'relaxed': adjustment = isLosingWeight ? -250 : 250; break;
                case 'steady': adjustment = isLosingWeight ? -500 : 500; break;
                case 'accelerated': adjustment = isLosingWeight ? -750 : 750; break;
                case 'vigorous': adjustment = isLosingWeight ? -1000 : 1000; break;
                default: adjustment = isLosingWeight ? -500 : 500;
            }

            let targetCalories = Math.round(tdee + adjustment);

            // Safety limits
            if (gender.toLowerCase() === 'male') {
                targetCalories = Math.max(1500, Math.min(4000, targetCalories));
            } else {
                targetCalories = Math.max(1200, Math.min(4000, targetCalories));
            }

            document.getElementById('calculatedCalories').textContent = targetCalories + ' cal/day';
        }

        window.showSnackbar = function(message, isError = false) {
            const snackbar = document.getElementById('snackbar');
            const snackbarText = document.getElementById('snackbarText');
            
            snackbarText.textContent = message;
            if (isError) {
                snackbar.classList.add('error');
            } else {
                snackbar.classList.remove('error');
            }
            
            snackbar.classList.add('show');
            setTimeout(() => {
                snackbar.classList.remove('show');
            }, 3000);
        }

        window.showLoading = function() {
            document.getElementById('loadingOverlay').classList.add('active');
        }

        window.hideLoading = function() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        window.handleUpdateProfile = async function() {
            const name = document.getElementById('profileName').value.trim();
            const email = document.getElementById('profileEmail').value.trim();
            
            if (!name || !email) {
                showSnackbar('Please fill in all fields', true);
                return;
            }

            showLoading();
            closeModal('editProfileModal');

            // Demo mode - simulate update
            setTimeout(() => {
                userData.name = name;
                userData.email = email;
                hideLoading();
                showSnackbar('Profile updated successfully! (Demo mode)');
            }, 1000);
        }

        window.handleChangePassword = async function() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                showSnackbar('Please fill in all fields', true);
                return;
            }

            if (newPassword !== confirmPassword) {
                showSnackbar('Passwords do not match', true);
                return;
            }

            if (newPassword.length < 6) {
                showSnackbar('Password must be at least 6 characters', true);
                return;
            }

            showLoading();
            closeModal('changePasswordModal');

            // Demo mode - simulate update
            setTimeout(() => {
                hideLoading();
                showSnackbar('Password changed successfully! (Demo mode)');
            }, 1000);
        }

        window.handleUpdateGoals = async function() {
            const goalWeight = parseFloat(document.getElementById('goalWeight').value);
            const weightPace = document.getElementById('weightPace').value;
            const activityLevel = document.getElementById('activityLevel').value;
            const caloriesText = document.getElementById('calculatedCalories').textContent;
            const dailyCalorieGoal = parseInt(caloriesText.replace(/[^\d]/g, ''));

            if (!goalWeight || goalWeight <= 0) {
                showSnackbar('Please enter a valid goal weight', true);
                return;
            }

            showLoading();
            closeModal('modifyGoalsModal');

            // Demo mode - simulate update
            setTimeout(() => {
                userData.goalWeight = goalWeight;
                userData.weightPace = weightPace;
                userData.activityLevel = activityLevel;
                userData.dailyCalorieGoal = dailyCalorieGoal;
                updateWeightSummary();
                hideLoading();
                showSnackbar('Goals updated successfully! (Demo mode)');
            }, 1000);
        }

        window.handleUpdateWeight = async function() {
            let newWeight = parseFloat(document.getElementById('newWeight').value);
            const unit = document.getElementById('weightUnit').value;

            if (!newWeight || newWeight <= 0) {
                showSnackbar('Please enter a valid weight', true);
                return;
            }

            // Convert to kg if needed
            if (unit === 'lbs') {
                newWeight = newWeight * 0.453592;
            }

            if (newWeight < 20 || newWeight > 500) {
                showSnackbar('Please enter a realistic weight', true);
                return;
            }

            showLoading();
            closeModal('weightModal');

            // Demo mode - simulate update
            setTimeout(() => {
                // Recalculate calories
                const gender = userData.gender || 'male';
                const age = userData.age || 25;
                const height = userData.height || 170;
                const activityLevel = userData.activityLevel || 'Moderately Active';
                const weightPace = userData.weightPace || 'steady';
                const goalWeight = userData.goalWeight || 0;

                // Calculate BMR
                let bmr;
                if (gender.toLowerCase() === 'male') {
                    bmr = (10 * newWeight) + (6.25 * height) - (5 * age) + 5;
                } else {
                    bmr = (10 * newWeight) + (6.25 * height) - (5 * age) - 161;
                }

                // Activity multiplier
                let multiplier;
                switch (activityLevel.toLowerCase()) {
                    case 'sedentary': multiplier = 1.2; break;
                    case 'lightly active': multiplier = 1.375; break;
                    case 'moderately active': multiplier = 1.55; break;
                    case 'extremely active': multiplier = 1.9; break;
                    default: multiplier = 1.2;
                }

                let tdee = bmr * multiplier;
                const isLosingWeight = newWeight > goalWeight;
                let adjustment;
                switch (weightPace) {
                    case 'relaxed': adjustment = isLosingWeight ? -250 : 250; break;
                    case 'steady': adjustment = isLosingWeight ? -500 : 500; break;
                    case 'accelerated': adjustment = isLosingWeight ? -750 : 750; break;
                    case 'vigorous': adjustment = isLosingWeight ? -1000 : 1000; break;
                    default: adjustment = isLosingWeight ? -500 : 500;
                }

                let newCalorieGoal = Math.round(tdee + adjustment);
                if (gender.toLowerCase() === 'male') {
                    newCalorieGoal = Math.max(1500, Math.min(4000, newCalorieGoal));
                } else {
                    newCalorieGoal = Math.max(1200, Math.min(4000, newCalorieGoal));
                }

                userData.weight = newWeight;
                userData.dailyCalorieGoal = newCalorieGoal;
                updateWeightSummary();
                calculateCalories();
                
                hideLoading();
                showSnackbar(`Weight updated! New calorie goal: ${newCalorieGoal} cal/day (Demo mode)`);
            }, 1000);
        }

        window.handleLogout = async function() {
            closeModal('logoutModal');
            showLoading();

            // Demo mode - just show message
            setTimeout(() => {
                hideLoading();
                showSnackbar('Logged out successfully! (Demo mode)');
            }, 1000);
        }

        window.handleDeleteAccount = async function() {
            closeModal('deleteModal');
            showLoading();

            // Demo mode - just show message
            setTimeout(() => {
                hideLoading();
                showSnackbar('Account deletion scheduled for 30 days (Demo mode)');
            }, 1000);
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });
    </script>
@endsection