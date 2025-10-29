@php
    $githubAccount = auth()->user()->socialAccounts()->where('provider', 'github')->first();
@endphp

<x-action-section>
    <x-slot name="title">
        {{ __('Connected social accounts') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Manage the social accounts linked to your profile.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Github') }}
        </h3>

        @if($githubAccount)
            <div class="flex items-center space-x-4 mt-4">
                <img src="{{ $githubAccount->avatar }}" alt="GitHub Avatar" class="w-12 h-12 rounded-full">
                <div>
                    <div><strong>{{ __('Name') }}:</strong> {{ $githubAccount->nickname ?? '---' }}</div>
                    <div>
                        <strong>{{ __('Github ID') }}:</strong> {{ $githubAccount->provider_user_id }}
                    </div>
                    <div>
                        <strong>{{ __('Profile') }}:</strong>
                        <a href="https://github.com/{{ $githubAccount->nickname }}" target="_blank" class="text-blue-600 underline">
                            {{ $githubAccount->nickname }}
                        </a>
                    </div>
                    <form method="POST" action="{{ route('socialite-extender.disconnect', ['provider' => 'github']) }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ml-4 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            {{ __('Disconnect GitHub') }}
                        </button>
                    </form>
                </div>
            </div>
        @else
            <form action="{{ route('socialite-extender.connect', ['provider' => 'github']) }}" method="get">
                <button type="submit" class="ml-4 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ __('Connect GitHub') }}
                </button>
            </form>
        @endif
    </x-slot>
</x-action-section>


