<x-filament-panels::page>

    {{-- Soldes de congés --}}
    @php $balances = $this->getLeaveBalances(); @endphp
    @if(count($balances) > 0)
    <x-filament::section>
        <x-slot name="heading">Soldes de congés {{ now()->year }}</x-slot>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($balances as $b)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $b['type'] }}</p>
                <p class="mt-1 text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $b['remaining'] }}</p>
                <p class="text-xs text-gray-400">/ {{ $b['total'] }} j — utilisé : {{ $b['used'] }} j</p>
            </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif

    {{-- Demandes en attente --}}
    @php $pending = $this->getPendingLeaves(); @endphp
    @if(count($pending) > 0)
    <x-filament::section>
        <x-slot name="heading">Demandes en attente ({{ count($pending) }})</x-slot>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($pending as $p)
            <div class="flex items-center justify-between py-2">
                <div>
                    <span class="font-medium text-sm">{{ $p['type'] }}</span>
                    <span class="text-xs text-gray-400 ml-2">{{ $p['debut'] }} → {{ $p['fin'] }}</span>
                </div>
                <x-filament::badge color="warning">{{ $p['jours'] }} j</x-filament::badge>
            </div>
            @endforeach
        </div>
    </x-filament::section>
    @endif

    {{-- Bulletins de paie --}}
    <x-filament::section>
        <x-slot name="heading">Mes bulletins de paie</x-slot>
        {{ $this->table }}
    </x-filament::section>

</x-filament-panels::page>
