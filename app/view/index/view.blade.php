@extends('layout.layout')

@section('title', '首页 - Webman')

@section('content')
    {{-- 欢迎横幅 --}}
    <div class="bg-gradient-to-br from-primary-600 to-primary-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm mb-6">
                    <i class="fa-solid fa-bolt text-3xl"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-4">
                    Hello <span class="text-yellow-300">{{ $name }}</span>
                </h1>
                <p class="text-primary-100 text-lg md:text-xl max-w-2xl mx-auto mb-8">
                    欢迎使用 Webman — 基于 Workerman 的高性能 PHP HTTP 服务框架
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="https://www.workerman.net/doc/webman"
                       target="_blank"
                       class="inline-flex items-center justify-center px-6 py-3 bg-white text-primary-700 font-semibold rounded-lg shadow-lg hover:bg-primary-50 transition-colors duration-200">
                        <i class="fa-solid fa-book mr-2"></i>
                        开始学习
                    </a>
                    <a href="https://github.com/walkor/webman"
                       target="_blank"
                       class="inline-flex items-center justify-center px-6 py-3 bg-white/10 text-white font-semibold rounded-lg border border-white/20 hover:bg-white/20 transition-colors duration-200 backdrop-blur-sm">
                        <i class="fa-brands fa-github mr-2"></i>
                        GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 特性展示 --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800">为什么选择 Webman</h2>
            <p class="text-gray-500 mt-2">高性能 · 高并发 · 易于扩展</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- 卡片 1 --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fa-solid fa-gauge-high text-primary-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">高性能</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    基于 Workerman 事件驱动架构，内存常驻，无需传统 PHP-FPM 的开销。
                </p>
            </div>

            {{-- 卡片 2 --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fa-solid fa-cubes text-primary-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">组件丰富</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    支持 Laravel ORM、Blade 模板、中间件、路由、验证等开箱即用。
                </p>
            </div>

            {{-- 卡片 3 --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                    <i class="fa-solid fa-plug text-primary-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">易于扩展</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    插件化架构，丰富的插件市场，轻松集成第三方服务。
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-gradient-to-br {
            background-image: linear-gradient(to bottom right, var(--tw-gradient-from), var(--tw-gradient-to));
        }
    </style>
@endpush
