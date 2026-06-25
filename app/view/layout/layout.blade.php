<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="">
    <title>@yield('title', 'Webman')</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Font Awesome 图标库 (CDN) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">

    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- ===== 导航栏 ===== --}}
    <header class="bg-white shadow-sm border-b border-gray-200">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- 左侧：Logo + 导航链接 --}}
                <div class="flex items-center space-x-8">
                    <a href="/" class="flex items-center space-x-2 text-primary-600 hover:text-primary-700">
                        <i class="fa-solid fa-bolt text-2xl"></i>
                        <span class="font-bold text-xl tracking-tight">Webman</span>
                    </a>
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="/"
                           class="text-gray-600 hover:text-primary-600 px-3 py-2 text-sm font-medium transition-colors duration-200
                                  {{ request() && request()->path() === '/' ? 'text-primary-600 border-b-2 border-primary-500' : '' }}">
                            <i class="fa-solid fa-house mr-1"></i>首页
                        </a>
                        <a href="/about"
                           class="text-gray-600 hover:text-primary-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                            <i class="fa-solid fa-info-circle mr-1"></i>关于
                        </a>
                        <a href="/contact"
                           class="text-gray-600 hover:text-primary-600 px-3 py-2 text-sm font-medium transition-colors duration-200">
                            <i class="fa-solid fa-envelope mr-1"></i>联系
                        </a>
                    </div>
                </div>

                {{-- 右侧：操作按钮 --}}
                <div class="flex items-center space-x-4">
                    <a href="/login"
                       class="text-gray-600 hover:text-primary-600 text-sm font-medium transition-colors duration-200">
                        登录
                    </a>
                    <a href="/register"
                       class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm">
                        注册
                    </a>

                    {{-- 移动端菜单按钮 --}}
                    <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="打开菜单">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- 移动端下拉菜单 --}}
            <div id="mobile-menu" class="hidden md:hidden pb-3 border-t border-gray-100 pt-2">
                <a href="/"
                   class="block px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50 rounded-md transition-colors duration-200">
                    <i class="fa-solid fa-house mr-1"></i>首页
                </a>
                <a href="/about"
                   class="block px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50 rounded-md transition-colors duration-200">
                    <i class="fa-solid fa-info-circle mr-1"></i>关于
                </a>
                <a href="/contact"
                   class="block px-3 py-2 text-sm text-gray-600 hover:text-primary-600 hover:bg-gray-50 rounded-md transition-colors duration-200">
                    <i class="fa-solid fa-envelope mr-1"></i>联系
                </a>
            </div>
        </nav>
    </header>

    {{-- ===== 主体内容 ===== --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ===== 页脚 ===== --}}
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- 品牌信息 --}}
                <div class="md:col-span-1">
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fa-solid fa-bolt text-primary-600 text-xl"></i>
                        <span class="font-bold text-lg text-gray-800">Webman</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        一个高性能的 PHP HTTP 服务框架，基于 Workerman 构建。
                    </p>
                </div>

                {{-- 快速链接 --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">快速链接</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-500 hover:text-primary-600 text-sm transition-colors duration-200">首页</a></li>
                        <li><a href="/about" class="text-gray-500 hover:text-primary-600 text-sm transition-colors duration-200">关于我们</a></li>
                        <li><a href="/contact" class="text-gray-500 hover:text-primary-600 text-sm transition-colors duration-200">联系我们</a></li>
                    </ul>
                </div>

                {{-- 资源 --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">资源</h3>
                    <ul class="space-y-2">
                        <li><a href="https://www.workerman.net/doc/webman" target="_blank" class="text-gray-500 hover:text-primary-600 text-sm transition-colors duration-200">文档</a></li>
                        <li><a href="https://github.com/walkor/webman" target="_blank" class="text-gray-500 hover:text-primary-600 text-sm transition-colors duration-200">GitHub</a></li>
                        <li><a href="https://wenda.workerman.net/" target="_blank" class="text-gray-500 hover:text-primary-600 text-sm transition-colors duration-200">社区</a></li>
                    </ul>
                </div>

                {{-- 联系信息 --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">联系方式</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center space-x-2 text-gray-500 text-sm">
                            <i class="fa-regular fa-envelope w-4"></i>
                            <span>support@workerman.net</span>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-500 text-sm">
                            <i class="fa-regular fa-globe w-4"></i>
                            <span>www.workerman.net</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- 版权信息 --}}
            <div class="border-t border-gray-200 mt-8 pt-8 flex flex-col sm:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} Webman. All rights reserved.
                </p>
                <div class="flex space-x-4 mt-4 sm:mt-0">
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors duration-200">
                        <i class="fa-brands fa-github text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors duration-200">
                        <i class="fa-brands fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors duration-200">
                        <i class="fa-brands fa-weixin text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- ===== 移动端菜单脚本 ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            if (btn && menu) {
                btn.addEventListener('click', function () {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
