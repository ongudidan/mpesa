# mpesa
**Introduction**

This package seeks to help php developers implement the various Mpesa APIs without much hustle. It is based on the REST API whose documentation is available on http://developer.safaricom.co.ke.
 
 **Installation using composer**<br>
 `composer require ongudidan/mpesa`<br>
 
 
 **Configuration**<br>
 At your project root, create a yii2.cfg file and in it set the consumer key and consumer secret as follows   
 `MPESA_CONSUMER_KEY= "consumer key"` <br>
 `MPESA_CONSUMER_SECRET="consumer secret"`<br>
 `MPESA_BUSINESS_SHORTCODE=" enter business shortcode eg  174379"`<br>
 `LIPA_NA_MPESA_PASSKEY="enter lipa na mpesa passkey eg bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919"`<br>

  _Remember to edit the consumer_key and consumer_secret values appropriately when switching between sandbox and live_

  
 **Usage**
 
 **Confirmation and validation urls** 

**B2C Payment Request**
 
 This creates transaction between an M-Pesa short code to a phone number registered on M-Pesa.
 
`$mpesa= new \Mpesa\Mpesa();`

`$b2cTransaction=$mpesa->b2c($InitiatorName, $SecurityCredential, $CommandID, $Amount, $PartyA, $PartyB, $Remarks, $QueueTimeOutURL, $ResultURL, $Occasion);`



**Account Balance Request**
 
This is used to enquire the balance on an M-Pesa BuyGoods (Till Number)

`$mpesa= new \Mpesa\Mpesa();`

`$balanceInquiry=$mpesa->accountBalance($CommandID, $Initiator, $SecurityCredential, $PartyA, $IdentifierType, $Remarks, $QueueTimeOutURL, $ResultURL);`



**Transaction Status Request**
This is used to check the status of transaction. 

`$mpesa= new \Mpesa\Mpesa();`

`$trasactionStatus=$mpesa->transactionStatus($Initiator, $SecurityCredential, $CommandID, $TransactionID, $PartyA, $IdentifierType, $ResultURL, $QueueTimeOutURL, $Remarks, $Occasion);`



**B2B Payment Request**

This is used to transfer funds between two companies.

`$mpesa= new \Mpesa\Mpesa();`

`$b2bTransaction=$mpesa->b2b($ShortCode, $CommandID, $Amount, $Msisdn, $BillRefNumber );`



**C2B Payment Request**

This is used to Simulate transfer of funds between a customer and business.


`$mpesa= new \Mpesa\Mpesa();`

`$b2bTransaction=$mpesa->c2b($ShortCode, $CommandID, $Amount, $Msisdn, $BillRefNumber );`

_Also important to note is that you should have registered validation and confirmation urls where the callback responses will be sent._



**STK Push Simulation**

This is used to initiate online payment on behalf of a customer.

`$mpesa= new \Mpesa\Mpesa();`

`$stkPushSimulation=$mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);`



**STK Push Status Query**

 This is used to check the status of a Lipa Na M-Pesa Online Payment.
 
`$mpesa= new \Mpesa\Mpesa();`

`$STKPushRequestStatus=$mpesa->STKPushQuery($checkoutRequestID, $businessShortCode, $BusinessShortCode, $LipaNaMpesaPasskey);`




**Callback Routes**
M-Pesa APIs are asynchronous. When a valid M-Pesa API request is received by the API Gateway, it is sent to M-Pesa where it is added to a queue. M-Pesa then processes the requests in the queue and sends a response to the API Gateway which then forwards the response to the URL registered in the CallBackURL or ResultURL request parameter. Whenever M-Pesa receives more requests than the queue can handle, M-Pesa responds by rejecting any more requests and the API Gateway sends a queue timeout response to the URL registered in the QueueTimeOutURL request parameter.

**Obtaining post data from callbacks**
 This is used to get post data from callback in json format. The data can be decoded and stored in a database.
 
 `$mpesa= new \Mpesa\Mpesa();`
 
 `$callbackData=$mpesa->getDataFromCallback();`
  
  **Finishing a transaction**
  After obtaining the Post data from the callbacks, use this at the end of your callback routes to complete the transaction
  
  `$mpesa= new \Mpesa\Mpesa();`
  
  `$callbackData=$mpesa->finishTransaction();`


  If validation fails, pass `false` to `finishTransaction()`

  `$mpesa= new \Mpesa\Mpesa();`
  
  `$callbackData=$mpesa->finishTransaction(false);`



