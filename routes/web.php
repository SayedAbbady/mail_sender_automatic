<?php

use App\Mail\Gmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
    // return view('gmail');
});

/*
Route::get('/gmail', function () {
    return Mail::to('infosayed71@gmail.com')->send(new Gmail()) ;
});
*/
// Send emails
Route::post('/emails/send','EmailController@sendEmailAutomatic');

// get number of emails
Route::post('/emails/getnumber','EmailController@getEmailsNumber');

// Show data 
Route::get('/emails/showData','EmailController@showData');

// Add files to current
Route::post('/emails/addfile','EmailController@addFilesToCurrent');

// Add files from scrach
Route::post('/emails/store','EmailController@uploadScrach');

// Delete all emails
Route::post('/emails/delete','EmailController@deleteEmail');

// Delete one email 
Route::post('/emails/deleteone','EmailController@deleteOne');
    
