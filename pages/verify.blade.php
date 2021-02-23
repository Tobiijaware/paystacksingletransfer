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
    
    <div class="col-lg-4">
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"></h3>
            </div>
            <div class="card-body">
                <form method="post" action="/checkaccnumber">
                   @csrf
                      <label>Account Number</label>
                        <input type="text" name="account_number" class="form-control" required>
                        <br>
                        
                        <label>Bank Code</label>
                        <input type="number" name="bankcode" class="form-control" required>
                        <br>
                       
                        <button type="submit" class="btn btn-primary btn-block">
                        verify</button>
                    </form>
            </div>
        </div>
        </div>
</div>



@endsection
