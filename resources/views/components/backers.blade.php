<p class="color-accent-5 text-center small pt-3 mb-4 pb-2">
    <div class="tick" data-value="0" data-did-init="handleTickInit3">
        <div style="margin-top: -2.5em; margin-bottom: .9em;" data-value-mapping="indexes" data-layout="horizontal center" data-transform="arrive(9, .001) -> round -> pad(000000) -> split -> delay(rtl, 100, 150)">
            <span data-view="flip"></span>
            <span data-view="flip"></span>
            <span data-view="flip"></span>
            <span class="tick-text-inline" style="color: rgb(235, 235, 235) !important;">,</span>
            <span data-view="flip">8</span>
            <span data-view="flip">5</span>
            <span data-view="flip">0</span>
            <span class="tick-text-inline" style="color: white !important;"> </span>
            <span class="tick-text-inline" style="color: white !important;">Pledges</span>
        </div>
    </div>
</p>

<script>
function handleTickInit3(tick) {
    setTimeout(function(){
        tick.value = {{ $totalAmount }};
    }, 100);
}
</script>

