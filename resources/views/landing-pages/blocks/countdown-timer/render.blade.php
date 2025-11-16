@php
    $targetDatetime = $data['target_datetime'] ?? '';
    $numberColor = $data['number_color'] ?? '#DFAD42';
    $labelColor = $data['label_color'] ?? '#DFAD42';
    $backgroundColor = $data['background_color'] ?? '#000000';
    $labelDays = $data['label_days'] ?? 'Days';
    $labelHours = $data['label_hours'] ?? 'Hours';
    $labelMinutes = $data['label_minutes'] ?? 'Minutes';
    $labelSeconds = $data['label_seconds'] ?? 'Seconds';
    $removeOnCompletion = $data['remove_on_completion'] ?? false;
@endphp

<x-landing-page-block.wrapper :data="$data">
    <div
        x-data="{
            targetDate: '{{ $targetDatetime }}',
            removeOnCompletion: {{ $removeOnCompletion ? 'true' : 'false' }},
            days: 0,
            hours: 0,
            minutes: 0,
            seconds: 0,
            isComplete: false,
            isHidden: false,
            interval: null,

            init() {
                if (!this.targetDate) {
                    return;
                }
                this.updateCountdown();
                this.interval = setInterval(() => this.updateCountdown(), 1000);
            },

            updateCountdown() {
                const now = new Date().getTime();
                const target = new Date(this.targetDate).getTime();
                const distance = target - now;

                if (distance <= 0) {
                    this.isComplete = true;

                    if (this.removeOnCompletion) {
                        this.isHidden = true;
                        if (this.interval) {
                            clearInterval(this.interval);
                        }
                        return;
                    }

                    // Count up from zero
                    const elapsed = Math.abs(distance);
                    this.days = Math.floor(elapsed / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((elapsed % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((elapsed % (1000 * 60)) / 1000);
                } else {
                    // Countdown to target
                    this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                }
            },

            formatNumber(num) {
                return String(num).padStart(2, '0');
            },

            destroy() {
                if (this.interval) {
                    clearInterval(this.interval);
                }
            }
        }"
        x-show="!isHidden"
        class="max-w-7xl mx-auto"
        style="background-color: {{ $backgroundColor }}; padding: 3rem; border-radius: 0.5rem;"
    >
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            {{-- Days --}}
            <div class="text-center">
                <div
                    class="text-5xl md:text-6xl font-bold mb-2"
                    style="color: {{ $numberColor }}"
                    x-text="formatNumber(days)"
                >00</div>
                <div class="text-sm md:text-base uppercase tracking-wide opacity-80" style="color: {{ $labelColor }}">
                    {{ $labelDays }}
                </div>
            </div>

            {{-- Hours --}}
            <div class="text-center">
                <div
                    class="text-5xl md:text-6xl font-bold mb-2"
                    style="color: {{ $numberColor }}"
                    x-text="formatNumber(hours)"
                >00</div>
                <div class="text-sm md:text-base uppercase tracking-wide opacity-80" style="color: {{ $labelColor }}">
                    {{ $labelHours }}
                </div>
            </div>

            {{-- Minutes --}}
            <div class="text-center">
                <div
                    class="text-5xl md:text-6xl font-bold mb-2"
                    style="color: {{ $numberColor }}"
                    x-text="formatNumber(minutes)"
                >00</div>
                <div class="text-sm md:text-base uppercase tracking-wide opacity-80" style="color: {{ $labelColor }}">
                    {{ $labelMinutes }}
                </div>
            </div>

            {{-- Seconds --}}
            <div class="text-center">
                <div
                    class="text-5xl md:text-6xl font-bold mb-2"
                    style="color: {{ $numberColor }}"
                    x-text="formatNumber(seconds)"
                >00</div>
                <div class="text-sm md:text-base uppercase tracking-wide opacity-80" style="color: {{ $labelColor }}">
                    {{ $labelSeconds }}
                </div>
            </div>
        </div>
    </div>
</x-landing-page-block.wrapper>
