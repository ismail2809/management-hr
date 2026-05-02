<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-bolt" color="primary" size="lg">
                Générer les fiches de paie
            </x-filament::button>
        </div>
    </form>

    @if ($generated)
        <div class="mt-8">
            <x-filament::section heading="Résultats de génération">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 font-medium text-gray-600 dark:text-gray-300">Employé</th>
                                <th class="px-4 py-2 font-medium text-gray-600 dark:text-gray-300">Statut</th>
                                <th class="px-4 py-2 font-medium text-gray-600 dark:text-gray-300">Détail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($results as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-2 font-medium">{{ $row['name'] }}</td>
                                    <td class="px-4 py-2">
                                        @if ($row['status'] === 'généré')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Généré
                                            </span>
                                        @elseif ($row['status'] === 'ignoré')
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Ignoré
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Erreur
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-500">{{ $row['detail'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
