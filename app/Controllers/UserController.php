<?php

namespace App\Controllers;

use App\Models\Timeback;
use App\Models\Withdrawal;
use App\Models\Wxpay;
use App\Services\Auth;
use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\InviteCode;
use App\Models\CheckInLog;
use App\Models\Ann;
use App\Models\Speedtest;
use App\Models\Shop;
use App\Models\Coupon;
use App\Models\Bought;
use App\Models\Ticket;
use App\Services\Config;
use App\Utils\Hash;
use App\Utils\QRcode;
use App\Utils\Tools;
use App\Utils\Radius;
use App\Utils\Wecenter;
use App\Models\RadiusBan;
use App\Models\DetectLog;
use App\Models\DetectRule;

use voku\helper\AntiXSS;

use App\Models\User;
use App\Models\Code;
use App\Models\Ip;
use App\Models\Paylist;
use App\Models\LoginIp;
use App\Models\BlockIp;
use App\Models\UnblockIp;
use App\Models\Payback;
use App\Models\Relay;
use App\Utils\QQWry;
use App\Utils\GA;
use App\Utils\Geetest;
use App\Utils\Telegram;
use App\Utils\TelegramSessionManager;
use App\Utils\Pay;
use App\Services\Mail;
use Wxpay\lib\WxPayApi\WxPayApi;
use Wxpay\lib\WxPayConfig\WxPayConfig;
use Wxpay\lib\WxPayUnifiedOrder;
use Wxpay\lib\Phpqrcode;


/**
 *  HomeController
 */
class UserController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = Auth::getUser();
    }

