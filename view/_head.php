<?php
// _head.php — shared <head> partial
// Requires: $pageTitle (string)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gourmet Hub Admin — <?= $pageTitle ?? 'Dashboard' ?></title>
    <meta name="description" content="Hệ thống quản trị nhà hàng Gourmet Hub — <?= $pageTitle ?? 'Dashboard' ?>.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        /* Gradients */
        .gradient-primary  { background: linear-gradient(135deg, #FF6F00, #E65100); }
        .gradient-blue     { background: linear-gradient(135deg, #1976D2, #0D47A1); }
        .gradient-green    { background: linear-gradient(135deg, #2E7D32, #1B5E20); }
        .gradient-purple   { background: linear-gradient(135deg, #7B1FA2, #4A148C); }
        .gradient-teal     { background: linear-gradient(135deg, #00838F, #006064); }
        .gradient-rose     { background: linear-gradient(135deg, #C62828, #B71C1C); }
        /* Sidebar */
        .sidebar-link { display:flex; align-items:center; gap:12px; padding:10px 16px; border-radius:12px; transition:all .2s; color:#9CA3AF; }
        .sidebar-link:hover { background:rgba(255,255,255,.1); color:#fff; }
        .sidebar-link.active { background:rgba(255,255,255,.15); color:#fff; font-weight:600; }
        /* KPI Cards */
        .kpi-card { border-radius:16px; padding:24px; color:#fff; position:relative; overflow:hidden; }
        .kpi-card::after { content:''; position:absolute; right:-20px; top:-20px; width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,.08); }
        /* Badges */
        .badge { font-size:11px; font-weight:600; padding:2px 8px; border-radius:9999px; display:inline-block; }
        /* Table rows */
        .trow { border-bottom:1px solid #F3F4F6; transition:background .15s; }
        .trow:hover { background:#FFF7ED; }
        /* Scrollbar */
        ::-webkit-scrollbar { width:6px; height:6px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#D1D5DB; border-radius:3px; }
        /* Sidebar toggle for mobile */
        @media (max-width:768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            main { margin-left:0 !important; }
        }
        /* Animate fade in */
        @keyframes fadeUp { from{ opacity:0; transform:translateY(12px); } to{ opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .35s ease both; }
        [x-cloak] { display:none !important; }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    primary: '#FF6F00', 'primary-dark': '#E65100',
                    success: '#2E7D32', warning: '#FBC02D',
                    danger:  '#D32F2F', info: '#1976D2'
                }
            }}
        }
    </script>
</head>
