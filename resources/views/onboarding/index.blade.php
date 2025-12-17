<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Profile</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <style>
        .step { display: none; }
        .step.active { display: block; animation: fadeIn 0.5s; }
        .selected-option { border: 4px solid #F97316; transform: scale(1.05); } /* Orange border */
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-[#F8E8D4] min-h-screen flex items-center justify-center font-sans">

<div class="w-full max-w-md p-6">
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <strong class="font-bold">Please check the following:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('onboarding.store') }}" method="POST" id="onboardingForm">
        @csrf

        <div class="flex justify-between mb-8 px-4 gap-2">
            <div id="bar1" class="h-2 flex-1 bg-green-600 rounded-full transition-colors duration-300"></div>
            <div id="bar2" class="h-2 flex-1 bg-gray-300 rounded-full transition-colors duration-300"></div>
            <div id="bar3" class="h-2 flex-1 bg-gray-300 rounded-full transition-colors duration-300"></div>
            <div id="bar4" class="h-2 flex-1 bg-gray-300 rounded-full transition-colors duration-300"></div>
        </div>

        <div id="step1" class="step active text-center">
            <h2 class="text-2xl font-bold text-[#6B5D45] mb-2">Preferred Name</h2>
            <p class="text-[#6B5D45] mb-4">What should we call you?</p>
            
            <input type="text" name="display_name" required placeholder="Name" 
                   class="w-full p-3 rounded-full border-none shadow-sm mb-8 text-center focus:ring-2 focus:ring-orange-400 focus:outline-none">

            <h3 class="text-lg text-[#6B5D45] mb-4">Choose your Avatar</h3>
            
            <input type="hidden" name="avatar" id="avatarInput" required>

            <div class="grid grid-cols-3 gap-4 mb-8">
                <img src="{{ asset('assets/images/avatar_avocado.png') }}" onclick="selectAvatar(this, 'avocado')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#C8E6C9] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">
                
                <img src="{{ asset('assets/images/avatar_sushi.png') }}" onclick="selectAvatar(this, 'sushi')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#B3E5FC] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">
                
                <img src="{{ asset('assets/images/avatar_taco.png') }}" onclick="selectAvatar(this, 'taco')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#FFCC80] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">

                <img src="{{ asset('assets/images/avatar_donut.png') }}" onclick="selectAvatar(this, 'donut')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#F8BBD0] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">

                <img src="{{ asset('assets/images/avatar_pizza.png') }}" onclick="selectAvatar(this, 'pizza')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#FFAB91] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">

                <img src="{{ asset('assets/images/avatar_strawberry.png') }}" onclick="selectAvatar(this, 'strawberry')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#81D4FA] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">

                <img src="{{ asset('assets/images/avatar_burger.png') }}" onclick="selectAvatar(this, 'burger')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#FFF59D] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">

                <img src="{{ asset('assets/images/avatar_ramen.png') }}" onclick="selectAvatar(this, 'ramen')" 
                     class="avatar-opt w-20 h-20 rounded-full bg-[#D1C4E9] p-2 cursor-pointer mx-auto hover:scale-105 transition object-contain">
            </div>

            <button type="button" onclick="nextStep(2)" class="w-full bg-[#F97316] text-white py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 transition">Next</button>
        </div>

        <div id="step2" class="step text-center">
            <h2 class="text-2xl font-bold text-[#6B5D45] mb-2">Select your Main Goal</h2>
            <p class="text-[#6B5D45] mb-6">Choose at least one goal:</p>

            <div class="space-y-3 mb-8 text-left">
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="checkbox" name="goals[]" value="lose_weight" class="mr-2 accent-orange-500"> Lose weight
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="checkbox" name="goals[]" value="gain_weight" class="mr-2 accent-orange-500"> Gain weight
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="checkbox" name="goals[]" value="maintain_weight" class="mr-2 accent-orange-500"> Maintain weight
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="checkbox" name="goals[]" value="cook" class="mr-2 accent-orange-500"> Learn how to cook
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="checkbox" name="goals[]" value="recipes" class="mr-2 accent-orange-500"> Discover recipes
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="checkbox" name="goals[]" value="healthy" class="mr-2 accent-orange-500"> Eat healthy
                </label>
            </div>

            <button type="button" onclick="nextStep(3)" class="w-full bg-[#F97316] text-white py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 mb-3">Next</button>
            <button type="button" onclick="nextStep(1)" class="w-full bg-white text-[#6B5D45] py-3 rounded-full font-bold shadow hover:bg-gray-50">Go Back</button>
        </div>

        <div id="step3" class="step text-center">
            <h2 class="text-2xl font-bold text-[#6B5D45] mb-2">Select your Activity Level</h2>
            <p class="text-[#6B5D45] mb-6">Choose what describes you best:</p>

            <div class="space-y-3 mb-8 text-left">
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="radio" name="activity_level" value="sedentary" class="mr-2 accent-orange-500" required>
                    <span class="font-bold">Sedentary</span> <br> <span class="text-sm text-gray-500 ml-6">Spend most of the day sitting</span>
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="radio" name="activity_level" value="lightly_active" class="mr-2 accent-orange-500">
                    <span class="font-bold">Lightly active</span> <br> <span class="text-sm text-gray-500 ml-6">Spend a good part of the day on your feet</span>
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="radio" name="activity_level" value="active" class="mr-2 accent-orange-500">
                    <span class="font-bold">Active</span> <br> <span class="text-sm text-gray-500 ml-6">Spend a good part of the day doing physical activity</span>
                </label>
                <label class="block bg-white p-4 rounded-xl shadow-sm cursor-pointer hover:bg-orange-50 transition">
                    <input type="radio" name="activity_level" value="very_active" class="mr-2 accent-orange-500">
                    <span class="font-bold">Very active</span> <br> <span class="text-sm text-gray-500 ml-6">Spend a good part of the day doing heavy physical activity</span>
                </label>
            </div>

            <button type="button" onclick="nextStep(4)" class="w-full bg-[#F97316] text-white py-3 rounded-full font-bold shadow-lg hover:bg-orange-600 mb-3">Next</button>
            <button type="button" onclick="nextStep(2)" class="w-full bg-white text-[#6B5D45] py-3 rounded-full font-bold shadow hover:bg-gray-50">Go Back</button>
        </div>

        <div id="step4" class="step text-center">
            <h2 class="text-2xl font-bold text-[#6B5D45] mb-4">Tell us about yourself</h2>

            <div class="flex gap-4 justify-center mb-6">
                <label class="cursor-pointer">
                    <input type="radio" name="sex" value="male" class="hidden peer" required>
                    <div class="bg-white px-6 py-3 rounded-xl shadow text-[#6B5D45] transition peer-checked:bg-blue-100 peer-checked:border-2 peer-checked:border-blue-500 hover:bg-gray-50">
                        ♂ Male
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="sex" value="female" class="hidden peer">
                    <div class="bg-white px-6 py-3 rounded-xl shadow text-[#6B5D45] transition peer-checked:bg-pink-100 peer-checked:border-2 peer-checked:border-pink-500 hover:bg-gray-50">
                        ♀ Female
                    </div>
                </label>
            </div>

            <div class="space-y-4 mb-8 text-left">
                <div>
                    <label class="block text-[#6B5D45] font-bold mb-1">Age</label>
                    <input type="number" name="age" class="w-full p-3 rounded-lg border-none shadow-sm focus:ring-2 focus:ring-orange-400" required>
                </div>
                
                <div>
                    <label class="block text-[#6B5D45] font-bold mb-1">Height</label>
                    <div class="flex">
                        <input type="number" name="height" class="w-full p-3 rounded-l-lg border-none shadow-sm focus:ring-2 focus:ring-orange-400" required>
                        <span class="bg-[#E69248] text-white px-4 py-3 rounded-r-lg font-bold">cm</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[#6B5D45] font-bold mb-1">Weight</label>
                    <div class="flex">
                        <input type="number" name="current_weight" class="w-full p-3 rounded-l-lg border-none shadow-sm focus:ring-2 focus:ring-orange-400" required>
                        <span class="bg-[#E69248] text-white px-4 py-3 rounded-r-lg font-bold">kg</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[#6B5D45] font-bold mb-1">Goal Weight</label>
                    <div class="flex">
                        <input type="number" name="goal_weight" class="w-full p-3 rounded-l-lg border-none shadow-sm focus:ring-2 focus:ring-orange-400" required>
                        <span class="bg-[#E69248] text-white px-4 py-3 rounded-r-lg font-bold">kg</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-white text-[#4CAF50] border-2 border-[#4CAF50] py-3 rounded-full font-bold shadow-lg hover:bg-[#4CAF50] hover:text-white transition">Create Account</button>
            <button type="button" onclick="nextStep(3)" class="mt-3 w-full bg-transparent text-gray-500 text-sm hover:underline">Go Back</button>
        </div>

    </form>
</div>

<script>
    function nextStep(targetStep) {
        // Validation Logic: Prevent moving forward if current step is empty
        const currentStepNum = targetStep - 1;
        
        // If we are moving FORWARD (current < target), validate input
        if (targetStep > 1 && currentStepNum > 0) {
            const currentStepDiv = document.getElementById('step' + currentStepNum);
            const requiredInputs = currentStepDiv.querySelectorAll('input[required]');
            
            let allValid = true;
            requiredInputs.forEach(input => {
                if (!input.value) {
                    allValid = false;
                    // Trigger browser validation message
                    input.reportValidity(); 
                }
            });

            if (!allValid) return; // Stop if validation fails
        }

        // Hide all steps
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        // Show target step
        document.getElementById('step' + targetStep).classList.add('active');
        
        // Update progress bar
        updateProgressBar(targetStep);
    }

    function selectAvatar(imgElement, avatarValue) {
        // Remove border from all avatars
        document.querySelectorAll('.avatar-opt').forEach(el => el.classList.remove('selected-option'));
        // Add border to clicked one
        imgElement.classList.add('selected-option');
        // Set hidden input value
        document.getElementById('avatarInput').value = avatarValue;
    }

    function updateProgressBar(step) {
        // Reset all bars to gray first
        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar' + i);
            bar.classList.remove('bg-green-600', 'bg-[#ea8724]'); // Remove colors
            bar.classList.add('bg-gray-300'); // Set default gray
        }

        // Apply colors based on current step
        if (step >= 1) {
            document.getElementById('bar1').classList.remove('bg-gray-300');
            document.getElementById('bar1').classList.add('bg-green-600');
        }
        if (step >= 2) {
            document.getElementById('bar2').classList.remove('bg-gray-300');
            document.getElementById('bar2').classList.add('bg-[#ea8724]'); // Orange
        }
        if (step >= 3) {
            document.getElementById('bar3').classList.remove('bg-gray-300');
            document.getElementById('bar3').classList.add('bg-green-600');
        }
        if (step >= 4) {
            document.getElementById('bar4').classList.remove('bg-gray-300');
            document.getElementById('bar4').classList.add('bg-[#ea8724]'); // Orange
        }
    }
</script>

</body>
</html>