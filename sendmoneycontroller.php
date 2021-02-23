<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use App\Models\TransferReci;
use App\Models\Wallet;
use Illuminate\Http\Request;

class sendmoneycontroller extends Controller
{


public function viewverify(){
    return view('admin.verify');
}















   public function verifyaccno(Request $request){

    $accno = $request['account_number'];
    $bankcode = $request['bankcode'];
$ref = $request['confirmpaypro'];
session()->put('ref',$ref);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=".rawurlencode($accno)."&bank_code=".rawurlencode($bankcode),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer sk_test_d87b35348acccdb6c6036ea86a39c17dcc55ef59",
        "Cache-Control: no-cache",
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $result = json_decode($response);
        $verify = $result->status;
    }

    if($verify){
        $name = $result->data->account_name;
        //dd($name);
        //echo "<script> alert()</script>";
        return view('admin/recipient')->with(['name'=>$name,'account_number'=> $accno, 'bank_code' => $bankcode]);
        //header("locaton: recipient.php?name='.$name.'&account_number='.$accno.'&bank_code='.$bankcode");
    }
   }




   public function recipient(Request $request){
       $name = $request['name'];
       $accno = $request['account_number'];
       $bankcode = $request['bank_code'] ;
    $url = "https://api.paystack.co/transferrecipient";
    $fields = [
      'type' => "nuban",
      'name' => $name,
      'account_number' => $accno,
      'bank_code' => $bankcode,
      'currency' => "NGN"
    ];
    $fields_string = http_build_query($fields);
    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Authorization: Bearer sk_test_d87b35348acccdb6c6036ea86a39c17dcc55ef59",
      "Cache-Control: no-cache",
    ));
    
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    
    //execute post
    $result = curl_exec($ch);
    $info = json_decode($result);
    $receiver_name = $info->data->name;
    $receiver_code = $info->data->recipient_code;
    $type = $info->data->type;
    $account_number = $info->data->details->account_number;
    $bank_code =$info->data->details->bank_code;
    $bank_name =$info->data->details->bank_name;
    $currency = $info->data->currency;
    $createdAt= $info->data->createdAt;
     //return $receiver_name;
        
    if($info->status){
        $check = DB::select("SELECT * FROM transfer_recipient WHERE name = '$receiver_name'");
        //TransferReci::where('name', $receiver_name)->first();
        // return count($check);
        if(count($check) > 0 ){
          return view("admin/initiate")->with(['recipient_code'=>$receiver_code]);
        }else{
          $sql = DB::select("INSERT INTO transfer_recipient (name,recipient_code,type,account_number,bank_code,bank_name,currency,created_at) VALUES ('$receiver_name',' $receiver_code','$type',' $account_number','$bank_code','$bank_name','$currency','$createdAt')");
          if($sql){
            return view("admin/initiate")->with(['recipient_code'=>$receiver_code]);
          }
          return redirect("loanmanaging")->with('error', 'An Error Occurred');
        }
    }
  }

public function initiate(Request $request){
    $receiver_code = $request['reciever_code'];
    $reason = $request['reason'];
    session()->put('reason', $reason);
   // return $receiver_code;
  
    $url = "https://api.paystack.co/transfer/";
    $fields = [
      'source' => "balance",
      'amount' => $request['amount']*100,
      'recipient' => $receiver_code,
      'reason' => $request['reason']
    ];
    $fields_string = http_build_query($fields);
    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Authorization: Bearer sk_test_d87b35348acccdb6c6036ea86a39c17dcc55ef59",
      "Cache-Control: no-cache",
    ));
    
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    
    //execute post
    $result = curl_exec($ch);
    $initiate = json_decode($result);

    $status = $initiate->status;
    //dd($initiate);
    $message = $initiate->data->status;
    $reference = $initiate->data->reference;
    $amount = $initiate->data->amount;
    $reason= $initiate->data->reason;
    $transfer_code = $initiate->data->transfer_code;
    $createdAt = $initiate->data->createdAt;

    if($status == "true"){
        return view("admin/finalize")->with(['transfer_code'=>$transfer_code]);
        // $sql = DB::select("INSERT INTO transfer_initiated (reference,amount_in_kobo,reason,status,transfer_code,created_at) VALUES ('$referrence','$amount','$reason',' $status','$transfer_code','$createdAt')");
      
    }
    // elseif($message == "Transfer requires OTP to continue"){
    //     return view("admin/finalize")->with(['transfer_code'=>$transfer_code]);
    //     //header("locaton: finalize_transfer.php?transfer_code='.$transfer_code.'&recipient_code='.$receiver_code");
    // }
}



