<div style="margin-top: 4em" class="progress custom-progressbar">
    <div class="progress-bar" role="progressbar" style="width: {{$pledgePercent}}%;"
         aria-valuenow="{{$pledgePercent}}" aria-valuemin="0" aria-valuemax="{{$pledgeGoal}}">
        <span class="progress-indicator">{{$pledgePercent}}%</span>
    </div>
</div>

<p class="color-accent-5 text-center small mb-4 pb-3">
    <div class="tick" data-value="0" data-did-init="handleTickInit2">
        <div style="margin-top: -2.5em; margin-bottom: .9em;" data-value-mapping="indexes" data-layout="horizontal center" data-transform="arrive(1000, .5) -> round -> pad(00000000) -> split -> delay(rtl, 100, 150)">

            <span class="tick-text-inline" style="color: rgb(235, 235, 235) !important;">$</span>

            <span data-view="flip">1</span>
            <span data-view="flip">2</span>

            <span class="tick-text-inline" style="color: rgb(235, 235, 235) !important;">,</span>

            <span data-view="flip">8</span>
            <span data-view="flip">5</span>
            <span data-view="flip">0</span>

            <span class="tick-text-inline" style="color: rgb(235, 235, 235) !important;">,</span>

            <span data-view="flip">8</span>
            <span data-view="flip">5</span>
            <span data-view="flip">0</span>

            <span class="tick-text-inline" style="color: rgb(235, 235, 235) !important; font-size: 1em; padding-top: .6em;}">.00</span>
            <span class="tick-text-inline"> </span>
            <span class="tick-text-inline" style="color: white !important;">Pledged</span>

        </div>
    </div>
</p>

<script>
    function handleTickInit2(tick) {
        setTimeout(function(){
            tick.value = {{ $totalPledgeAmount }};
        }, 300);
    }
</script>

