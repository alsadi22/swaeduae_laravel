<x-guest-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Join SwaedUAE') }}</h2>
            <p class="text-gray-600 mt-2">{{ __('Create your volunteer account and start making a difference in the UAE') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Account Information Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Account Information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                        <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Personal Information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Gender -->
                    <div>
                        <x-input-label for="gender" :value="__('Gender')" />
                        <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">{{ __('Select Gender') }}</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <!-- Nationality -->
                    <div>
                        <x-input-label for="nationality" :value="__('Nationality')" />
                        <x-text-input id="nationality" class="block mt-1 w-full" type="text" name="nationality" :value="old('nationality')" required />
                        <x-input-error :messages="$errors->get('nationality')" class="mt-2" />
                    </div>

                    <!-- Emirates ID -->
                    <div>
                        <x-input-label for="emirates_id" :value="__('Emirates ID (Optional)')" />
                        <x-text-input id="emirates_id" class="block mt-1 w-full" type="text" name="emirates_id" :value="old('emirates_id')" placeholder="784-XXXX-XXXXXXX-X" />
                        <x-input-error :messages="$errors->get('emirates_id')" class="mt-2" />
                    </div>

                    <!-- Emirate -->
                    <div>
                        <x-input-label for="emirate" :value="__('Emirate')" />
                        <select id="emirate" name="emirate" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">{{ __('Select Emirate') }}</option>
                            <option value="abu_dhabi" {{ old('emirate') == 'abu_dhabi' ? 'selected' : '' }}>{{ __('Abu Dhabi') }}</option>
                            <option value="dubai" {{ old('emirate') == 'dubai' ? 'selected' : '' }}>{{ __('Dubai') }}</option>
                            <option value="sharjah" {{ old('emirate') == 'sharjah' ? 'selected' : '' }}>{{ __('Sharjah') }}</option>
                            <option value="ajman" {{ old('emirate') == 'ajman' ? 'selected' : '' }}>{{ __('Ajman') }}</option>
                            <option value="umm_al_quwain" {{ old('emirate') == 'umm_al_quwain' ? 'selected' : '' }}>{{ __('Umm Al Quwain') }}</option>
                            <option value="ras_al_khaimah" {{ old('emirate') == 'ras_al_khaimah' ? 'selected' : '' }}>{{ __('Ras Al Khaimah') }}</option>
                            <option value="fujairah" {{ old('emirate') == 'fujairah' ? 'selected' : '' }}>{{ __('Fujairah') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('emirate')" class="mt-2" />
                    </div>

                    <!-- City -->
                    <div>
                        <x-input-label for="city" :value="__('City')" />
                        <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    <!-- Education Level -->
                    <div>
                        <x-input-label for="education_level" :value="__('Education Level')" />
                        <select id="education_level" name="education_level" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Select Education Level') }}</option>
                            <option value="high_school" {{ old('education_level') == 'high_school' ? 'selected' : '' }}>{{ __('High School') }}</option>
                            <option value="diploma" {{ old('education_level') == 'diploma' ? 'selected' : '' }}>{{ __('Diploma') }}</option>
                            <option value="bachelor" {{ old('education_level') == 'bachelor' ? 'selected' : '' }}>{{ __('Bachelor\'s Degree') }}</option>
                            <option value="master" {{ old('education_level') == 'master' ? 'selected' : '' }}>{{ __('Master\'s Degree') }}</option>
                            <option value="phd" {{ old('education_level') == 'phd' ? 'selected' : '' }}>{{ __('PhD') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('education_level')" class="mt-2" />
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-4">
                    <x-input-label for="address" :value="__('Address')" />
                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address') }}</textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            <!-- Volunteer Preferences Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Volunteer Preferences') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Skills -->
                    <div>
                        <x-input-label for="skills" :value="__('Skills & Expertise')" />
                        <textarea id="skills" name="skills" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="e.g., Teaching, Event Management, First Aid, IT Support">{{ old('skills') }}</textarea>
                        <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                    </div>

                    <!-- Interests -->
                    <div>
                        <x-input-label for="interests" :value="__('Areas of Interest')" />
                        <textarea id="interests" name="interests" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="e.g., Education, Environment, Community Service, Healthcare">{{ old('interests') }}</textarea>
                        <x-input-error :messages="$errors->get('interests')" class="mt-2" />
                    </div>

                    <!-- Languages -->
                    <div>
                        <x-input-label for="languages" :value="__('Languages Spoken')" />
                        <x-text-input id="languages" class="block mt-1 w-full" type="text" name="languages" :value="old('languages')" placeholder="e.g., Arabic, English, Hindi" />
                        <x-input-error :messages="$errors->get('languages')" class="mt-2" />
                    </div>

                    <!-- Transportation -->
                    <div>
                        <x-input-label for="has_transportation" :value="__('Transportation')" />
                        <select id="has_transportation" name="has_transportation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Select Option') }}</option>
                            <option value="1" {{ old('has_transportation') == '1' ? 'selected' : '' }}>{{ __('I have my own transportation') }}</option>
                            <option value="0" {{ old('has_transportation') == '0' ? 'selected' : '' }}>{{ __('I need transportation assistance') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('has_transportation')" class="mt-2" />
                    </div>
                </div>

                <!-- Bio -->
                <div class="mt-4">
                    <x-input-label for="bio" :value="__('Tell us about yourself')" />
                    <textarea id="bio" name="bio" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Share your motivation for volunteering and what you hope to achieve...">{{ old('bio') }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Emergency Contact') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Emergency Contact Name -->
                    <div>
                        <x-input-label for="emergency_contact_name" :value="__('Contact Name')" />
                        <x-text-input id="emergency_contact_name" class="block mt-1 w-full" type="text" name="emergency_contact_name" :value="old('emergency_contact_name')" />
                        <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
                    </div>

                    <!-- Emergency Contact Phone -->
                    <div>
                        <x-input-label for="emergency_contact_phone" :value="__('Contact Phone')" />
                        <x-text-input id="emergency_contact_phone" class="block mt-1 w-full" type="tel" name="emergency_contact_phone" :value="old('emergency_contact_phone')" />
                        <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-2" />
                    </div>

                    <!-- Emergency Contact Relationship -->
                    <div>
                        <x-input-label for="emergency_contact_relationship" :value="__('Relationship')" />
                        <x-text-input id="emergency_contact_relationship" class="block mt-1 w-full" type="text" name="emergency_contact_relationship" :value="old('emergency_contact_relationship')" placeholder="e.g., Parent, Spouse, Sibling" />
                        <x-input-error :messages="$errors->get('emergency_contact_relationship')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex items-start">
                    <input id="terms" name="terms" type="checkbox" class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                    <label for="terms" class="ml-3 text-sm text-gray-700">
                        {{ __('I agree to the') }} 
                        <a href="#" class="text-indigo-600 hover:text-indigo-500 underline">{{ __('Terms and Conditions') }}</a> 
                        {{ __('and') }} 
                        <a href="#" class="text-indigo-600 hover:text-indigo-500 underline">{{ __('Privacy Policy') }}</a>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('terms')" class="mt-2" />
            </div>

            <!-- Submit Section -->
            <div class="flex items-center justify-between pt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already have an account?') }}
                </a>

                <x-primary-button class="px-8 py-3">
                    {{ __('Create Account') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
