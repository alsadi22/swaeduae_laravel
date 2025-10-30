<x-guest-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">{{ __('Register Your Organization') }}</h2>
            <p class="text-gray-600 mt-2">{{ __('Join SwaedUAE as an organization and start creating volunteer opportunities') }}</p>
        </div>

        <form method="POST" action="{{ route('organization.register') }}" class="space-y-6">
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

            <!-- Organization Information Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Organization Information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Organization Name -->
                    <div>
                        <x-input-label for="organization_name" :value="__('Organization Name')" />
                        <x-text-input id="organization_name" class="block mt-1 w-full" type="text" name="organization_name" :value="old('organization_name')" required />
                        <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
                    </div>

                    <!-- License Number -->
                    <div>
                        <x-input-label for="license_number" :value="__('License Number')" />
                        <x-text-input id="license_number" class="block mt-1 w-full" type="text" name="license_number" :value="old('license_number')" required />
                        <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                    </div>

                    <!-- Organization Phone -->
                    <div>
                        <x-input-label for="organization_phone" :value="__('Organization Phone')" />
                        <x-text-input id="organization_phone" class="block mt-1 w-full" type="tel" name="organization_phone" :value="old('organization_phone')" required />
                        <x-input-error :messages="$errors->get('organization_phone')" class="mt-2" />
                    </div>

                    <!-- Organization Email -->
                    <div>
                        <x-input-label for="organization_email" :value="__('Organization Email')" />
                        <x-text-input id="organization_email" class="block mt-1 w-full" type="email" name="organization_email" :value="old('organization_email')" required />
                        <x-input-error :messages="$errors->get('organization_email')" class="mt-2" />
                    </div>

                    <!-- Website -->
                    <div>
                        <x-input-label for="website" :value="__('Website (Optional)')" />
                        <x-text-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" placeholder="https://example.com" />
                        <x-input-error :messages="$errors->get('website')" class="mt-2" />
                    </div>

                    <!-- Founded Year -->
                    <div>
                        <x-input-label for="founded_year" :value="__('Founded Year')" />
                        <x-text-input id="founded_year" class="block mt-1 w-full" type="number" name="founded_year" :value="old('founded_year')" min="1900" max="{{ date('Y') }}" />
                        <x-input-error :messages="$errors->get('founded_year')" class="mt-2" />
                    </div>

                    <!-- Employee Count -->
                    <div>
                        <x-input-label for="employee_count" :value="__('Number of Employees')" />
                        <x-text-input id="employee_count" class="block mt-1 w-full" type="number" name="employee_count" :value="old('employee_count')" min="1" />
                        <x-input-error :messages="$errors->get('employee_count')" class="mt-2" />
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <x-input-label for="description" :value="__('Organization Description')" />
                    <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tell us about your organization, mission, and the type of volunteer work you offer..." required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <!-- Organization Address Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Organization Address') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <x-input-label for="organization_address" :value="__('Address')" />
                        <textarea id="organization_address" name="organization_address" rows="2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('organization_address') }}</textarea>
                        <x-input-error :messages="$errors->get('organization_address')" class="mt-2" />
                    </div>

                    <!-- City -->
                    <div>
                        <x-input-label for="organization_city" :value="__('City')" />
                        <x-text-input id="organization_city" class="block mt-1 w-full" type="text" name="organization_city" :value="old('organization_city')" required />
                        <x-input-error :messages="$errors->get('organization_city')" class="mt-2" />
                    </div>

                    <!-- Emirate -->
                    <div>
                        <x-input-label for="organization_emirate" :value="__('Emirate')" />
                        <select id="organization_emirate" name="organization_emirate" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">{{ __('Select Emirate') }}</option>
                            <option value="abu_dhabi" {{ old('organization_emirate') == 'abu_dhabi' ? 'selected' : '' }}>{{ __('Abu Dhabi') }}</option>
                            <option value="dubai" {{ old('organization_emirate') == 'dubai' ? 'selected' : '' }}>{{ __('Dubai') }}</option>
                            <option value="sharjah" {{ old('organization_emirate') == 'sharjah' ? 'selected' : '' }}>{{ __('Sharjah') }}</option>
                            <option value="ajman" {{ old('organization_emirate') == 'ajman' ? 'selected' : '' }}>{{ __('Ajman') }}</option>
                            <option value="umm_al_quwain" {{ old('organization_emirate') == 'umm_al_quwain' ? 'selected' : '' }}>{{ __('Umm Al Quwain') }}</option>
                            <option value="ras_al_khaimah" {{ old('organization_emirate') == 'ras_al_khaimah' ? 'selected' : '' }}>{{ __('Ras Al Khaimah') }}</option>
                            <option value="fujairah" {{ old('organization_emirate') == 'fujairah' ? 'selected' : '' }}>{{ __('Fujairah') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('organization_emirate')" class="mt-2" />
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <x-input-label for="organization_postal_code" :value="__('Postal Code (Optional)')" />
                        <x-text-input id="organization_postal_code" class="block mt-1 w-full" type="text" name="organization_postal_code" :value="old('organization_postal_code')" />
                        <x-input-error :messages="$errors->get('organization_postal_code')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Primary Contact Section -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Primary Contact') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Contact Name -->
                    <div>
                        <x-input-label for="primary_contact_name" :value="__('Full Name')" />
                        <x-text-input id="primary_contact_name" class="block mt-1 w-full" type="text" name="primary_contact_name" :value="old('primary_contact_name')" required />
                        <x-input-error :messages="$errors->get('primary_contact_name')" class="mt-2" />
                    </div>

                    <!-- Contact Position -->
                    <div>
                        <x-input-label for="primary_contact_position" :value="__('Position/Title')" />
                        <x-text-input id="primary_contact_position" class="block mt-1 w-full" type="text" name="primary_contact_position" :value="old('primary_contact_position')" required />
                        <x-input-error :messages="$errors->get('primary_contact_position')" class="mt-2" />
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <x-input-label for="primary_contact_email" :value="__('Email Address')" />
                        <x-text-input id="primary_contact_email" class="block mt-1 w-full" type="email" name="primary_contact_email" :value="old('primary_contact_email')" required />
                        <x-input-error :messages="$errors->get('primary_contact_email')" class="mt-2" />
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <x-input-label for="primary_contact_phone" :value="__('Phone Number')" />
                        <x-text-input id="primary_contact_phone" class="block mt-1 w-full" type="tel" name="primary_contact_phone" :value="old('primary_contact_phone')" required />
                        <x-input-error :messages="$errors->get('primary_contact_phone')" class="mt-2" />
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
                    {{ __('Register Organization') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>