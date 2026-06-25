<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\Classteam;
use app\validation\ClassteamValidator;
use support\validation\annotation\Validate;

class ClassteamController
{
    /**
     * Create
     * @param Request $request
     * @return Response
     */
    #[Validate(validator: ClassteamValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new Classteam();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    /**
     * Update
     * @param Request $request
     * @return Response
     */
    #[Validate(validator: ClassteamValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = Classteam::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    /**
     * Delete
     * @param Request $request
     * @return Response
     */
    #[Validate(validator: ClassteamValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = Classteam::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    /**
     * Detail
     * @param Request $request
     * @return Response
     */
    #[Validate(validator: ClassteamValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = Classteam::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
