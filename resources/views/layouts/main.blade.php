<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      @include('base.head')
      @stack('style')
   </head>
   <body class="lang-{{app()->getLocale()}} route-{{Route::currentRouteName()}}">
      <main class="page-container">
         @yield('content')
      </main>

   </body>

   @include('base.footer')
   @include('base.scripts')
   @stack('scripts')
</html>