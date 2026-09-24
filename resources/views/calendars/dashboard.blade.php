@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/calendars/dashboard.css') }}">
@endsection

@section('title', 'お手伝いダッシュボード')

@section('content')
  <div class="dashboard">
    <div class="dashboard__area">

      <div class="dashboard__title">月別おこづかい金額</div>
      
        <div class="graph__container">
          @foreach($monthlyTotals as $month => $total)
            @php
              $percent = round(($total / $maxAmount) * 100);
            @endphp

            <a href="{{ route('calendars.index', ['month' => $month]) }}" class="chart__link">
            <div class="graph__column">
              <!-- 上部の金額表示 -->
              <span class="graph__value">¥{{ number_format($total) }}</span>
            
              <!-- インラインスタイルで動的に高さをセット -->
                <div class="graph__bar" style="height: {{ $percent }}%;"></div>
        
              <!-- 下部の「月」表示（例: 05月） -->
              <span class="graph__label">{{ \Carbon\Carbon::parse($month)->format('m月') }}</span>
            </div>
            </a>
          @endforeach
        </div>
      </div>
  </div>
@endsection