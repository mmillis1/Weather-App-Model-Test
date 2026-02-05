@props(['city', 'country', 'temperature', 'feelsLike', 'condition', 'description', 'windSpeed', 'humidity', 'timestamp'])

<div class="bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg p-6 shadow-lg transform transition-all duration-500 opacity-0 translate-y-4"
     style="animation: slideIn 0.5s ease-out forwards;">
    <!-- Location -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">
            {{ $city }}@if($country), {{ $country }}@endif
        </h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $description }}</p>
    </div>

    <!-- Temperature Display -->
    <div class="text-center mb-6">
        <div class="text-6xl font-bold text-[#f53003] dark:text-[#FF4433] mb-2">
            {{ $temperature }}°C
        </div>
        <p class="text-[#706f6c] dark:text-[#A1A09A]">
            Feels like {{ $feelsLike }}°C
        </p>
    </div>

    <!-- Weather Details Grid -->
    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
        <div class="text-center">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Condition</p>
            <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $condition }}</p>
        </div>
        <div class="text-center">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Humidity</p>
            <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $humidity }}%</p>
        </div>
        <div class="text-center col-span-2">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Wind Speed</p>
            <p class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $windSpeed }} m/s</p>
        </div>
    </div>

    <!-- Timestamp -->
    <div class="text-center mt-4 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
            Updated {{ \Carbon\Carbon::createFromTimestamp($timestamp)->diffForHumans() }}
        </p>
    </div>
</div>

<style>
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(1rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
