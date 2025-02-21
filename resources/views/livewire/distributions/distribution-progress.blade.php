<div wire:poll.10s="calculateProgress"> <!-- ✅ Polling will call calculateProgress() -->
    <h4 class="text-sm font-medium text-gray-900">Distribution Progress</h4>

    <div class="relative w-full overflow-hidden rounded-full bg-gray-200 shadow-inner">
        <div class="h-4 rounded-full transition-all duration-500"
             style="width: {{ $progressData['percentage'] ?? 0 }}%;
                    background-color: {{ $progressColor ?? '#3498db' }};">
        </div>
    </div>

    <!-- Progress Percentage & Counts -->
    <div class="mt-2 flex justify-between text-sm font-medium text-gray-600">
        <span>{{ $progressData['claimed'] ?? 0 }} / {{ $progressData['total'] ?? 0 }} Claimed</span>
        <span class="font-bold text-gray-900">{{ $progressData['percentage'] ?? 0 }}%</span>
    </div>
</div>
