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
                <h3 class="card-title">ENTER DETAILS</h3>
            </div>
            <div class="card-body">
                <form method="post" action="/initiatepay">
                   @csrf
                      <label>Enter Amount</label>
                        <input type="number" name="amount" class="form-control" placeholder="Enter Amount To Disburse" required>
                        <br>
                        <input type="hidden" name="reciever_code" value="{{$recipient_code}}" />

                        <label>Reason</label>
                        <select name="reason" class="form-control" required>
                          <option disabled selected>Select An Option</option>
                          <option value="Loan Disbursement">Loan Disbursement</option>
                          <option value="Investment Liquidation">Investment Liquidation</option>
                        </select>
                        {{-- <input type="text" name="reason" class="form-control" placeholder="What is The Reason For This Transfer" required> --}}
                        <br>
                       
                        <button type="submit" class="btn btn-primary btn-block">
                        VERIFY</button>
                    </form>
            </div>
        </div>
        </div>



      
</div>



@endsection
