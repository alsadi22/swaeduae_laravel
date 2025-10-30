<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and personal details.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('patch')

        <!-- Account Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-md font-semibold text-gray-900 mb-4">{{ __('Account Information') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Full Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="text-sm text-gray-800">
                                {{ __('Your email address is unverified.') }}

                                <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                    <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" required />
                    <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-md font-semibold text-gray-900 mb-4">{{ __('Personal Information') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="gender" :value="__('Gender')" />
                    <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">{{ __('Select Gender') }}</option>
                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                </div>

                <div>
                    <x-input-label for="nationality" :value="__('Nationality')" />
                    <x-text-input id="nationality" name="nationality" type="text" class="mt-1 block w-full" :value="old('nationality', $user->nationality)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('nationality')" />
                </div>

                <div>
                    <x-input-label for="emirates_id" :value="__('Emirates ID (Optional)')" />
                    <x-text-input id="emirates_id" name="emirates_id" type="text" class="mt-1 block w-full" :value="old('emirates_id', $user->emirates_id)" />
                    <x-input-error class="mt-2" :messages="$errors->get('emirates_id')" />
                </div>

                <div>
                    <x-input-label for="emirate" :value="__('Emirate')" />
                    <select id="emirate" name="emirate" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        <option value="">{{ __('Select Emirate') }}</option>
                        <option value="abu_dhabi" {{ old('emirate', $user->emirate) === 'abu_dhabi' ? 'selected' : '' }}>{{ __('Abu Dhabi') }}</option>
                        <option value="dubai" {{ old('emirate', $user->emirate) === 'dubai' ? 'selected' : '' }}>{{ __('Dubai') }}</option>
                        <option value="sharjah" {{ old('emirate', $user->emirate) === 'sharjah' ? 'selected' : '' }}>{{ __('Sharjah') }}</option>
                        <option value="ajman" {{ old('emirate', $user->emirate) === 'ajman' ? 'selected' : '' }}>{{ __('Ajman') }}</option>
                        <option value="umm_al_quwain" {{ old('emirate', $user->emirate) === 'umm_al_quwain' ? 'selected' : '' }}>{{ __('Umm Al Quwain') }}</option>
                        <option value="ras_al_khaimah" {{ old('emirate', $user->emirate) === 'ras_al_khaimah' ? 'selected' : '' }}>{{ __('Ras Al Khaimah') }}</option>
                        <option value="fujairah" {{ old('emirate', $user->emirate) === 'fujairah' ? 'selected' : '' }}>{{ __('Fujairah') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('emirate')" />
                </div>

                <div>
                    <x-input-label for="city" :value="__('City')" />
                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>

                <div>
                    <x-input-label for="education_level" :value="__('Education Level')" />
                    <select id="education_level" name="education_level" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('Select Education Level') }}</option>
                        <option value="high_school" {{ old('education_level', $user->education_level) === 'high_school' ? 'selected' : '' }}>{{ __('High School') }}</option>
                        <option value="diploma" {{ old('education_level', $user->education_level) === 'diploma' ? 'selected' : '' }}>{{ __('Diploma') }}</option>
                        <option value="bachelor" {{ old('education_level', $user->education_level) === 'bachelor' ? 'selected' : '' }}>{{ __('Bachelor\'s Degree') }}</option>
                        <option value="master" {{ old('education_level', $user->education_level) === 'master' ? 'selected' : '' }}>{{ __('Master\'s Degree') }}</option>
                        <option value="phd" {{ old('education_level', $user->education_level) === 'phd' ? 'selected' : '' }}>{{ __('PhD') }}</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('education_level')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="address" :value="__('Address (Optional)')" />
                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $user->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </div>

        <!-- Volunteer Preferences -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-md font-semibold text-gray-900 mb-4">{{ __('Volunteer Preferences') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="skills" :value="__('Skills (Optional)')" />
                    <textarea id="skills" name="skills" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="e.g., Teaching, Event Management, Photography">{{ old('skills', $user->skills) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('skills')" />
                </div>

                <div>
                    <x-input-label for="interests" :value="__('Interests (Optional)')" />
                    <textarea id="interests" name="interests" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="e.g., Education, Environment, Community Service">{{ old('interests', $user->interests) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('interests')" />
                </div>

                <div>
                    <x-input-label for="languages" :value="__('Languages (Optional)')" />
                    <x-text-input id="languages" name="languages" type="text" class="mt-1 block w-full" :value="old('languages', $user->languages)" placeholder="e.g., Arabic, English, French" />
                    <x-input-error class="mt-2" :messages="$errors->get('languages')" />
                </div>

                <div class="flex items-center">
                    <input id="has_transportation" name="has_transportation" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="1" {{ old('has_transportation', $user->has_transportation) ? 'checked' : '' }}>
                    <label for="has_transportation" class="ml-2 block text-sm text-gray-900">
                        {{ __('I have my own transportation') }}
                    </label>
                    <x-input-error class="mt-2" :messages="$errors->get('has_transportation')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="bio" :value="__('Bio (Optional)')" />
                    <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tell us about yourself and your volunteer experience...">{{ old('bio', $user->bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-md font-semibold text-gray-900 mb-4">{{ __('Emergency Contact') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <x-input-label for="emergency_contact_name" :value="__('Contact Name')" />
                    <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full" :value="old('emergency_contact_name', $user->emergency_contact_name)" />
                    <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                </div>

                <div>
                    <x-input-label for="emergency_contact_phone" :value="__('Contact Phone')" />
                    <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="tel" class="mt-1 block w-full" :value="old('emergency_contact_phone', $user->emergency_contact_phone)" />
                    <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
                </div>

                <div>
                    <x-input-label for="emergency_contact_relationship" :value="__('Relationship')" />
                    <x-text-input id="emergency_contact_relationship" name="emergency_contact_relationship" type="text" class="mt-1 block w-full" :value="old('emergency_contact_relationship', $user->emergency_contact_relationship)" placeholder="e.g., Parent, Spouse, Sibling" />
                    <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_relationship')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Profile updated successfully!') }}</p>
            @endif
        </div>
    </form>
</section>
