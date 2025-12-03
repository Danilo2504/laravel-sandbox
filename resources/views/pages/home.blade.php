@extends('layouts.main')

@section('content')
   <div class="container my-5">
      <div class="row">
         <div class="col-12">
            @session('success')
               <div class="alert alert-success" role="alert">
                  {{ session('success') }}
               </div>
            @endsession
            @session('error')
               <div class="alert alert-danger" role="alert">
                  {{ session('error') }}
               </div>
            @endsession
            <x-form method="POST" operation-type="login" action="{{ route('notification.send') }}">
               <div class="row g-4">
                  <div class="col-12 col-lg-6">
                     <x-form.input name="email" type="email" label="Email" required></x-form.input>
                  </div>
                  <div class="col-12 col-lg-6">
                     <x-form.input name="subject" id="subject" type="text" label="Sujeto" required></x-form.input>
                  </div>
                  <div class="col-12">
                     <x-form.input name="type" id="message_type" type="text" label="Tipo de mensaje" required></x-form.input>
                  </div>
                  <div class="col-12">
                     <x-button type="submit" label="Enviar" format="block"></x-button>
                  </div>
               </div>
            </x-form>
         </div>
      </div>
   </div>
@endsection