//    public function index($request, $response, $args)
//    {
//        /*$Speedtest['Tping']=Speedtest::where("datetime",">",time()-6*3600)->orderBy("telecomping","desc")->get();
//        $Speedtest['Uping']=Speedtest::where("datetime",">",time()-6*3600)->orderBy("unicomping","desc")->take(3);
//        $Speedtest['Cping']=Speedtest::where("datetime",">",time()-6*3600)->orderBy("cmccping","desc")->take(3);
//        $Speedtest['Tspeed']=Speedtest::where("datetime",">",time()-6*3600)->orderBy("telecomeupload","desc")->take(3);
//        $Speedtest['Uspeed']=Speedtest::where("datetime",">",time()-6*3600)->orderBy("unicomupload","desc")->take(3);
//        $Speedtest['Cspeed']=Speedtest::where("datetime",">",time()-6*3600)->orderBy("cmccupload","desc")->take(3);*/
//
//        $nodes = Node::where(
//            function ($query) {
//                $query->where('sort', 0)
//                    ->orwhere('sort', 10);
//            }
//        )->where(
//            function ($query) {
//                $query->where("node_group", "=", $this->user->node_group)
//                    ->orWhere("node_group", "=", 0);
//            }
//        )->where("type", "1")->where("node_class", "<=", $this->user->class)->get();
//        $android_add = "";
//        $android_add_without_mu = "";
//
//        $user = $this->user;
//
//
//        $mu_nodes = Node::where('sort', 9)->where('node_class', '<=', $user->class)->where("type", "1")->where(
//            function ($query) use ($user) {
//                $query->where("node_group", "=", $user->node_group)
//                    ->orWhere("node_group", "=", 0);
//            }
//        )->get();
//
//        $relay_rules = Relay::where('user_id', $this->user->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get();
//
//        if (!Tools::is_protocol_relay($user)) {
//            $relay_rules = array();
//        }
//
//        foreach ($nodes as $node) {
//            $ary['server'] = $node->server;
//            $ary['server_port'] = $user->port;
//            $ary['password'] = $user->passwd;
//            $ary['method'] = $node->method;
//            if ($node->custom_method) {
//                $ary['method'] = $this->user->method;
//            }
//
//            if ($node->mu_only == 0) {
//                if ($node->custom_rss == 1) {
//                    $node_name = $node->name;
//
//                    if ($node->sort == 10) {
//                        $relay_rule = Tools::pick_out_relay_rule($node->id, $user->port, $relay_rules);
//
//                        if ($relay_rule != null) {
//                            if ($relay_rule->dist_node() != null) {
//                                $node_name .= " - " . $relay_rule->dist_node()->name;
//                            }
//                        }
//                    }
//
//                    $ssurl = $ary['server'] . ":" . $ary['server_port'] . ":" . str_replace("_compatible", "", $user->protocol) . ":" . $ary['method'] . ":" . str_replace("_compatible", "", $user->obfs) . ":" . Tools::base64_url_encode($ary['password']) . "/?obfsparam=" . Tools::base64_url_encode($user->obfs_param) . "&remarks=" . Tools::base64_url_encode($node_name) . "&group=" . Tools::base64_url_encode(Config::get('appName'));
//                    $ssqr_s_new = "ssr://" . Tools::base64_url_encode($ssurl);
//                    $android_add .= $ssqr_s_new . " ";
//                    $android_add_without_mu .= $ssqr_s_new . " ";
//                } else {
//                    $ssurl = ($node->custom_method == 1 ? $user->method : $node->method) . ":" . $user->passwd . "@" . $node->server . ":" . $user->port;
//                    $ssqr = "ss://" . base64_encode($ssurl);
//                    $android_add .= $ssqr . " ";
//                    $android_add_without_mu .= $ssqr . " ";
//                }
//            }
//
//
//            if ($node->custom_rss == 1) {
//                foreach ($mu_nodes as $mu_node) {
//                    $mu_user = User::where('port', '=', $mu_node->server)->first();
//
//                    if ($mu_user == null) {
//                        continue;
//                    }
//
//                    if (!($mu_user->class >= $node->node_class && ($node->node_group == 0 || $node->node_group == $mu_user->node_group))) {
//                        continue;
//                    }
//
//                    if ($mu_user->is_multi_user == 1) {
//                        $mu_user->obfs_param = $user->getMuMd5();
//                    }
//
//                    $mu_user->protocol_param = $user->id . ":" . $user->passwd;
//
//                    $node_name = $node->name;
//
//                    if ($node->sort == 10 && $mu_user->is_multi_user != 2) {
//                        $relay_rule = Tools::pick_out_relay_rule($node->id, $mu_user->port, $relay_rules);
//
//                        if ($relay_rule != null) {
//                            if ($relay_rule->dist_node() != null) {
//                                $node_name .= " - " . $relay_rule->dist_node()->name;
//                            }
//                        }
//                    }
//
//
//                    $ary['server_port'] = $mu_user->port;
//                    $ary['password'] = $mu_user->passwd;
//                    $ary['method'] = $node->method;
//                    if ($node->custom_method) {
//                        $ary['method'] = $mu_user->method;
//                    }
//
//                    $ssurl = $ary['server'] . ":" . $ary['server_port'] . ":" . str_replace("_compatible", "", $mu_user->protocol) . ":" . $ary['method'] . ":" . str_replace("_compatible", "", $mu_user->obfs) . ":" . Tools::base64_url_encode($ary['password']) . "/?obfsparam=" . Tools::base64_url_encode($mu_user->obfs_param) . "&protoparam=" . Tools::base64_url_encode($mu_user->protocol_param) . "&remarks=" . Tools::base64_url_encode($node_name . " - " . $mu_node->server . " 端口单端口多用户") . "&group=" . Tools::base64_url_encode(Config::get('appName'));
//                    $ssqr_s_new = "ssr://" . Tools::base64_url_encode($ssurl);
//                    $android_add .= $ssqr_s_new . " ";
//                }
//            }
//        }
//
//        $ios_token = LinkController::GenerateIosCode("smart", 0, $this->user->id, 0, "smart");
//
//        $acl_token = LinkController::GenerateAclCode("smart", 0, $this->user->id, 0, "smart");
//
//        $router_token = LinkController::GenerateRouterCode($this->user->id, 0);
//        $router_token_without_mu = LinkController::GenerateRouterCode($this->user->id, 1);
//
//
//        $uid = time() . rand(1, 10000);
//        if (Config::get('enable_geetest_checkin') == 'true') {
//            $GtSdk = Geetest::get($uid);
//        } else {
//            $GtSdk = null;
//        }
//
//        $Ann = Ann::orderBy('date', 'desc')->first();
//
//
//        return $this->view()->assign("router_token", $router_token)->assign("router_token_without_mu", $router_token_without_mu)->assign("acl_token", $acl_token)->assign('ann', $Ann)->assign('geetest_html', $GtSdk)->assign("ios_token", $ios_token)->assign("android_add", $android_add)->assign("android_add_without_mu", $android_add_without_mu)->assign('enable_duoshuo', Config::get('enable_duoshuo'))->assign('duoshuo_shortname', Config::get('duoshuo_shortname'))->assign('baseUrl', Config::get('baseUrl'))->display('user/index.tpl');
//    }

    public function index($request, $response, $args)
    {
        $Ann = Ann::orderBy('date', 'desc')->first();
        $user = $this->user;
        return $this->view()->assign('user', $user)->assign('ann', $Ann)->display('user/index_new.tpl');
    }

    public function lookingglass($request, $response, $args)
    {
        $Speedtest = Speedtest::where("datetime", ">", time() - Config::get('Speedtest_duration') * 3600)->orderBy('datetime', 'desc')->get();

        return $this->view()->assign('speedtest', $Speedtest)->assign('hour', Config::get('Speedtest_duration'))->display('user/lookingglass.tpl');
    }


    public function code($request, $response, $args)
    {
        $user = $this->user;
        $list = Withdrawal::where('user_id',$user->id)->orderBy('datetime', 'desc')->get();
        foreach($list as $k=>$val){
            $list[$k]['datetime'] = date("Y-m-d H:i:s",$val['datetime']);
        }
        $Ann = Ann::orderBy('date', 'desc')->first();
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $codes = Code::where('type', '<>', '-2')->where('userid', '=', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/code');
        $withdrawal = Withdrawal::where('user_id', '=', $this->user->id)->orderBy('datetime', 'desc')->first();
        return $this->view()->assign('codes', $codes)->assign('ann',$Ann)->assign('list',$list)->assign('withdrawal', $withdrawal)->assign('pmw', Pay::getHTML($this->user))->display('user/code_new.tpl');
    }

    public function withdrawal($request, $response, $args)
    {
        $user = $this->user;
        $ali_id = $request->getParam('ali_id');
        $money = $request->getParam('money');
        if ($ali_id == '') {
            $res['ret'] = 0;
            $res['msg'] = "请填写支付宝账号";
            return $response->getBody()->write(json_encode($res));
        }
        if ($money == '') {
            $res['ret'] = 0;
            $res['msg'] = "请填写提现金额";
            return $response->getBody()->write(json_encode($res));
        } else {
            if ($money < 10) {
                $res['ret'] = 0;
                $res['msg'] = "奖励金积累10元以上才可以提现哦.";
                return $response->getBody()->write(json_encode($res));
            }
            if ($money > $user->money) {
                $res['ret'] = 0;
                $res['msg'] = "你填写的体现金额有误.$money";
                return $response->getBody()->write(json_encode($res));
            }
            if ($money % 10 != 0) {
                $res['ret'] = 0;
                $res['msg'] = "你填写的体现金额必须为十的整数倍哦";
                return $response->getBody()->write(json_encode($res));
            }
            $withdrawal = new Withdrawal();
            $withdrawal->ali_id = $ali_id;
            $withdrawal->datetime = time();
            $withdrawal->money = $money;
            $withdrawal->user_id = $user->id;
            $withdrawal->user_name = $user->user_name;
            $withdrawal->save();
            $user->money = $user->money - $money;
            $user->save();
            $res['ret'] = 1;
            $res['msg'] = "提现成功,请耐心等待2-3个工作日.";
            return $response->getBody()->write(json_encode($res));
        }
    }


    public function donate($request, $response, $args)
    {
        if (Config::get('enable_donate') != 'true') {
            exit(0);
        }

        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $codes = Code::where(
            function ($query) {
                $query->where("type", "=", -1)
                    ->orWhere("type", "=", -2);
            }
        )->where("isused", 1)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/donate');
        return $this->view()->assign('codes', $codes)->assign('total_in', Code::where('isused', 1)->where('type', -1)->sum('number'))->assign('total_out', Code::where('isused', 1)->where('type', -2)->sum('number'))->display('user/donate.tpl');
    }


    public function code_check($request, $response, $args)
    {
        $time = $request->getQueryParams()["time"];
        $codes = Code::where('userid', '=', $this->user->id)->where('usedatetime', '>', date('Y-m-d H:i:s', $time))->first();
        if ($codes != null && strpos($codes->code, "充值") !== false) {
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        } else {
            $res['ret'] = 0;
            return $response->getBody()->write(json_encode($res));
        }
    }


    public function alipay($request, $response, $args)
    {
        $amount = $request->getQueryParams()["amount"];
        Pay::getGen($this->user, $amount);
    }


    public function codepost($request, $response, $args)
    {
        $code = $request->getParam('code');
        $user = $this->user;


        if ($code == "") {
            $res['ret'] = 0;
            $res['msg'] = "请填好充值码";
            return $response->getBody()->write(json_encode($res));
        }

        $codeq = Code::where("code", "=", $code)->where("isused", "=", 0)->first();
        if ($codeq == null) {
            $res['ret'] = 0;
            $res['msg'] = "此充值码错误";
            return $response->getBody()->write(json_encode($res));
        }

        $codeq->isused = 1;
        $codeq->usedatetime = date("Y-m-d H:i:s");
        $codeq->userid = $user->id;
        $codeq->save();

        if ($codeq->type == -1) {
            $user->money = ($user->money + $codeq->number);
            $user->save();

            if ($user->ref_by != "" && $user->ref_by != 0 && $user->ref_by != null) {
                $gift_user = User::where("id", "=", $user->ref_by)->first();
                $gift_user->money = ($gift_user->money + ($codeq->number * (Config::get('code_payback') / 100)));
                $gift_user->save();

                $Payback = new Payback();
                $Payback->total = $codeq->number;
                $Payback->userid = $this->user->id;
                $Payback->ref_by = $this->user->ref_by;
                $Payback->ref_get = $codeq->number * (Config::get('code_payback') / 100);
                $Payback->datetime = time();
                $Payback->save();
            }

            $res['ret'] = 1;
            $res['msg'] = "充值成功，充值的金额为" . $codeq->number . "元。";

            if (Config::get('enable_donate') == 'true') {
                if ($this->user->is_hide == 1) {
                    Telegram::Send("姐姐姐姐，一位不愿透露姓名的大老爷给我们捐了 " . $codeq->number . " 元呢~");
                } else {
                    Telegram::Send("姐姐姐姐，" . $this->user->user_name . " 大老爷给我们捐了 " . $codeq->number . " 元呢~");
                }
            }

            return $response->getBody()->write(json_encode($res));
        }

        if ($codeq->type == 10001) {
            $user->transfer_enable = $user->transfer_enable + $codeq->number * 1024 * 1024 * 1024;
            $user->save();
        }

        if ($codeq->type == 10002) {
            if (time() > strtotime($user->expire_in)) {
                $user->expire_in = date("Y-m-d H:i:s", time() + $codeq->number * 86400);
            } else {
                $user->expire_in = date("Y-m-d H:i:s", strtotime($user->expire_in) + $codeq->number * 86400);
            }
            $user->save();
        }

        if ($codeq->type >= 1 && $codeq->type <= 10000) {
            if ($user->class == 0 || $user->class != $codeq->type) {
                $user->class_expire = date("Y-m-d H:i:s", time());
                $user->save();
            }
            $user->class_expire = date("Y-m-d H:i:s", strtotime($user->class_expire) + $codeq->number * 86400);
            $user->class = $codeq->type;
            $user->save();
        }
    }


    public function GaCheck($request, $response, $args)
    {
        $code = $request->getParam('code');
        $user = $this->user;


        if ($code == "") {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        $ga = new GA();
        $rcode = $ga->verifyCode($user->ga_token, $code);
        if (!$rcode) {
            $res['ret'] = 0;
            $res['msg'] = "测试错误";
            return $response->getBody()->write(json_encode($res));
        }


        $res['ret'] = 1;
        $res['msg'] = "测试成功";
        return $response->getBody()->write(json_encode($res));
    }


    public function GaSet($request, $response, $args)
    {
        $enable = $request->getParam('enable');
        $user = $this->user;


        if ($enable == "") {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        $user->ga_enable = $enable;
        $user->save();


        $res['ret'] = 1;
        $res['msg'] = "设置成功";
        return $response->getBody()->write(json_encode($res));
    }

    public function ResetPort($request, $response, $args)
    {
        $user = $this->user;

        $origin_port = $user->port;

        $user->port = Tools::getAvPort();
        $user->save();

        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }


        $res['ret'] = 1;
        $res['msg'] = "设置成功，新端口是" . $user->port;
        return $response->getBody()->write(json_encode($res));
    }

    public function GaReset($request, $response, $args)
    {
        $user = $this->user;
        $ga = new GA();
        $secret = $ga->createSecret();

        $user->ga_token = $secret;
        $user->save();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/user/edit');
        return $newResponse;
    }


    public function nodeAjax($request, $response, $args)
    {
        $id = $args['id'];
        $point_node = Node::find($id);
        $prefix = explode(" - ", $point_node->name);
        return $this->view()->assign('point_node', $point_node)->assign('prefix', $prefix[0])->assign('id', $id)->display('user/nodeajax.tpl');
    }


    public function node($request, $response, $args)
    {
        $Ann = Ann::orderBy('date', 'desc')->first();
        $user = Auth::getUser();
        $nodes = Node::where(
            function ($query) {
                $query->Where("node_group", "=", $this->user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where('type', 1)->where("node_class", "<=", $this->user->class)->orderBy('name')->get();

        $relay_rules = Relay::where('user_id', $this->user->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get();

        if (!Tools::is_protocol_relay($user)) {
            $relay_rules = array();
        }

        $node_prefix = array();
        $node_method = array();
        $a = 0;
        $node_order = array();
        $node_alive = array();
        $node_prealive = array();
        $node_heartbeat = array();
        $node_bandwidth = array();
        $node_muport = array();

        $ports_count = Node::where(
            function ($query) use ($user) {
                $query->Where("node_group", "=", $user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where('type', 1)->where('sort', 9)->where("node_class", "<=", $user->class)->orderBy('name')->count();

        $ports_count += 1;

        foreach ($nodes as $node) {
            if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0) && (!$node->isNodeTrafficOut())) {
                if ($node->sort == 9) {
                    $mu_user = User::where('port', '=', $node->server)->first();
                    $mu_user->obfs_param = $this->user->getMuMd5();
                    array_push($node_muport, array('server' => $node, 'user' => $mu_user));
                    continue;
                }

                $temp = explode(" - ", $node->name);
                if (!isset($node_prefix[$temp[0]])) {
                    $node_prefix[$temp[0]] = array();
                    $node_order[$temp[0]] = $a;
                    $node_alive[$temp[0]] = 0;

                    if (isset($temp[1])) {
                        $node_method[$temp[0]] = $temp[1];
                    } else {
                        $node_method[$temp[0]] = "";
                    }

                    $a++;
                }


                if ($node->sort == 0 || $node->sort == 7 || $node->sort == 8 || $node->sort == 10) {
                    $node_tempalive = $node->getOnlineUserCount();
                    $node_prealive[$node->id] = $node_tempalive;
                    if ($node->isNodeOnline() !== null) {
                        if ($node->isNodeOnline() === false) {
                            $node_heartbeat[$temp[0]] = "离线";
                        } else {
                            $node_heartbeat[$temp[0]] = "在线";
                        }
                    } else {
                        if (!isset($node_heartbeat[$temp[0]])) {
                            $node_heartbeat[$temp[0]] = "暂无数据";
                        }
                    }

                    if ($node->node_bandwidth_limit == 0) {
                        $node_bandwidth[$temp[0]] = (int)($node->node_bandwidth / 1024 / 1024 / 1024) . " GB / 不限";
                    } else {
                        $node_bandwidth[$temp[0]] = (int)($node->node_bandwidth / 1024 / 1024 / 1024) . " GB / " . (int)($node->node_bandwidth_limit / 1024 / 1024 / 1024) . " GB - " . $node->bandwidthlimit_resetday . " 日重置";
                    }

                    if ($node_tempalive != "暂无数据") {
                        $node_alive[$temp[0]] = $node_alive[$temp[0]] + $node_tempalive;
                    }
                } else {
                    $node_prealive[$node->id] = "暂无数据";
                    if (!isset($node_heartbeat[$temp[0]])) {
                        $node_heartbeat[$temp[0]] = "暂无数据";
                    }
                }

                if (isset($temp[1])) {
                    if (strpos($node_method[$temp[0]], $temp[1]) === false) {
                        $node_method[$temp[0]] = $node_method[$temp[0]] . " " . $temp[1];
                    }
                }


                array_push($node_prefix[$temp[0]], $node);
            }
        }
		$arr = array();
        if(count($node_prefix)>3){
	        for ($i=0;$i<3;$i++){
	            $nonum = array_rand($node_prefix,1);
	            $arr[] = $node_prefix[$nonum];
	            unset($node_prefix[$nonum]);
	        }
	        $arr = (object)$arr;
        }else{
        	$arr = (object)$node_prefix;
        }
        
        $node_order = (object)$node_order;
        $tools = new Tools();
        $ary['server'] = $node->server;
        $ary['server_port'] = $this->user->port;
        $ary['password'] = $this->user->passwd;
        $ary['method'] = $node->method;
        return $this->view()->assign('ann',$Ann)->assign('ary',$ary)->assign('relay_rules', $relay_rules)->assign('tools', $tools)->assign('node_method', $node_method)->assign('node_muport', $node_muport)->assign('node_bandwidth', $node_bandwidth)->assign('node_heartbeat', $node_heartbeat)->assign('node_prefix', $arr)->assign('node_prealive', $node_prealive)->assign('node_order', $node_order)->assign('user', $user)->assign('node_alive', $node_alive)->display('user/node_new.tpl');
    }


    public function nodeInfo($request, $response, $args)
    {
        $user = Auth::getUser();
        $id = $args['id'];
        $mu = $request->getQueryParams()["ismu"];
        $relay_rule_id = $request->getQueryParams()["relay_rule"];
        $node = Node::find($id);

        if ($node == null) {
            return null;
        }


        switch ($node->sort) {

            case 0:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0) && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {
                    $ary['server'] = $node->server;
                    $ary['local_address'] = '127.0.0.1';
                    $ary['local_port'] = 1080;
                    $ary['timeout'] = 300;
                    $ary['workers'] = 1;

                    $is_mu = 0;

                    if ($mu == 0) {
                        $ary['server_port'] = $this->user->port;
                        $ary['password'] = $this->user->passwd;
                        $ary['method'] = $node->method;
                        if ($node->custom_method) {
                            $ary['method'] = $this->user->method;
                        }

                        if ($node->custom_rss) {
                            $ary['obfs'] = str_replace("_compatible", "", $this->user->obfs);
                            $ary['protocol'] = str_replace("_compatible", "", $this->user->protocol);
                        }
                    } else {
                        $mu_user = User::where('port', '=', $mu)->where("is_multi_user", "<>", 0)->first();

                        if ($mu_user == null) {
                            return;
                        }
                        if (!($mu_user->class >= $node->node_class && ($node->node_group == 0 || $node->node_group == $mu_user->node_group))) {
                            return;
                        }
                        $mu_user->obfs_param = $this->user->getMuMd5();
                        $mu_user->protocol_param = $user->id . ":" . $user->passwd;
                        $user = $mu_user;
                        $node->name .= " - " . $mu . " 端口单端口多用户";
                        $ary['server_port'] = $mu_user->port;
                        $ary['password'] = $mu_user->passwd;
                        $ary['method'] = $node->method;

                        if ($node->custom_method) {
                            $ary['method'] = $mu_user->method;
                        }

                        if ($node->custom_rss) {
                            $ary['obfs'] = str_replace("_compatible", "", $mu_user->obfs);
                            if ($user->is_multi_user == 1) {
                                $ary['obfs_param'] = $mu_user->obfs_param;
                                $ary['protocol_param'] = $mu_user->protocol_param;
                            } else {
                                $ary['obfs_param'] = "";
                                $ary['protocol_param'] = $mu_user->protocol_param;
                                $mu_user->obfs_param = "";
                            }
                            $ary['protocol'] = str_replace("_compatible", "", $mu_user->protocol);
                        }

                        $is_mu = 1;
                    }

                    if ($relay_rule_id != 0) {
                        $node->name .= " - " . $relay_server->name;
                    }

                    $json = json_encode($ary);
                    $json_show = json_encode($ary, JSON_PRETTY_PRINT);

                    $ssurl = str_replace("_compatible", "", $user->obfs) . ":" . str_replace("_compatible", "", $user->protocol) . ":" . $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'] . "/" . base64_encode($user->obfs_param);
                    $ssqr_s = "ss://" . base64_encode($ssurl);
                    $ssurl = $ary['server'] . ":" . $ary['server_port'] . ":" . str_replace("_compatible", "", $user->protocol) . ":" . $ary['method'] . ":" . str_replace("_compatible", "", $user->obfs) . ":" . Tools::base64_url_encode($ary['password']) . "/?obfsparam=" . Tools::base64_url_encode($user->obfs_param) . "&protoparam=" . Tools::base64_url_encode($user->protocol_param) . "&remarks=" . Tools::base64_url_encode($node->name) . "&group=" . Tools::base64_url_encode(Config::get('appName'));
                    $ssqr_s_new = "ssr://" . Tools::base64_url_encode($ssurl);
                    $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
                    $ssqr = "ss://" . base64_encode($ssurl);

                    $token_1 = LinkController::GenerateSurgeCode($ary['server'], $ary['server_port'], $this->user->id, 0, $ary['method']);
                    $token_2 = LinkController::GenerateSurgeCode($ary['server'], $ary['server_port'], $this->user->id, 1, $ary['method']);

                    $surge_base = Config::get('baseUrl') . "/downloads/ProxyBase.conf";
                    $surge_proxy = "#!PROXY-OVERRIDE:ProxyBase.conf\n";
                    $surge_proxy .= "[Proxy]\n";
                    $surge_proxy .= "Proxy = custom," . $ary['server'] . "," . $ary['server_port'] . "," . $ary['method'] . "," . $ary['password'] . "," . Config::get('baseUrl') . "/downloads/SSEncrypt.module";
                    return $this->view()->assign('ary', $ary)->assign('mu', $is_mu)->assign('node', $node)->assign('user', $user)->assign('json', $json)->assign('link1', Config::get('baseUrl') . "/link/" . $token_1)->assign('link2', Config::get('baseUrl') . "/link/" . $token_2)->assign('json_show', $json_show)->assign('ssqr', $ssqr)->assign('ssqr_s_new', $ssqr_s_new)->assign('ssqr_s', $ssqr_s)->assign('surge_base', $surge_base)->assign('surge_proxy', $surge_proxy)->assign('info_server', $ary['server'])->assign('info_port', $this->user->port)->assign('info_method', $ary['method'])->assign('info_pass', $this->user->passwd)->display('user/nodeinfo_new.tpl');
                }
                break;

            case 1:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = "VPN 信息<br>地址：" . $node->server . "<br>" . "用户名：" . $email . "<br>密码：" . $this->user->passwd . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfovpn.tpl');
                }
                break;

            case 2:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = "SSH 信息<br>地址：" . $node->server . "<br>" . "用户名：" . $email . "<br>密码：" . $this->user->passwd . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfossh.tpl');
                }

                break;


            case 3:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $exp = explode(":", $node->server);
                    $token = LinkController::GenerateCode(3, $exp[0], $exp[1], 0, $this->user->id);
                    $json_show = "PAC 信息<br>地址：" . Config::get('baseUrl') . "/link/" . $token . "<br>" . "用户名：" . $email . "<br>密码：" . $this->user->passwd . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfopac.tpl');
                }

                break;

            case 4:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = "APN 信息<br>下载地址：" . $node->server . "<br>" . "用户名：" . $email . "<br>密码：" . $this->user->passwd . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfoapn.tpl');
                }

                break;

            case 5:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);

                    $json_show = "Anyconnect 信息<br>地址：" . $node->server . "<br>" . "用户名：" . $email . "<br>密码：" . $this->user->passwd . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfoanyconnect.tpl');
                }


                break;

            case 6:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $exp = explode(":", $node->server);

                    $token_cmcc = LinkController::GenerateApnCode("cmnet", $exp[0], $exp[1], $this->user->id);
                    $token_cnunc = LinkController::GenerateApnCode("3gnet", $exp[0], $exp[1], $this->user->id);
                    $token_ctnet = LinkController::GenerateApnCode("ctnet", $exp[0], $exp[1], $this->user->id);

                    $json_show = "APN 文件<br>移动地址：" . Config::get('baseUrl') . "/link/" . $token_cmcc . "<br>联通地址：" . Config::get('baseUrl') . "/link/" . $token_cnunc . "<br>电信地址：" . Config::get('baseUrl') . "/link/" . $token_ctnet . "<br>" . "用户名：" . $email . "<br>密码：" . $this->user->passwd . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfoapndownload.tpl');
                }


                break;

            case 7:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $token = LinkController::GenerateCode(7, $node->server, ($this->user->port - 20000), 0, $this->user->id);
                    $json_show = "PAC Plus 信息<br>PAC 地址：" . Config::get('baseUrl') . "/link/" . $token . "<br>支持方式：" . $node->method . "<br>备注：" . $node->info;


                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfopacplus.tpl');
                }


                break;

            case 8:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0)) {
                    $email = $this->user->email;
                    $email = Radius::GetUserName($email);
                    $token = LinkController::GenerateCode(8, $node->server, ($this->user->port - 20000), 0, $this->user->id);
                    $token_ios = LinkController::GenerateCode(8, $node->server, ($this->user->port - 20000), 1, $this->user->id);
                    $json_show = "PAC Plus Plus信息<br>PAC 一般地址：" . Config::get('baseUrl') . "/link/" . $token . "<br>PAC iOS 地址：" . Config::get('baseUrl') . "/link/" . $token_ios . "<br>" . "备注：" . $node->info;

                    return $this->view()->assign('json_show', $json_show)->display('user/nodeinfopacpp.tpl');
                }


                break;


            case 10:
                if ($user->class >= $node->node_class && ($user->node_group == $node->node_group || $node->node_group == 0) && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {
                    $relay_rule = Relay::where('id', $relay_rule_id)->where(
                        function ($query) use ($user) {
                            $query->Where("user_id", "=", $user->id)
                                ->orWhere("user_id", "=", 0);
                        }
                    )->first();

                    if ($relay_rule != null) {
                        $node->name .= " - " . $relay_rule->dist_node()->name;
                    }

                    $ary['server'] = $node->server;
                    $ary['local_address'] = '127.0.0.1';
                    $ary['local_port'] = 1080;
                    $ary['timeout'] = 300;
                    $ary['workers'] = 1;

                    $is_mu = 0;

                    if ($mu == 0) {
                        $ary['server_port'] = $this->user->port;
                        $ary['password'] = $this->user->passwd;
                        $ary['method'] = $node->method;
                        if ($node->custom_method) {
                            $ary['method'] = $this->user->method;
                        }

                        if ($node->custom_rss) {
                            $ary['obfs'] = str_replace("_compatible", "", $this->user->obfs);
                            $ary['protocol'] = str_replace("_compatible", "", $this->user->protocol);
                        }
                    } else {
                        $mu_user = User::where('port', '=', $mu)->where("is_multi_user", "<>", 0)->first();

                        if ($mu_user == null) {
                            return;
                        }

                        if (!($mu_user->class >= $node->node_class && ($node->node_group == 0 || $node->node_group == $mu_user->node_group))) {
                            return;
                        }
                        $mu_user->obfs_param = $this->user->getMuMd5();
                        $mu_user->protocol_param = $user->id . ":" . $user->passwd;
                        $user = $mu_user;
                        $node->name .= " - " . $mu . " 端口单端口多用户";
                        $ary['server_port'] = $mu_user->port;
                        $ary['password'] = $mu_user->passwd;
                        $ary['method'] = $node->method;

                        if ($node->custom_method) {
                            $ary['method'] = $mu_user->method;
                        }

                        if ($node->custom_rss) {
                            $ary['obfs'] = str_replace("_compatible", "", $mu_user->obfs);
                            if ($user->is_multi_user == 1) {
                                $ary['obfs_param'] = $mu_user->obfs_param;
                                $ary['protocol_param'] = $mu_user->protocol_param;
                            } else {
                                $ary['obfs_param'] = "";
                                $ary['protocol_param'] = $mu_user->protocol_param;
                                $mu_user->obfs_param = "";
                            }
                            $ary['protocol'] = str_replace("_compatible", "", $mu_user->protocol);
                        }

                        $is_mu = 1;
                    }

                    $json = json_encode($ary);
                    $json_show = json_encode($ary, JSON_PRETTY_PRINT);

                    $ssurl = str_replace("_compatible", "", $user->obfs) . ":" . str_replace("_compatible", "", $user->protocol) . ":" . $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'] . "/" . base64_encode($user->obfs_param);
                    $ssqr_s = "ss://" . base64_encode($ssurl);
                    $ssurl = $ary['server'] . ":" . $ary['server_port'] . ":" . str_replace("_compatible", "", $user->protocol) . ":" . $ary['method'] . ":" . str_replace("_compatible", "", $user->obfs) . ":" . Tools::base64_url_encode($ary['password']) . "/?obfsparam=" . Tools::base64_url_encode($user->obfs_param) . "&protoparam=" . Tools::base64_url_encode($user->protocol_param) . "&remarks=" . Tools::base64_url_encode($node->name) . "&group=" . Tools::base64_url_encode(Config::get('appName'));
                    $ssqr_s_new = "ssr://" . Tools::base64_url_encode($ssurl);
                    $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
                    $ssqr = "ss://" . base64_encode($ssurl);

                    $token_1 = LinkController::GenerateSurgeCode($ary['server'], $ary['server_port'], $this->user->id, 0, $ary['method']);
                    $token_2 = LinkController::GenerateSurgeCode($ary['server'], $ary['server_port'], $this->user->id, 1, $ary['method']);

                    $surge_base = Config::get('baseUrl') . "/downloads/ProxyBase.conf";
                    $surge_proxy = "#!PROXY-OVERRIDE:ProxyBase.conf\n";
                    $surge_proxy .= "[Proxy]\n";
                    $surge_proxy .= "Proxy = custom," . $ary['server'] . "," . $ary['server_port'] . "," . $ary['method'] . "," . $ary['password'] . "," . Config::get('baseUrl') . "/downloads/SSEncrypt.module";
                    return $this->view()->assign('ary', $ary)->assign('mu', $is_mu)->assign('node', $node)->assign('user', $user)->assign('json', $json)->assign('link1', Config::get('baseUrl') . "/link/" . $token_1)->assign('link2', Config::get('baseUrl') . "/link/" . $token_2)->assign('json_show', $json_show)->assign('ssqr', $ssqr)->assign('ssqr_s_new', $ssqr_s_new)->assign('ssqr_s', $ssqr_s)->assign('surge_base', $surge_base)->assign('surge_proxy', $surge_proxy)->assign('info_server', $ary['server'])->assign('info_port', $this->user->port)->assign('info_method', $ary['method'])->assign('info_pass', $this->user->passwd)->display('user/nodeinfo_new.tpl');
                }
                break;


            default:
                echo "微笑";

        }
    }

    public function GetPcConf($request, $response, $args)
    {
        $without_mu = $request->getQueryParams()["without_mu"];

        $newResponse = $response->withHeader('Content-type', ' application/octet-stream')->withHeader('Content-Disposition', ' attachment; filename=gui-config.json');//->getBody()->write($builder->output());
        $newResponse->getBody()->write(LinkController::GetPcConf(Node::where(
            function ($query) {
                $query->where('sort', 0)
                    ->orWhere('sort', 10);
            }
        )->where("type", "1")->where(
            function ($query) {
                $query->where("node_group", "=", $this->user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where("node_class", "<=", $this->user->class)->get(), $this->user, $without_mu));
        return $newResponse;
    }

    public function GetIosConf($request, $response, $args)
    {
        $newResponse = $response->withHeader('Content-type', ' application/octet-stream')->withHeader('Content-Disposition', ' attachment; filename=allinone.conf');//->getBody()->write($builder->output());
        $newResponse->getBody()->write(LinkController::GetIosConf(Node::where('sort', 0)->where("type", "1")->where(
            function ($query) {
                $query->where("node_group", "=", $this->user->node_group)
                    ->orWhere("node_group", "=", 0);
            }
        )->where("node_class", "<=", $this->user->class)->get(), $this->user));
        return $newResponse;
    }


    public function profile($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $paybacks = Payback::where("ref_by", $this->user->id)->orderBy("datetime", "desc")->paginate(15, ['*'], 'page', $pageNum);
        $paybacks->setPath('/user/profile');
        $timebacks = Timeback::where("ref_by", $this->user->id)->orderBy("datetime", "desc")->paginate(15, ['*'], 'page', $pageNum);
        $timebacks->setPath('/user/profile');

        $iplocation = new QQWry();

        $totallogin = LoginIp::where('userid', '=', $this->user->id)->where("type", "=", 0)->orderBy("datetime", "desc")->take(10)->get();

        $userloginip = array();

        foreach ($totallogin as $single) {
            //if(isset($useripcount[$single->userid]))
            {
                if (!isset($userloginip[$single->ip])) {
                    //$useripcount[$single->userid]=$useripcount[$single->userid]+1;
                    $location = $iplocation->getlocation($single->ip);
                    $userloginip[$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
                }
            }
        }


        return $this->view()->assign("userloginip", $userloginip)->assign("paybacks", $paybacks)->assign("timebacks", $timebacks)->display('user/profile.tpl');
    }


    public function announcement($request, $response, $args)
    {
        $Anns = Ann::orderBy('date', 'desc')->get();


        return $this->view()->assign("anns", $Anns)->display('user/announcement.tpl');
    }


    public function edit($request, $response, $args)
    {
        $themes = Tools::getDir(BASE_PATH . "/resources/views");
        $Ann = Ann::orderBy('date', 'desc')->first();
        $BIP = BlockIp::where("ip", $_SERVER["REMOTE_ADDR"])->first();
        if ($BIP == null) {
            $Block = "IP: " . $_SERVER["REMOTE_ADDR"] . " 没有被封";
            $isBlock = 0;
        } else {
            $Block = "IP: " . $_SERVER["REMOTE_ADDR"] . " 已被封";
            $isBlock = 1;
        }

        $bind_token = TelegramSessionManager::add_bind_session($this->user);

        $config_service = new Config();

        return $this->view()->assign('user', $this->user)->assign('ann',$Ann)->assign('themes', $themes)->assign('isBlock', $isBlock)->assign('Block', $Block)->assign('bind_token', $bind_token)->assign('telegram_bot', Config::get('telegram_bot'))->assign('config_service', $config_service)->display('user/edit_new.tpl');
    }

    public function edit_name($request, $response, $args)
    {
        $name = $request->getParam('user_name');
        $id = $request->getParam('id');
        $user = User::where("id", $id)->first();
        $user->user_name = $name;
        $is_save = $user->save();
        if ($is_save) {
            $res['ret'] = 1;
            $res['msg'] = "修改成功";
        } else {
            $res['ret'] = 0;
            $res['msg'] = "修改失败";
        }
        return $response->getBody()->write(json_encode($res));
    }


    public function invite($request, $response, $args)
    {
        $Ann = Ann::orderBy('date', 'desc')->first();
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $user = $this->user;
        $codes = InviteCode::where('user_id', $this->user->id)->orderBy("created_at", "desc")->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/user/invite');
        $paybacks = Payback::where("ref_by", $this->user->id)->orderBy("datetime", "desc")->paginate(15, ['*'], 'page', $pageNum);
        $paybacks->setPath('/user/invite');
        $timebacks = Timeback::where("ref_by", $this->user->id)->orderBy("datetime", "desc")->paginate(15, ['*'], 'page', $pageNum);
        foreach($paybacks as $k=>$val){
            foreach($timebacks as $kk=>$value){
                if($val['user_id'] == $value['user_id']){
                    $paybacks[$k]['time'] = $value['ref_get'];
                    continue;
                }
            }
        }
        $withdrawal = Withdrawal::where('user_id', '=', $this->user->id)->orderBy('datetime', 'desc')->first();
        $list = Withdrawal::where('user_id',$user->id)->orderBy('datetime', 'desc')->get();
        return $this->view()->assign('codes', $codes)->assign('withdrawal',$withdrawal)->assign('list',$list)->assign('ann',$Ann)->assign('paybacks',$paybacks)->assign('timebacks',$timebacks)->display('user/invite_new.tpl');
    }

    public function doInvite($request, $response, $args)
    {
        $n = $this->user->invite_num;
        if ($n < 1) {
            $res['ret'] = 0;
            $res['msg'] = "失败";
            return $response->getBody()->write(json_encode($res));
        }
        for ($i = 0; $i < $n; $i++) {
            $char = Tools::genRandomChar(32);
            $code = new InviteCode();
            $code->code = $char;
            $code->user_id = $this->user->id;
            $code->save();
        }
        $this->user->invite_num = 0;
        $this->user->save();
        $res['ret'] = 1;
        $res['msg'] = "生成成功。";
        return $this->echoJson($response, $res);
    }

    public function sys()
    {
        return $this->view()->assign('ana', "")->display('user/sys.tpl');
    }

    public function updatePassword($request, $response, $args)
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $user = $this->user;
        if (!Hash::checkPassword($user->pass, $oldpwd)) {
            $res['ret'] = 0;
            $res['msg'] = "旧密码错误";
            return $response->getBody()->write(json_encode($res));
        }
        if ($pwd != $repwd) {
            $res['ret'] = 0;
            $res['msg'] = "两次输入不符合";
            return $response->getBody()->write(json_encode($res));
        }

        if (strlen($pwd) < 8) {
            $res['ret'] = 0;
            $res['msg'] = "密码太短啦";
            return $response->getBody()->write(json_encode($res));
        }
        $hashPwd = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        $user->clean_link();

        $res['ret'] = 1;
        $res['msg'] = "修改成功";
        return $this->echoJson($response, $res);
    }

    public function updateHide($request, $response, $args)
    {
        $hide = $request->getParam('hide');
        $user = $this->user;
        $user->is_hide = $hide;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "修改成功";
        return $this->echoJson($response, $res);
    }

    public function Unblock($request, $response, $args)
    {
        $user = $this->user;
        $BIP = BlockIp::where("ip", $_SERVER["REMOTE_ADDR"])->get();
        foreach ($BIP as $bi) {
            $bi->delete();
        }

        $UIP = new UnblockIp();
        $UIP->userid = $user->id;
        $UIP->ip = $_SERVER["REMOTE_ADDR"];
        $UIP->datetime = time();
        $UIP->save();


        $res['ret'] = 1;
        $res['msg'] = "发送解封命令解封 " . $_SERVER["REMOTE_ADDR"] . " 成功";
        return $this->echoJson($response, $res);
    }

    public function shop($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $shops = Shop::where("status", 1)->paginate(15, ['*'], 'page', $pageNum);
        $shops->setPath('/user/shop');

        return $this->view()->assign('shops', $shops)->display('user/shop.tpl');
    }

    public function CouponCheck($request, $response, $args)
    {
        $user = $this->user;
        if (empty($user)) {
            $res['ret'] = 3;
            $res['msg'] = "请登录之后再进行购买!";
            return $response->getBody()->write(json_encode($res));
        }
        $coupon = $request->getParam('coupon');
        $shop = $request->getParam('shop');

        $shop = Shop::where("id", $shop)->where("status", 1)->first();

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = "非法请求";
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon == "") {
            $res['ret'] = 1;
            $res['name'] = $shop->name;
            $res['credit'] = "0 %";
            $res['total'] = $shop->price . "元";
            return $response->getBody()->write(json_encode($res));
        }

        $coupon = Coupon::where("code", $coupon)->first();

        if ($coupon == null) {
            $res['ret'] = 0;
            $res['msg'] = "优惠码无效";
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon->order($shop->id) == false) {
            $res['ret'] = 0;
            $res['msg'] = "此优惠码不可用于此商品";
            return $response->getBody()->write(json_encode($res));
        }

        $res['ret'] = 1;
        $res['name'] = $shop->name;
        $res['credit'] = $coupon->credit . " %";
        $res['total'] = $shop->price * ((100 - $coupon->credit) / 100) . "元";

        return $response->getBody()->write(json_encode($res));
    }

    /**
     *
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param UnifiedOrderInput $input
     */
    public function GetPayUrl($input)
    {
        if ($input->GetTrade_type() == "NATIVE") {
            $result = WxPayApi::unifiedOrder($input);
            return $result;
        }
    }

//    /**
//     * 流程：
//     * 1、调用统一下单，取得code_url，生成二维码
//     * 2、用户扫描二维码，进行支付
//     * 3、支付完成之后，微信服务器会通知支付成功
//     * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
//     */
    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function wx_pay($request, $response, $args)
    {
        $is_deductible = $request->getParam('is_deductible');
        $shop = $request->getParam('shop');
        $autorenew = $request->getParam('autorenew');
        $shop = Shop::where("id", $shop)->where("status", 1)->first();
        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = "非法请求";
            return $response->getBody()->write(json_encode($res));
        }
        $user = $this->user;
//        选中使用奖励金进行抵扣
        if ($is_deductible == 1) {
            $money = $user->money;
            if ($money > $shop->price) {
                $user->money = $user->money - $shop->price;
                $user->save();
                $bought = new Bought();
                $bought->userid = $user->id;
                $bought->shopid = $shop->id;
                $bought->datetime = time();
                if ($autorenew == 0 || $shop->auto_renew == 0) {
                    $bought->renew = 0;
                } else {
                    $bought->renew = time() + $shop->auto_renew * 86400;
                }
                $price = $shop->price;
                $bought->price = $price;
                $bought->save();
                $shop->buy($user);
                $res['ret'] = 1;
                $res['msg'] = "购买成功";
                return $response->getBody()->write(json_encode($res));
            } else {
                $price = ($shop->price - $money) * 100;
            }
        } else {
            $price = ($shop->price) * 100;
        }
        $name = $shop->name;
        $input = new WxPayUnifiedOrder();
        $input->SetBody($name);
        $buy_num = date("YmdHis") . rand(10, 100);
        $input->SetOut_trade_no($buy_num);
        $input->SetTotal_fee("10");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url("http://easytest.ngrok.cc/api/wxpay_callback");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("1234567890");
        $result = $this->GetPayUrl($input);
        $wxpay = new Wxpay();
        $wxpay->userid = $user->id;
        $wxpay->shopid = $shop->id;
        $wxpay->ref_by = $user->ref_by;
        $wxpay->buy_num = $buy_num;
        $wxpay->datetime = time();
        if ($autorenew == 0 || $shop->auto_renew == 0) {
            $wxpay->renew = 0;
        } else {
            $wxpay->renew = time() + $shop->auto_renew * 86400;
        }
        if (isset($onetime)) {
            $price = $shop->price;
        }
        $wxpay->price = $price;
        $wxpay->save();
        $res['ret'] = 4;
        $res['msg'] = $result['code_url'];
        $res['num'] = $buy_num;
        return $response->getBody()->write(json_encode($res));
    }

    public function look_result($request, $response, $args)
    {
        $buy_num = $request->getParam('buy_num');
        $shop = $request->getParam('shop');
        $wxpay = Wxpay::where('buy_num', '=', $buy_num)->first();
        if ($wxpay['is_buy'] == 2) {
            if ($wxpay['is_confirm'] == 1) {
                $wxpay->is_confirm = 2;
                $wxpay->save();
                $bought = new Bought();
                $bought->userid = $wxpay['userid'];
                $bought->shopid = $wxpay['shopid'];
                $bought->datetime = $wxpay['datetime'];
                $bought->renew = $wxpay['renew'];
                $bought->coupon = $wxpay['coupon'];
                $bought->price = $wxpay['price'] / 100;
                $bought->type = "Wxpay";
                $bought->save();
                $user = $this->user;
                $shop = Shop::where("id", $shop)->where("status", 1)->first();
                $shop->buy($user);
            }
            $res['ret'] = 5;
            $res['msg'] = "购买成功!";
        } else {
            $res['ret'] = 1;
        }
        return $response->getBody()->write(json_encode($res));
    }

    private function NumToStr($num)
    {
        if (stripos($num, 'e') === false) return $num;
        $num = trim(preg_replace('/[=\'"]/', '', $num, 1), '"');//出现科学计数法，还原成字符串
        $result = "";
        while ($num > 0) {
            $v = $num - floor($num / 10) * 10;
            $num = floor($num / 10);
            $result = $v . $result;
        }
        return $result;
    }
//    public function qr_code($request, $response, $args)
//    {
//        $url = urldecode($request->getParam('data'));
//        Phpqrcode\QRcode::png(urldecode($url));
//    }

    public function buy($request, $response, $args)
    {
        $coupon = $request->getParam('coupon');
        $code = $coupon;
        $shop = $request->getParam('shop');

        $autorenew = $request->getParam('autorenew');

        $shop = Shop::where("id", $shop)->where("status", 1)->first();

        if ($shop == null) {
            $res['ret'] = 0;
            $res['msg'] = "非法请求";
            return $response->getBody()->write(json_encode($res));
        }

        if ($coupon == "") {
            $credit = 0;
        } else {
            $coupon = Coupon::where("code", $coupon)->first();

            if ($coupon == null) {
                $credit = 0;
            } else {
                if ($coupon->onetime == 1) {
                    $onetime = true;
                }

                $credit = $coupon->credit;
            }

            if ($coupon->order($shop->id) == false) {
                $res['ret'] = 0;
                $res['msg'] = "此优惠码不可用于此商品";
                return $response->getBody()->write(json_encode($res));
            }

            if ($coupon->expire < time()) {
                $res['ret'] = 0;
                $res['msg'] = "此优惠码已过期";
                return $response->getBody()->write(json_encode($res));
            }
        }

        $price = $shop->price * ((100 - $credit) / 100);
        $user = $this->user;
        if ($user->money < $price) {
            $res['ret'] = 0;
            $res['msg'] = "余额不足";
            return $response->getBody()->write(json_encode($res));
        }

        $user->money = $user->money - $price;
        $user->save();

        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        if ($autorenew == 0 || $shop->auto_renew == 0) {
            $bought->renew = 0;
        } else {
            $bought->renew = time() + $shop->auto_renew * 86400;
        }

        $bought->coupon = $code;


        if (isset($onetime)) {
            $price = $shop->price;
        }
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);

        $res['ret'] = 1;
        $res['msg'] = "购买成功";

        return $response->getBody()->write(json_encode($res));
    }

    public function bought($request, $response, $args)
    {
        $Ann = Ann::orderBy('date', 'desc')->first();
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $shops = Bought::where("userid", $this->user->id)->orderBy("id", "desc")->paginate(15, ['*'], 'page', $pageNum);
        $shops->setPath('/user/bought');

        return $this->view()->assign('shops', $shops)->assign('ann',$Ann)->display('user/bought_new.tpl');
    }

    public function deleteBoughtGet($request, $response, $args)
    {
        $id = $request->getParam('id');
        $shop = Bought::where("id", $id)->where("userid", $this->user->id)->first();

        if ($shop == null) {
            $rs['ret'] = 0;
            $rs['msg'] = "退订失败，订单不存在。";
            return $response->getBody()->write(json_encode($rs));
        }

        if ($this->user->id == $shop->userid) {
            $shop->renew = 0;
        }

        if (!$shop->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "退订失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "退订成功";
        return $response->getBody()->write(json_encode($rs));
    }


    public function ticket($request, $response, $args)
    {
        $Ann = Ann::orderBy('date', 'desc')->first();
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $tickets = Ticket::where("userid", $this->user->id)->where("rootid", 0)->orderBy("datetime", "desc")->paginate(15, ['*'], 'page', $pageNum);
        $tickets->setPath('/user/ticket');

        return $this->view()->assign('tickets', $tickets)->assign('ann',$Ann)->display('user/ticket_new.tpl');
    }

    public function ticket_create($request, $response, $args)
    {
        return $this->view()->display('user/ticket_create.tpl');
    }

    public function ticket_add($request, $response, $args)
    {
        $title = $request->getParam('title');
        $content = $request->getParam('content');
        $type = $request->getParam('type');
        $phone = $request->getParam('phone');
        $contact = $request->getParam('contact');


        if ($title == "" || $content == "") {
            $res['ret'] = 0;
            $res['msg'] = "请填全";
            return $this->echoJson($response, $res);
        }
        if ($contact == "" || $phone == "") {
            $res['ret'] = 0;
            $res['msg'] = "请填写联系方式";
            return $this->echoJson($response, $res);
        }

        if (strpos($content, "admin") != false || strpos($content, "user") != false) {
            $res['ret'] = 0;
            $res['msg'] = "请求中有不正当的词语。";
            return $this->echoJson($response, $res);
        }


        $ticket = new Ticket();

        $antiXss = new AntiXSS();

        $ticket->title = $antiXss->xss_clean($title);
        $ticket->contact = $antiXss->xss_clean($contact);
        $ticket->type = $antiXss->xss_clean($type);
        $ticket->phone = $antiXss->xss_clean($phone);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = 0;
        $ticket->userid = $this->user->id;
        $ticket->datetime = time();
        $ticket->save();

        $adminUser = User::where("is_admin", "=", "1")->get();
        foreach ($adminUser as $user) {
            $subject = Config::get('appName') . "-新工单被开启";
            $to = $user->email;
            $text = "管理员您好，有人开启了新的工单，请您及时处理。。";
            try {
                Mail::send($to, $subject, 'news/warn.tpl', [
                    "user" => $user, "text" => $text
                ], [
                ]);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        $res['ret'] = 1;
        $res['msg'] = "提交成功";
        return $this->echoJson($response, $res);
    }

    public function ticket_update($request, $response, $args)
    {
        $id = $args['id'];
        $content = $request->getParam('content');
        $status = $request->getParam('status');

        if ($content == "" || $status == "") {
            $res['ret'] = 0;
            $res['msg'] = "请填全";
            return $this->echoJson($response, $res);
        }

        if (strpos($content, "admin") != false || strpos($content, "user") != false) {
            $res['ret'] = 0;
            $res['msg'] = "请求中有不正当的词语。";
            return $this->echoJson($response, $res);
        }


        $ticket_main = Ticket::where("id", "=", $id)->where("rootid", "=", 0)->first();
        if ($ticket_main->userid != $this->user->id) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user/ticket');
            return $newResponse;
        }

        if ($status == 1 && $ticket_main->status != $status) {
            $adminUser = User::where("is_admin", "=", "1")->get();
            foreach ($adminUser as $user) {
                $subject = Config::get('appName') . "-工单被重新开启";
                $to = $user->email;
                $text = "管理员您好，有人重新开启了<a href=\"" . Config::get('baseUrl') . "/admin/ticket/" . $ticket_main->id . "/view\">工单</a>，请您及时处理。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        } else {
            $adminUser = User::where("is_admin", "=", "1")->get();
            foreach ($adminUser as $user) {
                $subject = Config::get('appName') . "-工单被回复";
                $to = $user->email;
                $text = "管理员您好，有人回复了<a href=\"" . Config::get('baseUrl') . "/admin/ticket/" . $ticket_main->id . "/view\">工单</a>，请您及时处理。";
                try {
                    Mail::send($to, $subject, 'news/warn.tpl', [
                        "user" => $user, "text" => $text
                    ], [
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        $antiXss = new AntiXSS();

        $ticket = new Ticket();
        $ticket->title = $antiXss->xss_clean($ticket_main->title);
        $ticket->content = $antiXss->xss_clean($content);
        $ticket->rootid = $ticket_main->id;
        $ticket->userid = $this->user->id;
        $ticket->datetime = time();
        $ticket_main->status = $status;

        $ticket_main->save();
        $ticket->save();


        $res['ret'] = 1;
        $res['msg'] = "提交成功";
        return $this->echoJson($response, $res);
    }

    public function ticket_view($request, $response, $args)
    {
        $id = $args['id'];
        $ticket_main = Ticket::where("id", "=", $id)->where("rootid", "=", 0)->first();
        if ($ticket_main->userid != $this->user->id) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user/ticket');
            return $newResponse;
        }

        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }


        $ticketset = Ticket::where("id", $id)->orWhere("rootid", "=", $id)->orderBy("datetime", "desc")->paginate(5, ['*'], 'page', $pageNum);
        $ticketset->setPath('/user/ticket/' . $id . "/view");


        return $this->view()->assign('ticketset', $ticketset)->assign("id", $id)->display('user/ticket_view.tpl');
    }


    public function updateWechat($request, $response, $args)
    {
        $type = $request->getParam('imtype');
        $wechat = $request->getParam('wechat');

        $user = $this->user;

        if ($user->telegram_id != 0) {
            $res['ret'] = 0;
            $res['msg'] = "您绑定了 Telegram ，所以此项并不能被修改。";
            return $response->getBody()->write(json_encode($res));
        }

        if ($wechat == "" || $type == "") {
            $res['ret'] = 0;
            $res['msg'] = "请填好";
            return $response->getBody()->write(json_encode($res));
        }

        $user1 = User::where('im_value', $wechat)->where('im_type', $type)->first();
        if ($user1 != null) {
            $res['ret'] = 0;
            $res['msg'] = "此联络方式已经被注册了";
            return $response->getBody()->write(json_encode($res));
        }

        $user->im_type = $type;
        $antiXss = new AntiXSS();
        $user->im_value = $antiXss->xss_clean($wechat);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "修改成功";
        return $this->echoJson($response, $res);
    }


    public function updateSSR($request, $response, $args)
    {
        $protocol = $request->getParam('protocol');
        $obfs = $request->getParam('obfs');

        $user = $this->user;

        if ($obfs == "" || $protocol == "") {
            $res['ret'] = 0;
            $res['msg'] = "请填好";
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('obfs', $obfs)) {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('protocol', $protocol)) {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        $antiXss = new AntiXSS();

        $user->protocol = $antiXss->xss_clean($protocol);
        $user->obfs = $antiXss->xss_clean($obfs);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "修改成功";
        return $this->echoJson($response, $res);
    }

    public function updateTheme($request, $response, $args)
    {
        $theme = $request->getParam('theme');

        $user = $this->user;

        if ($theme == "") {
            $res['ret'] = 0;
            $res['msg'] = "???";
            return $response->getBody()->write(json_encode($res));
        }


        $user->theme = filter_var($theme, FILTER_SANITIZE_STRING);
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "ok";
        return $this->echoJson($response, $res);
    }


    public function updateMail($request, $response, $args)
    {
        $mail = $request->getParam('mail');

        $user = $this->user;

        if (!($mail == "1" || $mail == "0")) {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }


        $user->sendDailyMail = $mail;

        $user->save();
        $res['ret'] = 1;
        $res['msg'] = "ok";
        return $this->echoJson($response, $res);
    }

    public function PacSet($request, $response, $args)
    {
        $pac = $request->getParam('pac');

        $user = $this->user;

        if ($pac == "") {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }


        $user->pac = $pac;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "ok";
        return $this->echoJson($response, $res);
    }


    public function updateSsPwd($request, $response, $args)
    {
        $user = Auth::getUser();
        $pwd = $request->getParam('sspwd');

        if ($pwd == "") {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_validate($pwd)) {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        $user->updateSsPwd($pwd);
        $res['ret'] = 1;


        Radius::Add($user, $pwd);


        return $this->echoJson($response, $res);
    }

    public function updateMethod($request, $response, $args)
    {
        $user = Auth::getUser();
        $method = $request->getParam('method');
        $method = strtolower($method);

        if ($method == "") {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        if (!Tools::is_param_validate('method', $method)) {
            $res['ret'] = 0;
            $res['msg'] = "悟空别闹";
            return $response->getBody()->write(json_encode($res));
        }

        $user->updateMethod($method);
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function logout($request, $response, $args)
    {
        Auth::logout();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/auth/login');
        return $newResponse;
    }

    public function doCheckIn($request, $response, $args)
    {
        if (Config::get('enable_geetest_checkin') == 'true') {
            $ret = Geetest::verify($request->getParam('geetest_challenge'), $request->getParam('geetest_validate'), $request->getParam('geetest_seccode'));
            if (!$ret) {
                $res['ret'] = 0;
                $res['msg'] = "系统无法接受您的验证结果，请刷新页面后重试。";
                return $response->getBody()->write(json_encode($res));
            }
        }

        if (!$this->user->isAbleToCheckin()) {
            $res['msg'] = "您似乎已经续命过了...";
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        }
        $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
        $this->user->transfer_enable = $this->user->transfer_enable + Tools::toMB($traffic);
        $this->user->last_check_in_time = time();
        $this->user->save();
        $res['msg'] = sprintf("获得了 %u MB流量.", $traffic);
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function kill($request, $response, $args)
    {
        return $this->view()->display('user/kill.tpl');
    }

    public function handleKill($request, $response, $args)
    {
        $user = Auth::getUser();

        $email = $user->email;

        $passwd = $request->getParam('passwd');
        // check passwd
        $res = array();
        if (!Hash::checkPassword($user->pass, $passwd)) {
            $res['ret'] = 0;
            $res['msg'] = " 密码错误";
            return $this->echoJson($response, $res);
        }

        Auth::logout();
        $user->kill_user();
        $res['ret'] = 1;
        $res['msg'] = "GG!您的帐号已经从我们的系统中删除.";
        return $this->echoJson($response, $res);
    }

    public function trafficLog($request, $response, $args)
    {
        $traffic = TrafficLog::where('user_id', $this->user->id)->where("log_time", ">", (time() - 3 * 86400))->orderBy('id', 'desc')->get();
        return $this->view()->assign('logs', $traffic)->display('user/trafficlog.tpl');
    }


    public function detect_index($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $logs = DetectRule::paginate(15, ['*'], 'page', $pageNum);
        $logs->setPath('/user/detect');
        return $this->view()->assign('rules', $logs)->display('user/detect_index.tpl');
    }

    public function detect_log($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $logs = DetectLog::orderBy('id', 'desc')->where('user_id', $this->user->id)->paginate(15, ['*'], 'page', $pageNum);
        $logs->setPath('/user/detect/log');
        return $this->view()->assign('logs', $logs)->display('user/detect_log.tpl');
    }

    public function disable($request, $response, $args)
    {
        return $this->view()->display('user/disable.tpl');
    }

    public function telegram_reset($request, $response, $args)
    {
        $user = $this->user;
        $user->telegram_id = 0;
        $user->save();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/user/edit');
        return $newResponse;
    }
}