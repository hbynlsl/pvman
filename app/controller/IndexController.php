<?php

namespace app\controller;

use support\Db;
use support\Request;

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
}
