<?php
use Webman\Route;

// api路由
Route::group("/api/v1", function () {
	Route::post("/login", "app\controller\IndexController@login");
});

// 兜底所有页面 → 交给 Vue 路由
Route::fallback(function () {
	return file_get_contents(public_path() . "/index.html");
});
