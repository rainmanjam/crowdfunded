@foreach ($topPledges as $tPledge)
    <div class="content-block">
        <h5>{{$tPledge->display_name ?? 'Anonymous'}} - ${{number_format($tPledge->pledge_amount / 100)}}</h5>
        <hr>
        <p>{{$tPledge->message}}</p>
    </div>
@endforeach
