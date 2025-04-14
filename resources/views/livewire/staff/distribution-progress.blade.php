<div>
    <header class="p-4 border-sm bg-white rounded-lg overflow-hidden shadow-sm">
        <div class="mx-auto">
            <h1 class="text-3xl text-main tracking-tight text-gray-900">
                {{ $record->distribution->title ?? 'Distribution Details' }}
            </h1>

            <!-- Distribution Details -->
            <div class="text-gray-600 text-sm mt-2 flex flex-col sm:flex-row sm:items-center flex-wrap">
                <div class="mr-3 mb-1">
                    <span class="font-semibold">Date:</span> {{ $record->distribution_date ? \Carbon\Carbon::parse($record->distribution_date)->format('F d, Y') : 'N/A' }}
                </div>
                <div class="mr-3 mb-1">
                    <span class="font-semibold">Location:</span> {{ $record->location ?? 'N/A' }}
                </div>
                <div class="mr-3 mb-1">
                    <span class="font-semibold">Total:</span> {{ $progressData['total'] ?? 0 }}
                </div>
                <div class="mr-3 mb-1">
                    <span class="font-semibold">Claimed:</span> {{ $progressData['claimed'] ?? 0 }}
                </div>
                <div class="mb-1">
                    <span class="font-semibold">Remaining:</span> {{ $progressData['remaining'] ?? 0 }}
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        @php
            $total = $progressData['total'] ?? 0;
            $claimed = $progressData['claimed'] ?? 0;
            $remaining = $progressData['remaining'] ?? 0;

            // Calculate the percentage
            $percentage = $total > 0 ? round(($claimed / $total) * 100, 1) : 0;

            // Dynamic color based on progress
            if ($percentage == 100) {
                $progressColor = '#2980b9'; // Blue (Completed)
                $textColor = 'white';
            } elseif ($percentage > 50) {
                $progressColor = '#27ae60'; // Green (>50% completed)
                $textColor = 'white';
            } elseif ($percentage > 25) {
                $progressColor = '#f39c12'; // Orange (>25% completed)
                $textColor = 'black';
            } else {
                $progressColor = '#e74c3c'; // Red (<25% completed)
                $textColor = 'white';
            }
        @endphp

        <div class="progress-container mt-3">
            <div class="progress-bar" style="width: {{ $percentage }}%; background-color: {{ $progressColor }};"></div>
            <div class="progress-text">
                <small style="color: {{ $textColor }};">
                    {{ $percentage }}%
                </small>
            </div>
        </div>
        <div class="progress-summary">
            <span class="progress-current">
                {{ $claimed }} / {{ $total }} Claimed
            </span>
        </div>
    </header>

    <style>
        .progress-container {
            width: 100%;
            background-color: #e5e7eb;
            border-radius: 0.375rem;
            height: 14px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .progress-bar {
            height: 100%;
            border-radius: 0.375rem;
            transition: width 0.6s ease, background-color 0.6s ease;
            width: 0;
        }
        .progress-text {
            text-align: center;
            font-size: 0.775rem;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .progress-summary {
            position: relative;
            text-align: right;
            font-size: 0.8rem;
            margin-top: 4px;
            color: #555;
            font-weight: bold;
        }

        .progress-bar::after {
            content: '';
            display: block;
            height: 100%;
            border-radius: 0.375rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 25%, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(255, 255, 255, 0) 75%, rgba(255, 255, 255, 0) 100%);
            background-size: 40px 40px;
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            from {
                background-position: 40px 0;
            }
            to {
                background-position: 0 0;
            }
        }
    </style>
</div>
