<?php

namespace app\controller;

use support\Db;
use support\Request;
use app\model\User;

class IndexController
{
	public function index(Request $request)
	{
		return "webman-index-page.";
	}

	public function view(Request $request)
	{
		return view("index/view", ["name" => "webman"]);
	}

	public function json(Request $request)
	{
		$page = $request->get("page", 1);
		$rows = Db::table("students")->paginate(10, "*", "", $page);
		return json(["code" => 0, "datas" => $rows]);
	}

	public function login(Request $request)
	{
		$username = $request->post("username", "");
		$password = $request->post("password", "");

		if (empty($username) || empty($password)) {
			return json(["code" => 1, "message" => "用户名和密码不能为空"]);
		}

		$user = User::where("username", $username)->first();

		if (!$user || !password_verify($password, $user->password)) {
			return json(["code" => 1, "message" => "用户名或密码错误"]);
		}

		// 生成简单 token（生产环境应使用 JWT）
		$token = bin2hex(random_bytes(32));

		return json([
			"code" => 0,
			"message" => "登录成功",
			"data" => [
				"token" => $token,
				"user" => [
					"id" => $user->id,
					"username" => $user->username,
					"name" => $user->name,
				],
				"isAdmin" => (bool) $user->is_admin,
			],
		]);
	}
}
