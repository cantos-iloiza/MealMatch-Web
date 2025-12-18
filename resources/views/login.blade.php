<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MealMatch - Sign In</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=MuseoModerno:wght@400;500;600;700&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .font-museo { font-family: 'MuseoModerno', cursive, sans-serif; }
        .font-dm { font-family: 'DM Sans', sans-serif; }
        .modal-scroll::-webkit-scrollbar { width: 8px; }
        .modal-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #d4d4d4; border-radius: 4px; }
        .modal-scroll::-webkit-scrollbar-thumb:hover { background: #FB7E00; }

        /* Logic for hiding forms */
        .hidden-form {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#FFE4CD] to-[#CAECB4] flex items-center justify-center p-4 font-dm">
    <div class="w-full max-w-6xl flex flex-col lg:flex-row gap-8 lg:gap-12 items-center">
        
        <div class="flex-1 flex flex-col justify-center items-center text-center">
            <div class="mb-8">
                <h1 class="text-6xl font-bold font-museo mb-2">
                    <span class="text-[#E78315]">Meal</span><span class="text-[#61A140]">Match</span>
                </h1>
                <p class="text-xl font-bold text-[#90875F] font-dm tracking-wide">
                    Find Recipes. Track Calories.
                </p>
            </div>
            <div class="w-full max-w-md p-4 flex justify-center">
                <img src="{{ asset('images/login-icon.png') }}" alt="MealMatch Icon" class="w-full max-w-[350px] h-auto object-contain hover:scale-105 transition-transform duration-500">
            </div>
        </div>
    
        {{-- Success Alert --}}
        @if (session('success'))
            <div class="fixed top-5 right-5 z-50 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg flex items-center gap-3" onclick="this.remove()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="fixed top-5 right-5 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg flex items-center gap-3" onclick="this.remove()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold">Error</p>
                    <p class="text-sm">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <div class="flex-1 max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8 relative min-h-[500px]">
                <div class="flex gap-2 mb-6">
                    <button type="button" id="signin-tab" class="flex-1 py-3 text-sm font-semibold rounded-xl transition-all duration-200 bg-white text-[#1b1b18] border-2 border-black shadow-sm">Sign In</button>
                    <button type="button" id="signup-tab" class="flex-1 py-3 text-sm font-semibold rounded-xl transition-all duration-200 text-[#706f6c] border-2 border-gray-200 hover:border-gray-300">Sign Up</button>
                </div>

                {{-- Sign In Form --}}
                <form id="signin-form" class="space-y-4 transition-opacity duration-200">
                    <div>
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Email</label>
                        <input type="email" id="signin-email" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#FB7E00] focus:ring-2 focus:ring-[#FB7E00]/20 outline-none transition-all" placeholder="you@example.com">
                    </div>
                    
                    <div class="relative">
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="signin-password" class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-200 focus:border-[#FB7E00] focus:ring-2 focus:ring-[#FB7E00]/20 outline-none transition-all" placeholder="••••••••">
                            <button type="button" onclick="togglePassword('signin-password', 'eyeIconSignIn')" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-[#FB7E00] focus:outline-none z-20">
                                <svg id="eyeIconSignIn-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeIconSignIn-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-[#FB7E00] border-gray-300 rounded focus:ring-[#FB7E00] cursor-pointer">
                            <span class="ml-2 text-[#706f6c]">Remember me</span>
                        </label>
                        <a href="#" onclick="openForgotPasswordModal()" class="text-[#FB7E00] hover:text-[#e66d00] font-semibold">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-[#FB7E00] to-[#FFCC3F] text-white py-3 rounded-xl font-bold hover:shadow-lg transition-all duration-200 hover:scale-[1.02]">Sign In</button>
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-[#A1A09A]">Or continue with</span></div>
                    </div>
                    <button type="button" id="google-signin-btn"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200 cursor-pointer relative z-10">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-[#1b1b18] font-semibold">Google</span>
                    </button>
                </form>

                {{-- Sign Up Form --}}
                <form id="signup-form" class="space-y-4 hidden-form transition-opacity duration-200">
                    <div>
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Full Name</label>
                        <input type="text" id="signup-name" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#9FC089] focus:ring-2 focus:ring-[#9FC089]/20 outline-none transition-all" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Email</label>
                        <input type="email" id="signup-email" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#9FC089] focus:ring-2 focus:ring-[#9FC089]/20 outline-none transition-all" placeholder="you@example.com">
                    </div>
                    
                    <div class="relative">
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="signup-password" class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-200 focus:border-[#9FC089] focus:ring-2 focus:ring-[#9FC089]/20 outline-none transition-all" placeholder="••••••••">
                            <button type="button" onclick="togglePassword('signup-password', 'eyeIconSignUp')" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-[#9FC089] focus:outline-none z-20">
                                <svg id="eyeIconSignUp-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeIconSignUp-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-[#A1A09A]">Must be at least 8 characters</p>
                    </div>
                    
                    <div class="relative">
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="signup-confirm-password" class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-200 focus:border-[#9FC089] focus:ring-2 focus:ring-[#9FC089]/20 outline-none transition-all" placeholder="••••••••">
                            <button type="button" onclick="togglePassword('signup-confirm-password', 'eyeIconConfirm')" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-[#9FC089] focus:outline-none z-20">
                                <svg id="eyeIconConfirm-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeIconConfirm-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 mt-0.5 text-[#9FC089] border-gray-300 rounded focus:ring-[#9FC089] cursor-pointer">
                        <span class="ml-2 text-xs text-[#706f6c]">
                            I agree to the <button type="button" onclick="openModal('terms-modal')" class="text-[#9FC089] hover:underline font-medium">Terms of Service</button> and <button type="button" onclick="openModal('privacy-modal')" class="text-[#9FC089] hover:underline font-medium">Privacy Policy</button>
                        </span>
                    </label>
                    <button type="submit" class="w-full bg-gradient-to-r from-[#9FC089] to-[#79B4B0] text-white py-3 rounded-xl font-bold hover:shadow-lg transition-all duration-200 hover:scale-[1.02]">Create Account</button>
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                        <div class="relative flex justify-center text-sm"><span class="px-4 bg-white text-[#A1A09A]">Or continue with</span></div>
                    </div>
                    <button type="button" id="google-signup-btn"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-200 cursor-pointer relative z-10">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="text-[#1b1b18] font-semibold">Google</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

        <div id="terms-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="closeModal('terms-modal', event)">
            <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden transform transition-all scale-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#FB7E00]/10">
                    <div>
                        <h2 class="text-2xl font-bold font-museo text-[#E78315]">Terms of Service</h2>
                        <p class="text-xs text-[#706f6c] mt-1">Last Updated: 10/08/2025</p>
                    </div>
                    <button onclick="forceClose('terms-modal')" class="text-gray-400 hover:text-gray-600 transition-colors p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-6 overflow-y-auto modal-scroll text-sm text-[#1b1b18] leading-relaxed space-y-4">
                    <p class="font-medium">Please read these Terms of Service carefully before using the MealMatch mobile application operated by Group 3.</p>
                    <p>By downloading, accessing, or using MealMatch, you agree to be bound by these Terms.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">I. Overview</h3>
                    <p>MealMatch is a mobile application designed to help users:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Find recipes based on ingredients they already have.</li>
                        <li>Track calorie intake through built-in nutritional data.</li>
                        <li>Share, rate, and save personal recipes with the MealMatch community.</li>
                        <li>Promote sustainable eating and minimize food waste in support of Zero Hunger and Responsible Consumption goals.</li>
                    </ul>
                    <p class="italic text-gray-500 text-xs mt-2">MealMatch aims to provide an enjoyable, educational, and health-conscious experience — but should not be considered a substitute for professional dietary or medical advice.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">II. Eligibility</h3>
                    <p>You must be at least 13 years old to use MealMatch. If you are under 18, you must have the permission and supervision of a parent or guardian.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">III. Account Registration</h3>
                    <p>To access certain features (such as posting or saving recipes), you may need to create an account. You agree to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Provide accurate, current, and complete information.</li>
                        <li>Keep your login credentials secure.</li>
                        <li>Be responsible for all activity under your account.</li>
                    </ul>
                    <p>We reserve the right to suspend or terminate accounts that violate these Terms or engage in abusive, fraudulent, or harmful behavior.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">IV. User-Generated Content</h3>
                    <p>MealMatch allows users to post, share, and rate recipes (“User Content”).</p>
                    <p>By submitting User Content, you grant us a non-exclusive, royalty-free, worldwide, transferable license to use, display, modify, and distribute your content within the App and its promotional materials.</p>
                    <p>You represent and warrant that:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>You own or have the right to share the content you post.</li>
                        <li>Your content does not violate any copyright, trademark, or privacy rights.</li>
                        <li>Your content is appropriate and not offensive, harmful, or misleading.</li>
                    </ul>
                    <p>We reserve the right to review, moderate, or remove any content that violates these Terms.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">V. Health and Nutrition Disclaimer</h3>
                    <p>MealMatch provides nutritional and calorie data for informational purposes only.</p>
                    <p>We do not guarantee the accuracy, completeness, or reliability of this information.</p>
                    <p>Always consult a qualified healthcare or nutrition professional for dietary advice.</p>
                    <p>You are responsible for your own health choices and meal decisions.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">VI. Acceptable Use</h3>
                    <p>You agree not to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Use the App for unlawful or harmful purposes.</li>
                        <li>Upload viruses, malware, or harmful code.</li>
                        <li>Harass, spam, or defame other users.</li>
                        <li>Copy, scrape, or reproduce the App’s data or features for commercial use without permission.</li>
                    </ul>
                    <p>Violation of these rules may result in account suspension or legal action.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">VII. Intellectual Property</h3>
                    <p>All content and materials in MealMatch — including text, graphics, logos, recipes (not user-submitted), and software — are the property of Group 3 or its authors.</p>
                    <p>You may not reproduce, modify, or distribute them without our written consent.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">VIII. Privacy</h3>
                    <p>Your privacy is important to us. Please review our Privacy Policy to understand how we collect, use, and protect your data.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">IX. Updates and Changes</h3>
                    <p>We may update or modify MealMatch and these Terms at any time.</p>
                    <p>Continued use of the App after changes take effect means you accept the revised Terms.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">X. Limitation of Liability</h3>
                    <p>MealMatch is provided “as is” and “as available.”</p>
                    <p>We make no guarantees about uninterrupted or error-free operation.</p>
                    <p>To the fullest extent permitted by law, we are not liable for any damages, losses, or injuries arising from your use of the App or reliance on its content.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">XI. Termination</h3>
                    <p>We may suspend or terminate your access to MealMatch at any time if you violate these Terms or engage in misuse of the App.</p>
                    <p>Upon termination, your right to use the App will immediately end.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">XII. Governing Law</h3>
                    <p>These Terms are governed by and interpreted in accordance with the laws of the Republic of the Philippines, without regard to conflict of law principles.</p>

                    <h3 class="font-bold text-[#E78315] text-lg mt-4">XIII. Contact Us</h3>
                    <p>If you have questions or concerns about these Terms or MealMatch, please contact us at:</p>
                    <p class="font-bold">📧 group3@gmail.com</p>
                    <p class="font-bold">📍 Alangilangan, Batangas City</p>
                </div>
                
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button onclick="forceClose('terms-modal')" class="bg-[#E78315] text-white px-6 py-2 rounded-lg font-bold hover:bg-[#d4720d] transition-colors">I Understand</button>
                </div>
            </div>
        </div>

        <div id="privacy-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="closeModal('privacy-modal', event)">
            <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden transform transition-all scale-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#61A140]/10">
                    <div>
                        <h2 class="text-2xl font-bold font-museo text-[#61A140]">Privacy Policy</h2>
                        <p class="text-xs text-[#706f6c] mt-1">Last Updated: 10/08/2025</p>
                    </div>
                    <button onclick="forceClose('privacy-modal')" class="text-gray-400 hover:text-gray-600 transition-colors p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-6 overflow-y-auto modal-scroll text-sm text-[#1b1b18] leading-relaxed space-y-4">
                    <p>Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your information when you use our mobile application, MealMatch (the “App”).</p>
                    <p>By using MealMatch, you agree to the collection and use of information as described in this policy.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">I. Information We Collect</h3>
                    <p>We collect information to provide and improve the App’s services. The types of data we collect include:</p>
                    
                    <h4 class="font-bold mt-2">1. Personal Information</h4>
                    <p>When you create an account, we may collect:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Your name or username</li>
                        <li>Email address</li>
                        <li>Profile picture (if uploaded)</li>
                        <li>Other optional details (e.g., bio or preferences)</li>
                    </ul>

                    <h4 class="font-bold mt-2">2. Usage Data</h4>
                    <p>When you use the App, we automatically collect:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Device information (model, operating system, app version)</li>
                        <li>Log data (IP address, time, and usage activity)</li>
                        <li>Clicks, views, and recipe interactions (for analytics)</li>
                    </ul>

                    <h4 class="font-bold mt-2">3. Recipe and Nutrition Data</h4>
                    <p>When you use features like ingredient-based searches, calorie tracking, or community sharing, we collect:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Ingredients you input</li>
                        <li>Recipes you post, rate, or save</li>
                        <li>Nutrition and calorie information you track</li>
                    </ul>
                    <p class="italic text-gray-500">This data helps improve recommendations and personalize your experience.</p>

                    <h4 class="font-bold mt-2">4. Cookies and Similar Technologies</h4>
                    <p>We may use cookies or local storage to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Keep you signed in</li>
                        <li>Save preferences</li>
                        <li>Measure app performance</li>
                    </ul>
                    <p>You can disable cookies in your device settings, but some features may not work properly.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">II. How We Use Your Information</h3>
                    <p>We use the collected data to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Provide and improve our services and user experience</li>
                        <li>Personalize recipe suggestions based on your ingredients</li>
                        <li>Track your calorie intake and progress</li>
                        <li>Enable community features (posting, rating, and saving recipes)</li>
                        <li>Send optional notifications or updates (you can opt out anytime)</li>
                        <li>Maintain security and prevent fraud</li>
                        <li>Analyze usage trends for improvement</li>
                    </ul>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">III. Sharing and Disclosure</h3>
                    <p>We do not sell or rent your personal data to third parties.</p>
                    <p>However, we may share information in the following cases:</p>
                    
                    <h4 class="font-bold mt-2">1. Service Providers</h4>
                    <p>We may use third-party tools to help us operate the App, such as:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Analytics tools (e.g., Google Analytics for Firebase)</li>
                        <li>Cloud storage and database providers</li>
                        <li>Crash reporting and bug tracking tools</li>
                    </ul>
                    <p>These providers only access data as needed to perform their functions and must comply with privacy regulations.</p>

                    <h4 class="font-bold mt-2">2. Legal Requirements</h4>
                    <p>We may disclose your information if required by law or to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Comply with legal obligations</li>
                        <li>Protect our rights, property, and safety</li>
                        <li>Prevent fraud or misuse</li>
                    </ul>

                    <h4 class="font-bold mt-2">3. Community Sharing</h4>
                    <p>When you post recipes or comments, your username, profile, and recipe content may be visible to other users.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">IV. Data Retention</h3>
                    <p>We keep your personal information only as long as necessary to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Provide our services</li>
                        <li>Fulfill legal or regulatory requirements</li>
                        <li>Resolve disputes and enforce agreements</li>
                    </ul>
                    <p>You can request deletion of your account and data at any time (see Section 8).</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">V. Data Security</h3>
                    <p>We use reasonable technical and organizational measures to protect your data from unauthorized access, loss, or misuse.</p>
                    <p>However, no system is 100% secure, and we cannot guarantee absolute security.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">VI. Children’s Privacy</h3>
                    <p>MealMatch is not directed at children under 13 years old.</p>
                    <p>If we discover that a child under 13 has provided personal data, we will promptly delete it.</p>
                    <p>Parents or guardians may contact us to request data removal.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">VII. Your Rights and Choices</h3>
                    <p>You have the right to:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Access the data we hold about you</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Request deletion of your account and personal data</li>
                        <li>Opt out of analytics or promotional notifications</li>
                    </ul>
                    <p>To exercise these rights, contact us at <span class="font-bold">group3@gmail.com</span>.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">VIII. International Data Transfers</h3>
                    <p>If you access MealMatch from outside the Philippines, note that your data may be transferred and processed in countries where data protection laws may differ.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">IX. Updates to This Policy</h3>
                    <p>We may update this Privacy Policy from time to time.</p>
                    <p>If we make significant changes, we’ll notify you via the App or by email.</p>
                    <p>The updated version will always include the “Last Updated” date at the top.</p>

                    <h3 class="font-bold text-[#61A140] text-lg mt-4">X. Contact Us</h3>
                    <p>If you have questions or concerns about these Terms or MealMatch, please contact us at:</p>
                    <p class="font-bold">📧 group3@gmail.com</p>
                    <p class="font-bold">📍 Alangilangan, Batangas City</p>
                </div>
            </div>    
        </div>
    {{-- Privacy Modal --}}
    <div id="privacy-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="closeModal('privacy-modal', event)">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh] overflow-hidden transform transition-all scale-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#61A140]/10">
                <div>
                    <h2 class="text-2xl font-bold font-museo text-[#61A140]">Privacy Policy</h2>
                    <p class="text-xs text-[#706f6c] mt-1">Last Updated: 10/08/2025</p>
                </div>
                <button onclick="forceClose('privacy-modal')" class="text-gray-400 hover:text-gray-600 transition-colors p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            
            <div class="p-6 overflow-y-auto modal-scroll text-sm text-[#1b1b18] leading-relaxed space-y-4">
               {{-- Privacy Content kept same for brevity --}}
               <p>Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your information.</p>
            </div>
            
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button onclick="forceClose('privacy-modal')" class="bg-[#61A140] text-white px-6 py-2 rounded-lg font-bold hover:bg-[#4a802e] transition-colors">I Understand</button>
            </div>
        </div>
    </div>

    {{-- Forgot Password Modal --}}
    <div id="forgot-password-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm" onclick="closeModal('forgot-password-modal', event)">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl flex flex-col overflow-hidden transform transition-all scale-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#FB7E00]/10">
                <div><h2 class="text-2xl font-bold font-museo text-[#E78315]">Reset Password</h2></div>
                <button onclick="forceClose('forgot-password-modal')" class="text-gray-400 hover:text-gray-600 transition-colors p-2"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-6">Enter the email address associated with your account and we'll send you a link to reset your password.</p>
                <form onsubmit="handleForgotPassword(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#1b1b18] mb-2">Email Address</label>
                        <input type="email" id="reset-email" required class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#FB7E00] focus:ring-2 focus:ring-[#FB7E00]/20 outline-none transition-all" placeholder="you@example.com">
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-[#FB7E00] to-[#FFCC3F] text-white py-3 rounded-xl font-bold hover:shadow-lg transition-all duration-200 hover:scale-[1.02]">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global functions for HTML onclick events
        function togglePassword(inputId, iconIdPrefix) {
            const passwordInput = document.getElementById(inputId);
            const eyeOpen = document.getElementById(iconIdPrefix + '-open');
            const eyeClosed = document.getElementById(iconIdPrefix + '-closed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Tab Switching
        document.addEventListener('DOMContentLoaded', () => {
            const signinTab = document.getElementById('signin-tab');
            const signupTab = document.getElementById('signup-tab');
            const signinForm = document.getElementById('signin-form');
            const signupForm = document.getElementById('signup-form');

            if(signinTab && signupTab && signinForm && signupForm) {
                signinTab.addEventListener('click', () => {
                    signinForm.classList.remove('hidden-form');
                    signupForm.classList.add('hidden-form');
                    
                    // Style Active
                    signinTab.classList.add('bg-white', 'text-[#1b1b18]', 'shadow-sm', 'border-black');
                    signinTab.classList.remove('text-[#706f6c]', 'border-gray-200');
                    
                    // Style Inactive
                    signupTab.classList.remove('bg-white', 'text-[#1b1b18]', 'shadow-sm', 'border-black');
                    signupTab.classList.add('text-[#706f6c]', 'border-gray-200');
                });

                signupTab.addEventListener('click', () => {
                    signupForm.classList.remove('hidden-form');
                    signinForm.classList.add('hidden-form');
                    
                    // Style Active
                    signupTab.classList.add('bg-white', 'text-[#1b1b18]', 'shadow-sm', 'border-black');
                    signupTab.classList.remove('text-[#706f6c]', 'border-gray-200');
                    
                    // Style Inactive
                    signinTab.classList.remove('bg-white', 'text-[#1b1b18]', 'shadow-sm', 'border-black');
                    signinTab.classList.add('text-[#706f6c]', 'border-gray-200');
                });
            }
        });

        // Modal Logic
        window.openModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeModal = (modalId, event) => {
            if (event.target.id === modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.style.overflow = '';
            }
        };

        window.forceClose = (modalId) => {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = '';
        };

        window.openForgotPasswordModal = () => {
            const signInEmail = document.getElementById('signin-email').value;
            if(signInEmail) {
                document.getElementById('reset-email').value = signInEmail;
            }
            window.openModal('forgot-password-modal');
        };
    </script>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { 
            getAuth, 
            signInWithEmailAndPassword, 
            createUserWithEmailAndPassword,
            GoogleAuthProvider,
            signInWithPopup,
            signInWithRedirect,
            getRedirectResult,
            sendPasswordResetEmail
        } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

        // Firebase Config
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        // Initialize Firebase
        try {
            const app = initializeApp(firebaseConfig);
            const auth = getAuth(app);
            console.log("Firebase initialized successfully");

            // --- Define Helper Functions for Module Scope ---

            window.handleSessionLogin = async (user) => {
                try {
                    const idToken = await user.getIdToken(true);
                    
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000); 

                    const response = await fetch("{{ route('auth.session-login') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ idToken }),
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);
                    const data = await response.json();
                    
                    if (!response.ok) throw new Error(data.message || 'Session login failed');

                    window.location.href = data.redirect_url || '/onboarding';
                    
                } catch (error) {
                    console.error('Session Login Error:', error);
                    alert('Login failed: ' + error.message);
                }
            };

            window.handleGoogleSignIn = async () => {
                const btns = [
                    document.getElementById('google-signin-btn'),
                    document.getElementById('google-signup-btn')
                ];
                btns.forEach(b => b && (b.disabled = true));

                try {
                    const provider = new GoogleAuthProvider();
                    provider.setCustomParameters({ prompt: 'select_account' });
                    
                    try {
                        const result = await signInWithPopup(auth, provider);
                        await window.handleSessionLogin(result.user);
                    } catch (popupError) {
                        console.warn('Popup failed, trying redirect:', popupError);
                        await signInWithRedirect(auth, provider);
                    }
                } catch (error) {
                    console.error('Google Sign In Error:', error);
                    alert('Google Sign-In failed: ' + error.message);
                    btns.forEach(b => b && (b.disabled = false));
                }
            };

            window.handleForgotPassword = async (e) => {
                e.preventDefault();
                const email = document.getElementById('reset-email').value;
                try {
                    await sendPasswordResetEmail(auth, email);
                    alert('Password reset link sent! Check your email.');
                    window.forceClose('forgot-password-modal');
                } catch (error) {
                    console.error("Reset Error:", error);
                    alert("Failed to send reset email: " + error.message);
                }
            };

            // --- Bind Events When DOM is Ready ---
            
            // Handle Redirect Result (from Google Redirect flow)
            try {
                const result = await getRedirectResult(auth);
                if (result?.user) {
                    console.log('Recovered from redirect');
                    await window.handleSessionLogin(result.user);
                }
            } catch (error) {
                console.error('Redirect result error:', error);
            }

            // Bind Forms
            const signinFormEl = document.getElementById('signin-form');
            if(signinFormEl) {
                signinFormEl.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    console.log('Signin form submitted');
                    
                    const email = document.getElementById('signin-email').value;
                    const password = document.getElementById('signin-password').value;
                    const submitBtn = e.target.querySelector('button[type="submit"]');
                    
                    if(!email || !password) {
                         alert('Please enter email and password');
                         return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Signing in...';
                    
                    try {
                        const userCredential = await signInWithEmailAndPassword(auth, email, password);
                        await window.handleSessionLogin(userCredential.user);
                    } catch (error) { 
                        console.error('Sign in error:', error); 
                        let msg = 'Error signing in: ' + error.code;
                        if(error.code === 'auth/invalid-credential') msg = 'Incorrect email or password.';
                        alert(msg);
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Sign In';
                    }
                });
            }

            const signupFormEl = document.getElementById('signup-form');
            if(signupFormEl) {
                signupFormEl.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    console.log('Signup form submitted');

                    const email = document.getElementById('signup-email').value;
                    const password = document.getElementById('signup-password').value;
                    const confirmPassword = document.getElementById('signup-confirm-password').value;
                    const submitBtn = e.target.querySelector('button[type="submit"]');

                    if (password !== confirmPassword) { 
                        alert('Passwords do not match!'); 
                        return; 
                    }
                    
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Creating account...';
                    
                    try {
                        const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                        await window.handleSessionLogin(userCredential.user);
                    } catch (error) { 
                        console.error('Sign up error:', error); 
                        if (error.code === 'auth/email-already-in-use') {
                            if(confirm("Account exists. Log in instead?")) {
                                document.getElementById('signin-tab').click();
                            }
                        } else {
                            alert('Error signing up: ' + error.message); 
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Create Account';
                    }
                });
            }

            // Bind Google Buttons
            ['google-signin-btn', 'google-signup-btn'].forEach(id => {
                const btn = document.getElementById(id);
                if (btn) {
                    btn.addEventListener('click', window.handleGoogleSignIn);
                }
            });

        } catch (initError) {
            console.error("CRITICAL: Firebase failed to initialize.", initError);
            alert("System Error: Authentication service failed to load. Check console for details.");
        }
    </script>
</body>
</html>