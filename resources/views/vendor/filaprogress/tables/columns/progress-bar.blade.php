@php
    $total = $getState()['total'];
    $claimed = $getState()['progress'];
    $remaining = $getState()['remaining'];

    // Calculate the percentage
    $progressPercent = $total > 0 ? round(($claimed / $total) * 100, 2) : 0;

    // Dynamic color based on progress
    if ($progressPercent == 100) {
        $progressColor = '#2980b9'; // Blue (Completed)
    } elseif ($progressPercent > 50) {
        $progressColor = '#27ae60'; // Green (>50% completed)
    } elseif ($progressPercent > 25) {
        $progressColor = '#f39c12'; // Orange (>25% completed)
    } else {
        $progressColor = '#e74c3c'; // Red (<25% completed)
    }
@endphp


<div class="parent">
    <div class="progress-container">
        <div class="progress-bar" style="width: {{ $progressPercent }}%; background-color: {{ $progressColor }};"></div>
        <div class="progress-text">
            <small>
                {{ $progressPercent }}%
            </small>
        </div>
    </div>

    <!-- Show exact claimed vs remaining values below -->
    <div class="progress-summary">
        <span class="progress-current">
             {{ $claimed }} / {{ $total }}  Remaining
        </span>
    </div>

</div>

<style>
    .parent{
        width: 100%;
    }
    .progress-container {
        width: 100%;
        background-color: #e5e7eb;
        border-radius: 0.375rem;
        height: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .progress-bar {
        height: 100%;
        border-radius: 0.375rem;
        transition: width 0.3s, background-color 0.3s;
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

    /* .progress-bar::after {
        content: '';
        display: block;
        height: 100%;
        border-radius: 0.375rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 25%, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, rgba(255, 255, 255, 0) 75%, rgba(255, 255, 255, 0) 100%);
        background-size: 40px 40px;
        animation: progress-bar-stripes 1s linear infinite;
    } */


    @keyframes progress-bar-stripes {
        from {
            background-position: 40px 0;
        }
        to {
            background-position: 0 0;
        }
    }
</style>
