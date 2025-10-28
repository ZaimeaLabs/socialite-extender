@php
    $githubAccount = auth()->user()->socialAccounts()->where('provider', 'github')->first();
@endphp

@if($githubAccount)
    <div class="flex items-center space-x-4 mt-4">
        <img src="{{ $githubAccount->avatar }}" alt="GitHub Avatar" class="w-12 h-12 rounded-full">
        <div>
            <div><strong>Nume:</strong> {{ $githubAccount->nickname ?? '---' }}</div>
            <div>
                <strong>GitHub ID:</strong> {{ $githubAccount->provider_user_id }}
            </div>
            <div>
                <strong>Profil:</strong>
                <a href="https://github.com/{{ $githubAccount->nickname }}" target="_blank" class="text-blue-600 underline">
                    {{ $githubAccount->nickname }}
                </a>
            </div>
            <form method="POST" action="{{ route('socialite-extender.disconnect', ['provider' => 'github']) }}" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Deconectează GitHub
                </button>
            </form>
        </div>
    </div>
@else
    <form action="{{ route('socialite-extender.connect', ['provider' => 'github']) }}" method="get">
        <button type="submit" class="btn btn-primary">
            Conectează GitHub
        </button>
    </form>
@endif
