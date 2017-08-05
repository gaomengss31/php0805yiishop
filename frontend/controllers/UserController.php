<?php
namespace frontend\controllers;

use backend\models\User;
use frontend\models\Address;
use frontend\models\Goods;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\helpers\Json;
use yii\web\Cookie;
use yii\web\Request;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use frontend\models\Cart;

class UserController extends \yii\web\Controller
{
    public $layout = false;
        //关闭csrf验证，跨站请求伪造
    public $enableCsrfValidation = false;

    //注册
    public function actionRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;

        return $this->render('register',['model'=>$model]);
    }

    //ajax提交表单
    public function actionAjaxRegister()
    {
        $model = new Member();
        $model->scenario = Member::SCENARIO_REGISTER;
        if($model->load(\Yii::$app->request->post()) && $model->validate() ){
            //有验证码save的参数为false
            $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                $code2 = \Yii::$app->session->get('code_'.$model->tel);
                if($model->smsCode != $code2){
                    return '注册bu成功';exit;
                }

            $model->save(false);
            return Json::encode(['status'=>true,'msg'=>'注册成功']);
        }else{
            //验证失败，提示错误信息

            return Json::encode(['status'=>false,'msg'=>$model->getErrors()]);
        }
    }

    public function actionIndex()
    {
      //显示视图
        return $this->render('index');
    }

    //登录
    public function actionLogin()
    {
        $model = new Member();
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post(), 'Member');

            if ($model->validate()){
                $admin = Member::findOne(['username' => $model->username]);
                //var_dump($admin);exit;
                if ($admin){
                    if (\Yii::$app->security->validatePassword($model->password, $admin->password_hash)) {
                        \Yii::$app->user->login($admin,$model->rememberMe?7*24*3600:0);
                        //添加最后登录ip和时间
                        $admin->last_login_time = time();
                        $admin->last_login_ip = ip2long(\Yii::$app->request->userIP);
                        $admin->save(false);
                        //判断cookie中的购物车，如果有的话，就保存到购物车表，保存成功删除cookie
                        \Yii::$app->session->setFlash('success', '登录成功');
                        //开始将cookie中的数据，同步到数据表
                        //1.取出cookie中的数据，key=>value的格式
                        $cookies = \Yii::$app->request->cookies;
                        $cart = $cookies->get('cart');//目前$cart 是个对象
                        if($cart == null){
                            $carts = [];
                        }else{
                            $carts = unserialize($cart->value);
                        }
                        foreach (array_keys($carts) as $a){//开始遍历,出来的结果就是商品id
                            //取出goods_id对应的遍历出来的值， 以及取出数据表中，用户的member_id，
                            $b = Cart::find()->where(['goods_id'=>$a])->andWhere(['member_id'=>\Yii::$app->user->id])->one();
                            if($b){//如果有这个商品，就直接加数量
                                $b->amount += $carts[$a];
                                $b->save();//添加后，直接保存。
                            }else{
                                //如果没有就重新添加,这
                                $model = new Cart();
                                $model->amount=$b;
                                $model->goods_id=$a;
                                $model->member_id = \Yii::$app->user->getId();
                                $model->save();
                            }
                            //4.重新保存cookie,将过期时间设置为当前时间搓-1
                            $cookies = \Yii::$app->response->cookies;
                            $d= new Cookie([
                                'name' => 'cart',
                                'value' => serialize($carts),
                                'expire' => time()-1,
                            ]);
                            $cookies->add($d);

                        }

                    //确定cart的key->value格式的值
                        //2.遍历出cookie中的数据

                        //4.重新保存cookie,将过期时间设置为当前时间搓-1.

                        return $this->redirect(['user/address']);
                    }else{
                        echo '密码错误';exit;
                        return $this->redirect(['login']);
                    }
                }else {
                    echo '账号不存在';exit;
                }

            }
            //echo '验证不通过';exit;
            var_dump($model->getErrors());exit;
        }
        return $this->render('login');
    }
    //增加
    public function actionAddress()
    {
        $models = Address::find()->where(['user_id'=>\Yii::$app->user->getId()])->all();
        $model = new Address();
        $request = new Request();
        //开始验证数据
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->user_id = \Yii::$app->user->getId();
                $model->save();
                return $this->redirect('address');
            }
        }
        return $this->render('address',['models'=>$models]);
    }
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
    //删除
    public function actionDelete($id){
        $del = Address::findOne($id)->delete();
        return  $this->redirect('address');

    }

    //修改
    /*public function actionEdit($id){
        $models = Address::findOne(['id'=>$id]);
        var_dump($models);exit;
        $request = new Request();
        //开始验证数据
        if($request->isPost){
            $models->load($request->post());
            if($models->validate()){
                $models->user_id = \Yii::$app->user->getId();
                $models->save();
                return $this->redirect('address');
            }
        }
        return $this->render('address',['models'=>$models]);
    }*/

    //视图中点击短信发送的点击事件，传到这边来，调用这个方法。
    public function actionTestSms()
    {
        $tel =\Yii::$app->request->post('tel');
        $code = rand(1000,9999);
        $res = \Yii::$app->sms->setPhoneNumbers($tel)->setTemplateParam(['code'=>$code])->send();
        //将短信验证码保存redis（session，mysql）
        \Yii::$app->session->set('code_'.$tel,$code);
//
//        }

    }

    //添加到购物车成功页面
    public function actionAddToCart($goods_id,$amount)
    {//传商品id  商品数量

        //未登录
        if (\Yii::$app->user->isGuest) {
            //如果没有登录就存放在cookie中
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if ($cart == null) {
                $carts = [$goods_id => $amount];
            } else {
                $carts = unserialize($cart->value);
                if (isset($carts[$goods_id])) {
                    //购物车中已经有该商品，数量累加
                    $carts[$goods_id] += $amount;
                } else {
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }

            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name' => 'cart',
                'value' => serialize($carts),
                'expire' => 7 * 24 * 3600 + time()
            ]);
            $cookies->add($cookie);
            //var_dump($cookies->get('cart'));
            //return 'ok';
        } else {//用户已登录，操作购物车数据表
                    $model = new Cart();
                    $model->amount=$amount;
                    $model->goods_id=$goods_id;
                    $model->member_id = \Yii::$app->user->getId();
                    $model->save();
                    return $this->redirect(['cart']);

        }
        return $this->redirect(['cart']);
    }

    public function actionCart(){
        $this->layout = false;
        //1 用户未登录，购物车数据从cookie取出

        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //var_dump(unserialize($cookies->getValue('cart')));
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [];
            }else{
                $carts = unserialize($cart->value);
            }
            $models = Goods::find()->where(['in','id',array_keys($carts)])->asArray()->all();

        }else{//用户已登录，购物车数据从数据表取出来
            $msg=Cart::find()->select('goods_id,amount')->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
            //var_dump(($ids));exit;
            $ids = array_column($msg, 'goods_id');//取出$msg中的，goods_id
            $names = array_column($msg, 'amount');//出去$msg中的， amount
            $carts = array_combine($ids, $names);//合并这个数组
            //var_dump($cards);
            //var_dump($ids,$names,$msg);exit;
            $models = Goods::find()->where(['in','id',$ids])->asArray()->all();
        }
        return $this->render('cart',['models'=>$models,'carts'=>$carts]);

    }

    //修改购物车数据
    public function actionAjaxCart()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        //数据验证

        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if($cart==null){
                $carts = [$goods_id=>$amount];
            }else{
                $carts = unserialize($cart->value);//[1=>99，2=》1]
                if(isset($carts[$goods_id])){
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                }else{
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            return 'success';
        }else{
            //登录状态下，改变商品数量，数据库也能跟着改变
        }
    }

    //删除购物车数据
    public function actionCartDelete($goods_id)
    {
        //$goods_id = \Yii::$app->request->post('goods_id');
        //$amount = \Yii::$app->request->post('amount');
        //数据验证
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            $carts = unserialize($cart->value);//[1=>99，2=》1]
            unset($carts[$goods_id]);
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name'=>'cart',
                'value'=>serialize($carts),
                'expire'=>7*24*3600+time()
            ]);
            $cookies->add($cookie);
            return $this->redirect('cart');
        }else{//登录情况下，操作数据表，进行删除
            Cart::findOne(['goods_id' => $goods_id])->delete();
            return $this->redirect(['cart']);
        }

        //订单

    }
    public function actionOrder(){
        //流程:
        //1.结算后， 判断是否登录
        if(\Yii::$app->user->isGuest){
           return $this->redirect('logio');
        }
            //2.由于还有支付方式，还有送货方式，需要在模型里搞一个数组。
        $model = new Order();
        $user_id = \Yii::$app->user->getId();
        //显示地址
        //User::find()->where(['name' => '小伙儿'])->all();   返回 ['name' => '小伙儿'] 的所有数据
        $address = Address::find()->where(['user_id'=>$user_id])->all();
        //显示，购物车
        $msg=Cart::find()->select('goods_id,amount')->where(['member_id'=>\Yii::$app->user->id])->asArray()->all();
        //var_dump(($ids));exit;
        $ids = array_column($msg, 'goods_id');//取出$msg中的，goods_id
        $names = array_column($msg, 'amount');//出去$msg中的， amount
        $carts = array_combine($ids, $names);//合并这个数组
        $models = Goods::find()->where(['in','id',$ids])->asArray()->all();
        return $this->render('order',['model'=>$model,'address'=>$address,'carts'=>$carts,'models'=>$models]);
    }

    public function actionOrder1(){
        /*$transaction = \Yii::$app->db->beginTransaction();//开启事物
        //保存订单
        //（判断库存，如果足够就往下走）保存订单表
        try{

        }catch (){

        }
        //（判断库存，如果足够就往下走）保存订单表
        $transaction->commit();//提交事物
        //（判断库存，如果不够就回滚）保存订单表
        $transaction->rollBack();//回滚*/


        $model =new Order();
        $request = new Request();

        if($request->post()){
            $mod= \Yii::$app->request->post();
            /*if(){//验证库存商品数量是否足够

            }*/
            //地址表相关：
            $address = Address::find()->where(['id'=>$mod['address_id']])->one();
            $model->member_id = \Yii::$app->user->getId();//用户id
            $model->name =$address->username;
            $model->province = $address->province;
            $model->city = $address->city;
            $model->area =$address->area;
            $model->address =$address->address;
            $model->tel =$address->tel;
            //配送方式相关
            $model->delivery_id = $mod['delivery_id'];
            $model->delivery_name = Order::$deliveries[$model->delivery_id]['name'];
            $model->delivery_price = Order::$deliveries[$model->delivery_id]['price'];
            //支付方式相关
            $model->payment_id = $mod['pay_id'];
            $model->payment_name = Order::$payes[$model->payment_id]['name'];
            $model->total=888;
            $model->status=1;
            $model->trade_no=133;
            $model->create_time=time();
            $model->save(false);//这个是验证的问题。
            //开始保存order_goods表
            //查找购物车的数据
            $modelorder = new OrderGoods();
            $carts = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();
            $model = Order::find()->where(['member_id'=>\Yii::$app->user->id])->one();


            //取出购物车里的值
            foreach ($carts as $cart);
            $modelorder->order_id=$model->id;
            $modelorder->goods_id = $cart->goods_id;
            $modelorder->amount = $cart->amount;

            //取出商品表中的值
            $goods =Goods::find()->where(['id'=>$cart->goods_id])->one();
            $modelorder->goods_name = $goods->name;
            $modelorder->logo = $goods->logo;
            $modelorder->price = $goods->shop_price;
            $modelorder->total = 0;
            //保存
            $modelorder->save(false);

            //清空购物车
            $cart->delete(['member_id'=>\Yii::$app->user->id,'goods_id'=>$cart->goods_id]);

        }
        return $this->redirect('goods/index');
    }

}
