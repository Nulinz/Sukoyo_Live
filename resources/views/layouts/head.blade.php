<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sukoyo</title>

    <!-- Favicon -->
    <link rel="icon" href="" sizes="32*32" type="image/png">

    <!-- Bootstrap CSS with fallback -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          onerror="this.onerror=null;this.href='{{ asset('assets/css/bootstrap.min.css') }}'">

    <!-- Font/Icons CSS with fallbacks -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
          onerror="this.onerror=null;this.href='{{ asset('assets/css/boxicons.min.css') }}'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          onerror="this.onerror=null;this.href='{{ asset('assets/css/font-awesome.min.css') }}'">

    <!-- Google Fonts with fallback -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" 
          rel="stylesheet" onerror="this.onerror=null;this.href='{{ asset('assets/css/source-sans-3.css') }}'">

    <!-- jQuery UI CSS with fallback -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"
          onerror="this.onerror=null;this.href='{{ asset('assets/css/jquery-ui.css') }}'">

    <!-- DataTables CSS with fallback -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css"
          onerror="this.onerror=null;this.href='{{ asset('assets/css/dataTables.bootstrap4.min.css') }}'">

    <!-- Select2 CSS with fallback -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          onerror="this.onerror=null;this.href='{{ asset('assets/css/select2.min.css') }}'">

    <!-- Local Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/aside.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/list.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/form.css') }}">
</head>

<body>

