<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Validate;

class Manage
{
	public function index($id='')
	{
		$id = intput('id', '');
		$secretKey = uniqid(sha1('gaoxiaoxiang'));
		if ($id != $secretKey) return '你走错了';

		$rounds = D('Round')->order('id desc')->select();
		$this->assign('rounds', $rounds);
		return view();
	}

	public function add()
	{
		if (!request()->isPost()) return json(['code'=>0, 'msg'=>'请求类型错误']);

		$stime = input('post.stime','');
		$etime = input('post.etime', '');
		$types = input('post.types','');
		$open  = input('post.open', 0);


		$round = D('Round');
		$data = $round->order('etime desc')->find();
		if ($stime <= $data.etime) return json(['code'=>0, 'msg'=>'开始时间要大于上次截止时间']);

		$rule = [
			'stime' => 'require|date',
			'etime' => 'require|date',
			'types' => 'require',
		];
		$msg = [
			'stime' => '请选择开始时间',
			'etime' => '请选择结束时间',
			'types' => '请选择类型',
		];

		$validate = new Validate($rule, $msg);
		if(!$validate->check($data)){
			return json(['code'=>0, 'msg'=>$validate->getError())];
		}

		$data = [
			'stime' => $stime,
			'etime' => $etime,
			'types' => $types,
			'open' => $open
		];

		$retval = $round->save($data);
		if (!$retval) {
			return json(['code'=>0, 'msg'=>'操作失败']);
		}

		return json(['code'=>200, 'msg'=>'操作成功']);
	}
}
