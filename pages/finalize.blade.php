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
                <h3 class="card-title">FINALIZE TRANSFER</h3>
            </div>
            <div class="card-body">
                <form method="post" action="/finalizepay">
                   @csrf
                      <label>Enter OTP</label>
                        <input type="number" name="otp" class="form-control" required>
                        <br>
                        <input type="hidden" name="transfer_code" value="{{$transfer_code}}"/>
                       
                        <button type="submit" class="btn btn-primary btn-block">
                        Pay</button>
                    </form>
            </div>
        </div>
        </div>
</div>



@endsection
