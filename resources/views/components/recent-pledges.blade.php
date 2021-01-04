@foreach ($recentPledges as $rPledge)
<div class="content-block">
    <h5>{{$rPledge->display_name ?? 'Anonymous'}} - ${{number_format($rPledge->pledge_amount / 100)}}</h5>
    <hr>
    <p>{{$rPledge->message}}</p>
</div>
@endforeach
