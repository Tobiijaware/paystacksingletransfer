@extends('layouts.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Paystack</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href=".">Home</a></li>
            <li class="breadcrumb-item active">Paystack</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
<div class="row">
    
    <div class="col-lg-12">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><b>Account Verified {{$name}}</b></h3>
            </div>
            <div class="card-body">
               <h3><b> Account Number: {{$account_number}}</b></h3>
                <h3><b>Bank Code: {{$bank_code}}</b></h3>
                <form method="post" action="/addreptodb">
                   @csrf
                     <input hidden name="account_number" value="{{$account_number}}" />
                     <input hidden name="name" value="{{$name}}" />
                     <input hidden name="bank_code" value=" {{$bank_code}}" />
                       
                        <button type="submit" class="btn btn-primary btn-block">
                        verify</button>
                </form>
            </div>
        </div>
        </div>
</div>



@endsection