public function finalize(Request $request){
    $otp = $request['otp'];
    $transfer_code =  $request['transfer_code'];
    //return $transfer_code;
   
    $url = "https://api.paystack.co/transfer/finalize_transfer";
  $fields = [
    "transfer_code" =>  $transfer_code, 
    "otp" => $otp
  ];
  $fields_string = http_build_query($fields);
  //open connection
  $ch = curl_init();
  
  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer sk_test_d87b35348acccdb6c6036ea86a39c17dcc55ef59",
    "Cache-Control: no-cache",
  ));
  
  //So that curl_exec returns the contents of the cURL; rather than echoing it
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
  
  //execute post
  $result = curl_exec($ch);
  $initiate = json_decode($result);
  //echo $result;
 
     $status = $initiate->status;
     $message = $initiate->data->status;
     $reference = $initiate->data->reference;
     $amount = $initiate->data->amount;
     $reason= $initiate->data->reason;
     $transfer_code = $initiate->data->transfer_code;
     $createdAt = $initiate->data->createdAt;
 
     if($status == "true"){
         $sql = DB::select("INSERT INTO transfer_initiated (reference,amount_in_kobo,reason,status,transfer_code,created_at) VALUES ('$reference','$amount','$reason',' $status','$transfer_code','$createdAt')");
        
    if(session()->get('reason')=="Investment Liquidation"){
      $rep = Auth::user()->user_id;
      $refs = session()->get('ref');
      $userid = getinvestment($refs,'users_id');
      $deposit = walletLoan($userid,$refs,18)-walletLoan($userid,$refs,6);
      $ctime = time();
      $sq=DB::select("SELECT * FROM wallet WHERE reference='$refs' AND type=4 ");
      if(count($sq)==0){
        $type = 4;
        $amt = ($type>10) ? $deposit : '-'.$deposit;
        $wallet = new Wallet();
        $wallet->trno = win_hash(10);
        $wallet->reference = $refs;
        $wallet->user_id = $userid;
        $wallet->amount = $amt;
        $wallet->status = 5;
        $wallet->type = $type;
        $wallet->remark = 'Investment Complete Liquidation';
        $wallet->ctime = time();
        $wallet->mm = date('m',$ctime);
        $wallet->yy = date('y',$ctime);
        $wallet->rep = $rep;
        $wallet->save();
       //$this->walletProcess($userid,$deposit,5,4,$ctime,$refs);//liquidate investment
       //$this->walletProcess($bid,$userid,$interest,5,3,$ctime,$refs);//liquidate investment interest
       //DB::select("UPDATE investment_users SET status=4,rep='$rep',terminate='$ctime' WHERE referrence_code='$refs' ");
       DB::table('investment_users')
            ->where('referrence_code', $refs)
            ->update([
                'status'=>4,
                'rep'=>$rep,
                'terminate'=>$ctime
            ]);
      return redirect('investmentmanaging')->with('success', 'Investment Account successfully Liquidated');
    }else{
      return redirect('investmentmanaging')->with('error', 'Operation Failed');
    }




    }else{
      $rep = Auth::user()->user_id;
      $refs = session()->get('ref');
      //return $refs;
      $start = time();
     
      $stop = $start+60*60*24*getloan($refs,'days');
      $sql = DB::table('loan_users')
          ->where('referrence_code',$refs)
          ->update([
              'status'=>4,
              'start'=>$start,
              'due_date'=>$stop,
              'rep'=>$rep,
          ]);

      $this->addLoanTranch($refs);
      $remark = 'Loan Disbursed';
      $id = getloan($refs,'users_id');
      $amt = getloan($refs,'amount')+getloan($refs,'interest');
      $amount = getloan($refs,'amount');
      $interest = getloan($refs,'amount');
      $profee = getloan($refs,'profee');
      //$this->walletPro2($refs,$bid,$id,10,$amount,$interest,$profee,$remark);
      //$this->walletProcess($bid,$id,$amt,5,10,$start,$refs,$remark); //disburse
      $type = 10;
      $amt = ($type>10) ? $amt : '-'.$amt;
      $ctime = $request->input('ctime') ? strtotime($request->input('ctime')) : time();
      $wallet = new Wallet();
      $wallet->trno = win_hash(10);
      $wallet->reference = $refs;
      $wallet->user_id = $id;
      $wallet->amount = $amt;
      $wallet->status = 5;
      $wallet->type = $type;
      $wallet->remark = 'Loan Disbursement';
      $wallet->ctime = time();
      $wallet->mm = date('m');
      $wallet->yy = date('y');
      $wallet->rep = $rep;
      $wallet->save();
      if($wallet->save()){
       return redirect('loanmanaging')->with('success', 'Transfer Successful');
      }
    }
        
         
       
     }
}



// public function getreadytotransfer(){
//     $
// }












}
