@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<script>
    window.location.href = "{{ route('tenant.modules.financials.expenses.index') }}";
</script>
@endsection
